<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Talla;
use App\Models\ProductoTalla;
use App\Models\Variante;
use App\Models\Caracteristica;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Transaccion;
use App\Models\Empleado;
use App\Models\Diseno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

class PedidoController extends Controller
{
    /**
     * Mostrar catálogo de productos para clientes
     */
    public function catalogo()
    {
        $productos = Producto::with([
            'categoria',
            'variante.varianteCaracteristicas.caracteristica.opcion',
            'diseno'
        ])
            ->leftJoin('categorias', 'productos.idCategoria', '=', 'categorias.idCategoria')
            ->where('productos.estado', 1)
            ->orderBy('categorias.nombreCategoria')
            ->orderBy('productos.nombre')
            ->select('productos.*')
            ->get();

        $categorias = Categoria::where('estado', 1)
            ->orderBy('nombreCategoria')
            ->get();

        return view('pedidos.catalogo', compact('productos', 'categorias'));
    }

    /**
     * API: Precios por talla para un producto
     */
    public function apiTallasPreciosPorProducto($idProducto)
    {
        $producto = Producto::findOrFail($idProducto);

        $tallas = Talla::where('estado', 1)
            ->orderBy('nombre')
            ->get(['idTalla', 'nombre']);

        $pt = ProductoTalla::where('idProducto', $idProducto)
            ->get()
            ->keyBy('idTalla');

        $base = (float) ($producto->precioVenta ?? 0);

        $resp = $tallas->map(function ($t) use ($pt, $base) {
            $row = $pt->get($t->idTalla);
            $precioAdicional = (float) ($row->precioAdicional ?? 0);
            return [
                'idTalla' => $t->idTalla,
                'nombreTalla' => $t->nombre,
                'precioBase' => $base,
                'precioAdicional' => $precioAdicional,
                'precioUnitario' => $base + $precioAdicional,
            ];
        })->values();

        return response()->json([
            'idProducto' => $producto->idProducto,
            'precios' => $resp,
        ]);
    }

    /**
     * API: Variantes disponibles para un producto
     */
    public function apiVariantesPorProducto($idProducto)
    {
        $producto = Producto::with(['variante'])
            ->where('idProducto', $idProducto)
            ->firstOrFail();

        $variantes = Variante::where('idProducto', $idProducto)
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get(['idVariante', 'nombre']);

        return response()->json([
            'producto' => ['idProducto' => $producto->idProducto, 'nombre' => $producto->nombre],
            'variantes' => $variantes,
        ]);
    }

    /**
     * API: Opciones de personalización por producto con sus características activas
     */
    public function apiOpcionesPorProducto($idProducto)
    {
        $producto = Producto::with(['opciones.caracteristicas' => function ($q) {
            $q->where('estado', 1)->orderBy('nombre');
        }])->where('idProducto', $idProducto)->firstOrFail();

        $opciones = $producto->opciones()
            ->where('opcions.estado', 1)
            ->orderBy('opcions.nombre')
            ->get()
            ->map(function ($op) {
                return [
                    'idOpcion' => $op->idOpcion,
                    'nombreOpcion' => $op->nombre,
                    'caracteristicas' => $op->caracteristicas
                        ->where('estado', 1)
                        ->sortBy('nombre')
                        ->values()
                        ->map(function ($c) {
                            return [
                                'idCaracteristica' => $c->idCaracteristica,
                                'nombre' => $c->nombre,
                            ];
                        })->all(),
                ];
            });

        return response()->json([
            'producto' => [
                'idProducto' => $producto->idProducto,
                'nombre' => $producto->nombre,
            ],
            'opciones' => $opciones,
        ]);
    }

    /**
     * API: Características agrupadas por opción para una variante
     */
    public function apiCaracteristicasDeVariante($idVariante)
    {
        $variante = Variante::with(['varianteCaracteristicas.caracteristica.opcion'])
            ->findOrFail($idVariante);

        $grupo = [];
        foreach ($variante->varianteCaracteristicas as $vc) {
            $car = $vc->caracteristica;
            if (!$car) {
                continue;
            }
            $op = $car->opcion;
            $opKey = $op ? ($op->idOpcion . '|' . $op->nombre) : ('otros|Otros');
            if (!isset($grupo[$opKey])) {
                $grupo[$opKey] = [
                    'idOpcion' => $op->idOpcion ?? null,
                    'nombreOpcion' => $op->nombre ?? 'Otros',
                    'caracteristicas' => []
                ];
            }
            $grupo[$opKey]['caracteristicas'][] = [
                'idCaracteristica' => $car->idCaracteristica,
                'nombre' => $car->nombre,
            ];
        }

        $resultado = array_values($grupo);

        return response()->json([
            'idVariante' => $variante->idVariante,
            'nombreVariante' => $variante->nombre,
            'opciones' => $resultado,
        ]);
    }

    /**
     * Configurar producto con opciones de personalización
     */
    public function configurarProducto($idProducto)
    {
        try {
            $producto = Producto::with([
                'opciones.caracteristicas' => function ($query) {
                    $query->where('estado', 1);
                },
                'diseno'
            ])->where('estado', 1)->findOrFail($idProducto);

            $tallas = Talla::where('estado', 1)
                ->orderBy('nombre')
                ->get();

            $diseñadores = Empleado::with('user')
                ->whereHas('user', function ($q) {
                    $q->where('rol', 'diseñador')->where('estado', 1);
                })
                ->where('estado', 1)
                ->get();

            $productos = Producto::where('estado', 1)->get();
            $clientesNaturales = ClienteNatural::with(['user' => function ($query) {
                $query->where('estado', 1);
            }])->where('estado', 1)->get();
            
            // Orden personalizado para tallas
            $ordenPersonalizado = [
                '3XL', '2XL', 'XL', 'L', 'M', 'S', 'XS', 
                '14', '12', '10', '8', '6', '4', '2',
                '2XLD', 'XLD', 'LD', 'MD', 'SD', '14D'
            ];

            $tallas = $tallas->sortBy(function ($talla) use ($ordenPersonalizado) {
                $index = array_search($talla->nombre, $ordenPersonalizado);
                return $index === false ? 999 : $index;
            });

            $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->get();
            $configuracion = session('configuracion_pedido', []);

            return view('pedidos.configurar', compact(
                'producto',
                'tallas',
                'diseñadores',
                'productos',
                'clientesNaturales',
                'clientesEstablecimientos',
                'configuracion'
            ));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'Producto no encontrado o no disponible.');
        }
    }

    /**
     * Guardar configuración temporal del pedido
     */
    public function guardarConfiguracion(Request $request)
    {
        try {
            $validated = $request->validate([
                'idProducto' => 'required|exists:productos,idProducto',
                'items' => 'required|array|min:1',
                'items.*.idTalla' => 'required|exists:tallas,idTalla',
                'items.*.nombre' => 'nullable|string|max:100',
                'items.*.numero' => 'nullable|integer|min:0|max:999',
                'items.*.observaciones' => 'nullable|string|max:255',
                'fechaEntrega' => 'required|date|after:today',
                'lugarEntrega' => 'required|string|max:255',
                'idEmpleado' => 'required|exists:empleados,idEmpleado'
            ]);

            session([
                'configuracion_pedido' => [
                    'idProducto' => $request->idProducto,
                    'items' => $request->items,
                    'fechaEntrega' => $request->fechaEntrega,
                    'lugarEntrega' => $request->lugarEntrega,
                    'idEmpleado' => $request->idEmpleado,
                    'timestamp' => now()
                ]
            ]);

            return redirect()->route('pedidos.nuevo')
                ->with('success', 'Configuración guardada correctamente. Ahora completa la información del cliente.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }

    /**
     * Página para "Personalizar mi diseño"
     */
    public function personalizarDiseno(Request $request)
    {
        $ventaParam = $request->query('venta');
        if ($ventaParam) {
            $ventaExiste = Venta::where('idVenta', $ventaParam)->exists();
            if ($ventaExiste) {
                session()->put('ventaDestino', (int) $ventaParam);
            }
        }

        $productosBase = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->get(['idProducto', 'nombre']);

        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get();

        return view('pedidos.personalizar', compact('productosBase', 'tallas'));
    }

    /**
     * Guardar temporalmente el diseño
     */
    public function iniciarPedidoConDiseno(Request $request)
    {
        $request->validate([
            'disenoPersonalizado' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $ruta = $request->file('disenoPersonalizado')->store('disenos_personalizados', 'public');
        session()->put('disenoTemporal', $ruta);

        return redirect()->route('pedidos.nuevo')
            ->with('success', 'Diseño cargado. Completa los datos de tu pedido.');
    }

    /**
     * Mostrar formulario "Nuevo Pedido"
     */
    public function nuevoPedido()
    {
        $user = auth()->user();

        $esAdministrador = optional($user->empleado)->rol === 'administrador';
        $tieneDiseno = session()->has('disenoTemporal');

        if (!$tieneDiseno && !$esAdministrador) {
            return redirect()->route('pedidos.personalizar')
                ->with('error', 'Primero sube tu diseño.');
        }

        $productos = Producto::where('estado', 1)
            ->whereBetween('idProducto', [1, 4])
            ->orderBy('idProducto')
            ->get();

        $tallas = Talla::where('estado', 1)->orderBy('idTalla')->get();
        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::with('representante')->where('estado', 1)->get();

        $diseñadores = Empleado::with('user')
            ->where('rol', 'diseñador')
            ->where('estado', 1)
            ->get();

        if ($esAdministrador && !$tieneDiseno) {
            session()->flash('info', 'Modo administrador: Puedes crear pedidos sin diseño. Los clientes normales necesitarán subir un diseño.');
        }

        return view('pedidos.nuevo', compact('productos', 'tallas', 'clientesNaturales', 'clientesEstablecimientos', 'diseñadores'));
    }

    /**
     * Guardar pedido desde formulario con diseño temporal
     */
    public function guardarNuevoPedido(Request $request)
    {
        if (!session()->has('disenoTemporal')) {
            return redirect()->route('pedidos.personalizar')
                ->with('error', 'No se encontró el diseño subido.');
        }

        $disenadorId = $request->input('idEmpleado');

        if ($disenadorId && !Empleado::where('idEmpleado', $disenadorId)->where('rol', 'diseñador')->where('estado', 1)->exists()) {
            return back()->with('error', 'El diseñador seleccionado no es válido.')->withInput();
        }

        $producto = Producto::findOrFail($request->idProducto);
        $rutaDiseno = session()->get('disenoTemporal');

        $idsTalla = $request->input('idTalla', []);
        $cantidades = $request->input('cantidad', []);
        $nombres = $request->input('nombrePersonalizado', []);
        $numeros = $request->input('numeroPersonalizado', []);
        $observs = $request->input('observaciones', []);

        DB::beginTransaction();
        try {
            $subtotal = 0.0;
            $itemsCalculados = [];

            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar la venta. Cree un empleado o asocie uno al usuario actual.');
            }

            foreach ($idsTalla as $i => $idTalla) {
                $cant = (int)($cantidades[$i] ?? 0);
                if ($cant <= 0) continue;

                $precioAdicional = (float) (ProductoTalla::where('idProducto', $producto->idProducto)
                    ->where('idTalla', $idTalla)
                    ->value('precioAdicional') ?? 0);

                $precioUnit = (float) ($producto->precioVenta ?? 0) + $precioAdicional;
                $sub = $precioUnit * $cant;
                $subtotal += $sub;

                $itemsCalculados[] = [
                    'idTalla' => $idTalla,
                    'cantidad' => $cant,
                    'precioUnitario' => $precioUnit,
                    'subtotal' => $sub,
                    'nombre' => $nombres[$i] ?? null,
                    'numero' => $numeros[$i] ?? null,
                    'observacion' => $observs[$i] ?? null,
                ];
            }

            if (empty($itemsCalculados)) {
                throw new \Exception('Debes agregar al menos una fila válida (talla y cantidad).');
            }

            $ventaDestinoId = session()->get('ventaDestino');
            if ($ventaDestinoId) {
                $venta = Venta::with('transacciones')->findOrFail($ventaDestinoId);
            } else {
                $total = $subtotal;
                $venta = Venta::create([
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'fechaEntrega' => $request->fechaEntrega,
                    'lugarEntrega' => $request->lugarEntrega,
                    'estadoPedido' => '0',
                    'saldo' => $total,
                    'estado' => 1,
                    'idEmpleado' => $idEmpleadoSeguro,
                    'idCliente' => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                    'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
                ]);
            }

            $primerDetalle = null;

            foreach ($itemsCalculados as $it) {
                $detalle = DetalleVenta::create([
                    'cantidad' => $it['cantidad'],
                    'nombrePersonalizado' => $it['nombre'],
                    'numeroPersonalizado' => $it['numero'],
                    'textoAdicional' => null,
                    'observacion' => $it['observacion'],
                    'precioUnitario' => $it['precioUnitario'],
                    'estado' => 1,
                    'idTalla' => $it['idTalla'],
                    'idVenta' => $venta->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ]);

                if (!$primerDetalle) {
                    $primerDetalle = $detalle;
                }
            }

            if ($rutaDiseno && $primerDetalle) {
                Diseno::create([
                    'archivo' => $rutaDiseno,
                    'iddetalleVenta' => $primerDetalle->iddetalleVenta,
                    'estado' => 1,
                    'idEmpleado' => $disenadorId,
                ]);
            }

            if ($ventaDestinoId) {
                $nuevoSubtotal = DetalleVenta::where('idVenta', $venta->idVenta)
                    ->selectRaw('COALESCE(SUM(cantidad * precioUnitario),0) as s')
                    ->value('s');
                $venta->subtotal = $nuevoSubtotal;
                $venta->total = $nuevoSubtotal;

                $pagos = $venta->transacciones
                    ->where('tipoTransaccion', 'pago')
                    ->sum('monto');
                $venta->saldo = max($venta->total - (float) $pagos, 0);
                $venta->save();
            } else {
                $montoAdelanto = (float) ($request->montoAdelanto ?? 0);
                if ($montoAdelanto > 0) {
                    if ($montoAdelanto > $venta->total) {
                        throw new \Exception('El adelanto no puede ser mayor que el total.');
                    }
                    Transaccion::create([
                        'tipoTransaccion' => 'pago',
                        'monto' => $montoAdelanto,
                        'metodoPago' => $request->tipoTransaccion ?? 'efectivo',
                        'observaciones' => null,
                        'estado' => 1,
                        'idVenta' => $venta->idVenta,
                    ]);
                    $venta->saldo = max($venta->total - $montoAdelanto, 0);
                    $venta->save();
                }
            }

            DB::commit();

            session()->forget('disenoTemporal');
            if ($ventaDestinoId) session()->forget('ventaDestino');

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar nuevo pedido', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al guardar el pedido: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Guardar pedido directamente desde catálogo (sin diseño)
     */
    public function guardarDesdeCatalogo(Request $request)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProducto',
            'fechaEntrega' => 'required|date|after:today',
            'lugarEntrega' => 'required|string|max:255',
            'idEmpleado' => 'required|exists:empleados,idEmpleado',
            'tipoCliente' => 'required|in:natural,establecimiento',
            'items' => 'required|array|min:1',
            'items.*.idTalla' => 'required|exists:tallas,idTalla',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.nombre' => 'nullable|string|max:100',
            'items.*.numero' => 'nullable|integer|min:0|max:999',
            'items.*.observaciones' => 'nullable|string|max:255',
            'caracteristicas' => 'nullable|array',
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $producto = Producto::findOrFail($request->idProducto);

            $idEmpleadoSeguro = optional(auth()->user()->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                throw new \Exception('No existe ningún empleado registrado para asociar la venta.');
            }

            $subtotal = 0;
            $itemsCalculados = [];

            foreach ($request->items as $item) {
                $idTalla = $item['idTalla'];
                $cantidad = (int)$item['cantidad'];

                if ($cantidad <= 0) continue;

                $precioAdicional = (float) ProductoTalla::where('idProducto', $producto->idProducto)
                    ->where('idTalla', $idTalla)
                    ->value('precioAdicional') ?? 0;

                $precioUnit = (float)($producto->precioVenta ?? 0) + $precioAdicional;
                $sub = $precioUnit * $cantidad;
                $subtotal += $sub;

                $itemsCalculados[] = [
                    'idTalla' => $idTalla,
                    'cantidad' => $cantidad,
                    'precioUnitario' => $precioUnit,
                    'subtotal' => $sub,
                    'nombre' => $item['nombre'] ?? null,
                    'numero' => $item['numero'] ?? null,
                    'observacion' => $item['observaciones'] ?? null,
                ];
            }

            if (empty($itemsCalculados)) {
                throw new \Exception('Debes agregar al menos una prenda válida.');
            }

            $total = $subtotal;
            $venta = Venta::create([
                'subtotal' => $subtotal,
                'total' => $total,
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0',
                'saldo' => $total,
                'estado' => 1,
                'idEmpleado' => $idEmpleadoSeguro,
                'idCliente' => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
            ]);

            $primerDetalle = null;
            $caracteristicasSeleccionadas = $request->input('caracteristicas', []);

            foreach ($itemsCalculados as $item) {
                $detalle = DetalleVenta::create([
                    'cantidad' => $item['cantidad'],
                    'nombrePersonalizado' => $item['nombre'],
                    'numeroPersonalizado' => $item['numero'],
                    'textoAdicional' => null,
                    'observacion' => $item['observacion'],
                    'precioUnitario' => $item['precioUnitario'],
                    'estado' => 1,
                    'idTalla' => $item['idTalla'],
                    'idVenta' => $venta->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ]);

                if (!empty($caracteristicasSeleccionadas) && !$primerDetalle) {
                    foreach ($caracteristicasSeleccionadas as $idOpcion => $idCaracteristica) {
                        if ($idCaracteristica) {
                            DB::table('variante_caracteristicas')->insert([
                                'iddetalleVenta' => $detalle->iddetalleVenta,
                                'idCaracteristica' => $idCaracteristica,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                if (!$primerDetalle) {
                    $primerDetalle = $detalle;
                }
            }

            $montoAdelanto = (float)($request->montoAdelanto ?? 0);
            if ($montoAdelanto > 0) {
                if ($montoAdelanto > $venta->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }

                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto' => $montoAdelanto,
                    'metodoPago' => $request->tipoTransaccion ?? 'efectivo',
                    'observaciones' => null,
                    'estado' => 1,
                    'idVenta' => $venta->idVenta,
                ]);

                $venta->saldo = max($venta->total - $montoAdelanto, 0);
                $venta->save();
            }

            DB::commit();

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido creado exitosamente desde el catálogo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar pedido desde catálogo', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Error al guardar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Agregar producto configurado al carrito
     */
    public function agregarAlCarrito(Request $request)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProducto',
            'idTalla' => 'required|exists:tallas,idTalla',
            'cantidad' => 'required|integer|min:1',
            'caracteristicas' => 'array',
            'nombrePersonalizado' => 'nullable|string|max:50',
            'numeroPersonalizado' => 'nullable|string|max:10',
            'textoAdicional' => 'nullable|string|max:200',
            'disenoPersonalizado' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $producto = Producto::findOrFail($request->idProducto);
        $talla = Talla::findOrFail($request->idTalla);

        $rutaDisenoPersonalizado = null;
        if ($request->hasFile('disenoPersonalizado')) {
            $rutaDisenoPersonalizado = $request->file('disenoPersonalizado')
                ->store('disenos_personalizados', 'public');
        } elseif (session()->has('disenoTemporal')) {
            $rutaDisenoPersonalizado = session()->get('disenoTemporal');
        }

        $itemCarrito = [
            'id' => uniqid(),
            'idProducto' => $producto->idProducto,
            'nombreProducto' => $producto->nombre,
            'idTalla' => $talla->idTalla,
            'nombreTalla' => $talla->nombre,
            'cantidad' => $request->cantidad,
            'precioUnitario' => $producto->precioVenta,
            'subtotal' => $producto->precioVenta * $request->cantidad,
            'caracteristicas' => $request->caracteristicas ?? [],
            'nombrePersonalizado' => $request->nombrePersonalizado,
            'numeroPersonalizado' => $request->numeroPersonalizado,
            'textoAdicional' => $request->textoAdicional,
            'disenoPersonalizado' => $rutaDisenoPersonalizado,
            'fotoProducto' => $producto->foto,
            'archivoDiseno' => $producto->diseno->archivo ?? null
        ];

        $carrito = session()->get('carrito', []);
        $carrito[] = $itemCarrito;
        session()->put('carrito', $carrito);

        if (session()->has('disenoTemporal')) {
            session()->forget('disenoTemporal');
        }

        return redirect()->route('pedidos.carrito')
            ->with('success', 'Producto agregado al carrito exitosamente');
    }

    /**
     * Mostrar carrito de compras
     */
    public function carrito()
    {
        $carrito = session()->get('carrito', []);
        $total = collect($carrito)->sum('subtotal');

        return view('pedidos.carrito', compact('carrito', 'total'));
    }

    /**
     * Eliminar item del carrito
     */
    public function eliminarDelCarrito($itemId)
    {
        $carrito = session()->get('carrito', []);
        $carrito = collect($carrito)->reject(function ($item) use ($itemId) {
            return $item['id'] === $itemId;
        })->values()->toArray();

        session()->put('carrito', $carrito);

        return redirect()->route('pedidos.carrito')
            ->with('success', 'Producto eliminado del carrito');
    }

    /**
     * Mostrar formulario de checkout
     */
    public function checkout()
    {
        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'El carrito está vacío');
        }

        $total = collect($carrito)->sum('subtotal');

        $clientesNaturales = ClienteNatural::where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->get();

        return view('pedidos.checkout', compact('carrito', 'total', 'clientesNaturales', 'clientesEstablecimientos'));
    }

    /**
     * Procesar pedido final
     */
    public function procesarPedido(Request $request)
    {
        $request->validate([
            'tipoCliente' => 'required|in:natural,establecimiento',
            'idCliente' => 'required_if:tipoCliente,natural',
            'idEstablecimiento' => 'required_if:tipoCliente,establecimiento',
            'fechaEntrega' => 'required|date|after:today',
            'lugarEntrega' => 'required|string|max:200',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $carrito = session()->get('carrito', []);

        if (empty($carrito)) {
            return redirect()->route('pedidos.catalogo')
                ->with('error', 'El carrito está vacío');
        }

        DB::beginTransaction();

        try {
            $subtotal = collect($carrito)->sum('subtotal');

            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar la venta. Cree un empleado o asocie uno al usuario actual.');
            }

            $total = $subtotal;
            $venta = Venta::create([
                'subtotal' => $subtotal,
                'total' => $total,
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0',
                'saldo' => $total,
                'estado' => 1,
                'idEmpleado' => $idEmpleadoSeguro,
                'idCliente' => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
            ]);

            foreach ($carrito as $item) {
                DetalleVenta::create([
                    'cantidad' => $item['cantidad'],
                    'nombrePersonalizado' => $item['nombrePersonalizado'] ?? null,
                    'numeroPersonalizado' => $item['numeroPersonalizado'] ?? null,
                    'textoAdicional' => null,
                    'observacion' => $request->observaciones,
                    'precioUnitario' => $item['precioUnitario'],
                    'estado' => 1,
                    'idTalla' => $item['idTalla'],
                    'idVenta' => $venta->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ]);
            }

            DB::commit();

            session()->forget('carrito');

            Log::info('Pedido creado exitosamente', [
                'idVenta' => $venta->idVenta,
                'total' => $total,
                'items' => count($carrito)
            ]);

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pedido creado exitosamente');
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Error al procesar pedido', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error al procesar el pedido: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar confirmación de pedido
     */
    public function confirmacion($idVenta)
    {
        $venta = Venta::with([
            'detalleVentas.talla',
            'clienteNatural.user',
            'clienteEstablecimiento.representante',
            'transacciones',
            'empleado.user'
        ])->findOrFail($idVenta);

        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get(['idTalla', 'nombre']);

        $metodosPago = collect([
            ['id' => null, 'nombre' => 'Efectivo', 'codigo' => 'efectivo'],
            ['id' => null, 'nombre' => 'QR', 'codigo' => 'qr'],
            ['id' => null, 'nombre' => 'Cheque', 'codigo' => 'cheque'],
            ['id' => null, 'nombre' => 'Transferencia bancaria', 'codigo' => 'transferencia'],
        ]);

        return view('pedidos.confirmacion', compact('venta', 'metodosPago', 'tallas'));
    }

    /**
     * Agregar un detalle de venta desde la confirmación
     */
    public function agregarDetalle(Request $request, $idVenta)
    {
        $request->validate([
            'idTalla' => 'required|exists:tallas,idTalla',
            'cantidad' => 'required|integer|min:1',
            'precioUnitario' => 'required|numeric|min:0',
            'nombrePersonalizado' => 'nullable|string|max:50',
            'numeroPersonalizado' => 'nullable|string|max:10',
            'textoAdicional' => 'nullable|string|max:200',
            'observacion' => 'nullable|string|max:500',
            'descripcion' => 'nullable|string|max:200',
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $venta = Venta::with('transacciones')->findOrFail($idVenta);

            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar el detalle.');
            }

            $rawDelete = $request->input('delete_ids');
            $deleteIds = collect([]);
            if (is_array($rawDelete)) {
                $deleteIds = collect($rawDelete);
            } elseif (is_string($rawDelete) && trim($rawDelete) !== '') {
                $deleteIds = collect(explode(',', $rawDelete));
            }
            $deleteIds = $deleteIds->map(fn($v) => (int) $v)->filter();
            if ($deleteIds->isNotEmpty()) {
                DetalleVenta::where('idVenta', $venta->idVenta)
                    ->whereIn('iddetalleVenta', $deleteIds->all())
                    ->delete();
            }

            $ids = $request->input('row_id', []);
            $tallas = $request->input('idTalla', []);
            $cantidades = $request->input('cantidad', []);
            $precios = $request->input('precioUnitario', []);
            $nombres = $request->input('nombrePersonalizado', []);
            $numeros = $request->input('numeroPersonalizado', []);
            $observs = $request->input('observacion', []);
            $descrs = $request->input('descripcion', []);

            foreach ($ids as $i => $rowId) {
                $dataDet = [
                    'cantidad' => (int) ($cantidades[$i] ?? 0),
                    'nombrePersonalizado' => $nombres[$i] ?? null,
                    'numeroPersonalizado' => $numeros[$i] ?? null,
                    'textoAdicional' => null,
                    'observacion' => $observs[$i] ?? null,
                    'descripcion' => $descrs[$i] ?? null,
                    'precioUnitario' => (float) ($precios[$i] ?? 0),
                    'estado' => 1,
                    'idTalla' => (int) ($tallas[$i] ?? 0),
                    'idVenta' => $venta->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ];

                if ($dataDet['cantidad'] <= 0) {
                    continue;
                }

                if ($rowId) {
                    $det = DetalleVenta::where('idVenta', $venta->idVenta)
                        ->where('iddetalleVenta', $rowId)
                        ->first();
                    if ($det) {
                        $det->update($dataDet);
                    }
                } else {
                    DetalleVenta::create($dataDet);
                }
            }

            $nuevoSubtotal = DetalleVenta::where('idVenta', $venta->idVenta)
                ->selectRaw('COALESCE(SUM(cantidad * precioUnitario),0) as s')
                ->value('s');
            $venta->subtotal = $nuevoSubtotal;
            $venta->total = $nuevoSubtotal;

            $montoAd = (float) ($request->montoAdelanto ?? 0);
            $tipoPago = $request->tipoTransaccion;
            if ($montoAd > 0) {
                if ($montoAd > $venta->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }
                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto' => $montoAd,
                    'metodoPago' => $tipoPago ?? 'efectivo',
                    'observaciones' => null,
                    'estado' => 1,
                    'idVenta' => $venta->idVenta,
                ]);
            }

            $pagos = $venta->transacciones
                ->where('tipoTransaccion', 'pago')
                ->sum('monto');
            $venta->saldo = max($venta->total - (float) $pagos, 0);
            $venta->save();

            DB::commit();
            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Detalle agregado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al agregar detalle', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'No se pudo agregar el detalle: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar imagen del pedido vía AJAX
     */
    public function eliminarImagen($idVenta)
    {
        try {
            $pedido = Venta::findOrFail($idVenta);
            $diseno = $pedido->disenos->first();

            if ($diseno) {
                Storage::delete('public/' . $diseno->archivo);
                $diseno->delete();
                Log::info('Imagen eliminada vía AJAX', ['idVenta' => $idVenta]);
                return response()->json(['success' => true, 'message' => 'Imagen eliminada correctamente']);
            } else {
                return response()->json(['success' => false, 'message' => 'No se encontró imagen asociada'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error al eliminar imagen vía AJAX', ['idVenta' => $idVenta, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error al eliminar la imagen'], 500);
        }
    }

    /**
     * Listar todos los pedidos
     */
    public function index()
    {
        $pedidos = Venta::with([
            'clienteNatural',
            'clienteEstablecimiento',
            'empleado',
            'detalleVentas',
            'disenos.empleado.user'
        ])->where('estado', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $contador = ($pedidos->currentPage() - 1) * $pedidos->perPage() + 1;
        $pedidos->each(function ($pedido) use (&$contador) {
            $pedido->contador = $contador++;
        });

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Ver detalle de pedido
     */
    public function show($pedido)
    {
        $pedido = Venta::with([
            'detalleVentas.talla',
            'detalleVentas.diseno',
            'clienteNatural',
            'clienteEstablecimiento',
            'empleado'
        ])->findOrFail($pedido);

        $disenoUrl = null;
        if (session()->has('disenoTemporal')) {
            $disenoUrl = asset('storage/' . session()->get('disenoTemporal'));
        }

        return view('pedidos.show', compact('pedido', 'disenoUrl'));
    }

    /**
     * Editar pedido (datos básicos)
     */
    public function edit($idVenta)
    {
        $pedido = Venta::with(['clienteNatural', 'clienteEstablecimiento', 'detalleVentas.talla', 'detalleVentas.producto', 'disenos'])
            ->findOrFail($idVenta);

        $estados = [
            '0' => 'En Diseño',
            '1' => 'Producción',
            '2' => 'Terminado',
            '3' => 'Entregado',
            '4' => 'Cancelado',
        ];

        $diseñadores = Empleado::where('rol', 'diseñador')->where('estado', 1)->get();
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get(['idTalla', 'nombre']);
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get(['idProducto', 'nombre']);

        $metodosPago = collect([
            ['id' => null, 'nombre' => 'Efectivo', 'codigo' => 'efectivo'],
            ['id' => null, 'nombre' => 'QR', 'codigo' => 'qr'],
            ['id' => null, 'nombre' => 'Cheque', 'codigo' => 'cheque'],
            ['id' => null, 'nombre' => 'Transferencia bancaria', 'codigo' => 'transferencia'],
        ]);

        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::with('representante')->where('estado', 1)->get();
        $empleados = Empleado::with('user')->where('estado', 1)->get();

        return view('pedidos.edit', compact(
            'pedido',
            'estados',
            'tallas',
            'productos',
            'metodosPago',
            'clientesNaturales',
            'clientesEstablecimientos',
            'empleados',
            'diseñadores'
        ));
    }

    /**
     * Actualizar pedido
     */
    public function update(Request $request, $idVenta)
    {
        $request->validate([
            'fechaEntrega' => 'required|date',
            'lugarEntrega' => 'required|string|max:200',
            'estadoPedido' => 'required|in:0,1,2,3,4',
            'imagenPedido' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'tipoCliente' => 'nullable|string',
            'idEmpleado' => 'required|exists:empleados,idEmpleado',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $pedido = Venta::findOrFail($idVenta);
        $pedido->fill($request->all());

        if ($request->filled('tipoCliente')) {
            if (str_starts_with($request->tipoCliente, 'natural:')) {
                $pedido->idCliente = explode(':', $request->tipoCliente)[1];
                $pedido->idEstablecimiento = null;
            } elseif (str_starts_with($request->tipoCliente, 'establecimiento:')) {
                $pedido->idEstablecimiento = explode(':', $request->tipoCliente)[1];
                $pedido->idCliente = null;
            }
        }

        if ($request->filled('idEmpleado')) {
            $pedido->idEmpleado = $request->idEmpleado;
        }

        if ($request->hasFile('imagenPedido')) {
            $disenoAntiguo = $pedido->disenos->first();
            if ($disenoAntiguo) {
                Storage::delete('public/' . $disenoAntiguo->archivo);
                $disenoAntiguo->delete();
            }

            $imagen = $request->file('imagenPedido');
            $path = $imagen->store('imagenes_pedidos', 'public');

            $primerDetalle = DetalleVenta::where('idVenta', $pedido->idVenta)->first();
            if ($primerDetalle) {
                Diseno::create([
                    'archivo' => $path,
                    'iddetalleVenta' => $primerDetalle->iddetalleVenta,
                    'estado' => 1,
                ]);
                Log::info('Nueva imagen subida para pedido', ['idVenta' => $pedido->idVenta, 'archivo' => $path]);
            }
        } elseif ($request->boolean('delete_imagen')) {
            $diseno = $pedido->disenos->first();
            if ($diseno) {
                $archivoPath = 'public/' . $diseno->archivo;
                Storage::delete($archivoPath);
                $diseno->delete();
                Log::info('Imagen eliminada del pedido', ['idVenta' => $pedido->idVenta]);
            }
        }

        $pedido->save();

        return redirect()->route('pedidos.index')->with('success', 'Pedido actualizado correctamente.');
    }

    /**
     * Actualizar detalles del pedido
     */
    public function updateDetalles(Request $request, $idVenta)
    {
        $pedido = Venta::with('transacciones')->findOrFail($idVenta);

        $request->validate([
            'idProducto' => 'nullable|exists:productos,idProducto',
            'row_id' => 'required|array',
            'idTalla' => 'required|array|min:1',
            'idTalla.*' => 'required|exists:tallas,idTalla',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'precioUnitario' => 'required|array|min:1',
            'precioUnitario.*' => 'required|numeric|min:0',
            'nombrePersonalizado' => 'nullable|array',
            'numeroPersonalizado' => 'nullable|array',
            'observacion' => 'nullable|array',
            'descripcion' => 'nullable|array',
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $rawDelete = (string) $request->input('delete_ids', '');
            $deleteIds = collect(array_filter(array_map('trim', explode(',', $rawDelete))))
                ->map(fn($v) => (int) $v)
                ->filter();

            if ($deleteIds->isNotEmpty()) {
                DetalleVenta::where('idVenta', $pedido->idVenta)
                    ->whereIn('iddetalleVenta', $deleteIds->all())
                    ->delete();
            }

            $ids = $request->input('row_id', []);
            $tallas = $request->input('idTalla', []);
            $cantidades = $request->input('cantidad', []);
            $precios = $request->input('precioUnitario', []);
            $nombres = $request->input('nombrePersonalizado', []);
            $numeros = $request->input('numeroPersonalizado', []);
            $observs = $request->input('observacion', []);
            $descrs = $request->input('descripcion', []);

            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado
                ?? DB::table('empleados')->value('idEmpleado');

            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar los detalles.');
            }

            foreach ($ids as $i => $rowId) {
                $dataDet = [
                    'cantidad' => (int) ($cantidades[$i] ?? 0),
                    'nombrePersonalizado' => $nombres[$i] ?? null,
                    'numeroPersonalizado' => $numeros[$i] ?? null,
                    'textoAdicional' => null,
                    'observacion' => $observs[$i] ?? null,
                    'descripcion' => $descrs[$i] ?? null,
                    'precioUnitario' => (float) ($precios[$i] ?? 0),
                    'estado' => 1,
                    'idTalla' => (int) ($tallas[$i] ?? 0),
                    'idVenta' => $pedido->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
                ];

                if ($dataDet['cantidad'] <= 0) {
                    continue;
                }

                if (!empty($rowId)) {
                    $det = DetalleVenta::where('idVenta', $pedido->idVenta)
                        ->where('iddetalleVenta', $rowId)
                        ->first();
                    if ($det) {
                        $det->update($dataDet);
                    }
                } else {
                    DetalleVenta::create($dataDet);
                }
            }

            $nuevoSubtotal = DetalleVenta::where('idVenta', $pedido->idVenta)
                ->selectRaw('COALESCE(SUM(cantidad * precioUnitario),0) as s')
                ->value('s');

            $pedido->subtotal = $nuevoSubtotal;
            $pedido->total = $nuevoSubtotal;

            $montoAd = (float) ($request->montoAdelanto ?? 0);
            $tipoPago = $request->tipoTransaccion;
            if ($montoAd > 0) {
                if ($montoAd > $pedido->total) {
                    throw new \Exception('El adelanto no puede ser mayor que el total.');
                }
                Transaccion::create([
                    'tipoTransaccion' => 'pago',
                    'monto' => $montoAd,
                    'metodoPago' => $tipoPago ?? 'efectivo',
                    'observaciones' => null,
                    'estado' => 1,
                    'idVenta' => $pedido->idVenta,
                ]);
            }

            $pagosAcumulados = Transaccion::where('idVenta', $pedido->idVenta)
                ->where('tipoTransaccion', 'pago')
                ->sum('monto');

            $pedido->saldo = max($pedido->total - (float) $pagosAcumulados, 0);
            $pedido->save();

            DB::commit();

            return redirect()->route('pedidos.edit', $pedido->idVenta)
                ->with('success', 'Detalles actualizados correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar detalles', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'No se pudo actualizar los detalles: ' . $e->getMessage());
        }
    }

    /**
     * API: Búsqueda unificada de clientes
     */
    public function apiBuscarClientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['results' => []]);
        }

        $qLike = '%' . $q . '%';

        $naturales = ClienteNatural::query()
            ->where('cliente_naturals.estado', 1)
            ->leftJoin('users', 'cliente_naturals.idCliente', '=', 'users.idUser')
            ->where(function ($w) use ($qLike) {
                $w->where('users.ci', 'like', $qLike)
                    ->orWhere('users.name', 'like', $qLike)
                    ->orWhere('users.telefono', 'like', $qLike)
                    ->orWhere('cliente_naturals.nit', 'like', $qLike);
            })
            ->orderBy('users.name')
            ->limit(15)
            ->get([
                'cliente_naturals.idCliente',
                'cliente_naturals.nit',
                'users.ci',
                'users.name',
                'users.telefono',
            ])
            ->map(function ($row) {
                $doc = $row->ci ?: $row->nit;
                $label = trim(($doc ? 'CI: ' . $doc . ' - ' : '') . ($row->name ?: 'Cliente') . ($row->telefono ? ' - Tel: ' . $row->telefono : ''));
                return [
                    'type' => 'natural',
                    'value' => 'natural:' . $row->idCliente,
                    'label' => $label,
                ];
            });

        $establecimientos = ClienteEstablecimiento::query()
            ->where('cliente_establecimientos.estado', 1)
            ->leftJoin('users', 'cliente_establecimientos.idRepresentante', '=', 'users.idUser')
            ->where(function ($w) use ($qLike) {
                $w->where('cliente_establecimientos.nit', 'like', $qLike)
                    ->orWhere('cliente_establecimientos.razonSocial', 'like', $qLike)
                    ->orWhere('users.name', 'like', $qLike)
                    ->orWhere('users.telefono', 'like', $qLike);
            })
            ->orderBy('cliente_establecimientos.razonSocial')
            ->limit(15)
            ->get([
                'cliente_establecimientos.idEstablecimiento',
                'cliente_establecimientos.nit',
                'cliente_establecimientos.razonSocial',
                'users.telefono',
                'users.name as representante',
            ])
            ->map(function ($row) {
                $doc = $row->nit ?: '';
                $nom = $row->razonSocial ?: 'Establecimiento';
                $tel = $row->telefono ?: '';
                $label = trim(($doc ? 'NIT: ' . $doc . ' - ' : '') . $nom . ($tel ? ' - Tel: ' . $tel : ''));
                return [
                    'type' => 'establecimiento',
                    'value' => 'establecimiento:' . $row->idEstablecimiento,
                    'label' => $label,
                ];
            });

        $results = $naturales->concat($establecimientos)->values();

        return response()->json(['results' => $results]);
    }

    /**
     * Actualizar estado de pedido (AJAX)
     */
    public function actualizarEstado(Request $request, $idVenta)
    {
        try {
            $request->validate([
                'estadoPedido' => 'required|in:0,1,2,3,4'
            ]);

            $pedido = Venta::findOrFail($idVenta);
            $estadoAnterior = $pedido->estadoPedido;

            $pedido->estadoPedido = $request->estadoPedido;
            $pedido->save();

            Log::info('Estado de pedido actualizado', [
                'idVenta' => $idVenta,
                'estadoAnterior' => $estadoAnterior,
                'estadoNuevo' => $request->estadoPedido
            ]);

            $estados = [
                '0' => 'En Diseño',
                '1' => 'Producción',
                '2' => 'Terminado',
                '3' => 'Entregado',
                '4' => 'Cancelado'
            ];

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Estado actualizado correctamente',
                    'estadoNuevo' => $estados[$request->estadoPedido] ?? 'Desconocido',
                    'pedidoId' => $idVenta
                ]);
            }

            return redirect()->back()
                ->with('success', 'Estado del pedido actualizado exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de pedido', [
                'idVenta' => $idVenta,
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar el estado: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Error al actualizar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Registrar un pago para una venta
     */
    public function registrarPago(Request $request, $idVenta)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodoPago' => 'required|string|max:100',
            'observaciones' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $venta = Venta::where('idVenta', $idVenta)->lockForUpdate()->firstOrFail();

            $monto = (float) $request->monto;

            $pagosAcumulados = Transaccion::where('idVenta', $venta->idVenta)
                ->where('tipoTransaccion', 'pago')
                ->sum('monto');
            $saldoActual = max(0, ((float) $venta->total) - (float) $pagosAcumulados);

            if ($monto > $saldoActual) {
                DB::rollBack();
                return redirect()->back()->with('error', 'El monto del pago no puede superar el saldo pendiente.');
            }

            Transaccion::create([
                'tipoTransaccion' => 'pago',
                'monto' => $monto,
                'metodoPago' => $request->metodoPago,
                'observaciones' => $request->observaciones,
                'estado' => 1,
                'idVenta' => $venta->idVenta,
            ]);

            $nuevoSaldo = max($saldoActual - $monto, 0);
            $venta->saldo = $nuevoSaldo;
            $venta->save();

            DB::commit();
            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                ->with('success', 'Pago registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar pago', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al registrar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un pedido
     */
    public function destroy($idVenta)
    {
        DB::beginTransaction();
        try {
            $venta = Venta::findOrFail($idVenta);

            $venta->estado = 0;
            $venta->save();

            DB::commit();
            return redirect()->route('pedidos.index')->with('successdelete', 'Pedido eliminado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar (lógico) pedido', ['idVenta' => $idVenta, 'error' => $e->getMessage()]);
            return redirect()->route('pedidos.index')->with('error', 'No se pudo eliminar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar los pedidos asignados al diseñador actual
     */
    public function pedidosAsignados(Request $request)
    {
        $idEmpleado = auth()->user()->empleado->idEmpleado;

        $query = Venta::with(['clienteNatural', 'clienteEstablecimiento', 'detalles'])
            ->whereHas('disenos', function ($q) use ($idEmpleado) {
                $q->where('disenos.idEmpleado', $idEmpleado);
            });

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $pedidos = $query->latest()->paginate(10);

        return view('pedidos.asignados', compact('pedidos'));
    }

    /**
     * Crear nuevo pedido
     */
    public function create()
    {
        $diseñadores = Empleado::with('user')
            ->where('rol', 'diseñador')
            ->where('estado', 1)
            ->get();

        $productos = Producto::where('estado', 1)->get();
        $tallas = Talla::where('estado', 1)->get();
        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::with('representante')->where('estado', 1)->get();

        return view('pedidos.nuevo', compact(
            'productos',
            'tallas',
            'clientesNaturales',
            'clientesEstablecimientos',
            'diseñadores'
        ));
    }
}