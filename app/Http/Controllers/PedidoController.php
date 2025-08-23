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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
     * Retorna para cada talla activa el precio unitario final calculado como:
     * precioUnitario = producto.precioVenta + (producto_tallas.precioAdicional || 0)
     */
    public function apiTallasPreciosPorProducto($idProducto)
    {
        $producto = Producto::findOrFail($idProducto);

        // Todas las tallas activas del sistema
        $tallas = Talla::where('estado', 1)
            ->orderBy('nombre')
            ->get(['idTalla', 'nombre']);

        // Relación producto_tallas (puede no existir registro para alguna talla)
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

        // Estructura mínima para el front
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

        // Solo opciones activas asociadas al producto
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

        // Mapear a una estructura por opción
        $grupo = [];
        foreach ($variante->varianteCaracteristicas as $vc) {
            $car = $vc->caracteristica; // Caracteristica
            if (!$car) { continue; }
            $op = $car->opcion; // Opcion
            $opKey = $op ? ($op->idOpcion.'|'.$op->nombre) : ('otros|Otros');
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

        // Reindexar
        $resultado = array_values($grupo);

        return response()->json([
            'idVariante' => $variante->idVariante,
            'nombreVariante' => $variante->nombre,
            'opciones' => $resultado,
        ]);
    }

    /**
     * Configurador de producto - mostrar opciones de personalización
     */
    public function configurarProducto($idProducto)
    {
        $producto = Producto::with([
            'categoria',
            'variante.varianteCaracteristicas.caracteristica.opcion',
            'productoTallas.talla',
            'diseno'
        ])->findOrFail($idProducto);

        // Obtener todas las tallas disponibles para el producto
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get();

        return view('pedidos.configurar', compact('producto', 'tallas'));
    }

    /**
     * Página para "Personalizar mi diseño" (flujo B)
     * Permite elegir un producto base, talla, cantidad y subir archivo de diseño.
     */
    public function personalizarDiseno()
    {
        // Productos base activos. Puedes filtrar por una categoría específica si lo deseas.
        $productosBase = Producto::where('estado', 1)
                                ->orderBy('nombre')
                                ->get(['idProducto', 'nombre']);

        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get();

        return view('pedidos.personalizar', compact('productosBase', 'tallas'));
    }

    /**()
     * Guardar temporalmente el diseño y avanzar al flujo de pedido.
     * No solicita talla/nombre/número todavía.
     */
    public function iniciarPedidoConDiseno(Request $request)
    {
        $request->validate([
            'disenoPersonalizado' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        // Guardar archivo en storage y ruta en sesión
        $ruta = $request->file('disenoPersonalizado')->store('disenos_personalizados', 'public');
        session()->put('disenoTemporal', $ruta);

        // Redirigir al formulario de nuevo pedido (no se vuelve a pedir imagen)
        return redirect()->route('pedidos.nuevo')
                         ->with('success', 'Diseño cargado. Completa los datos de tu pedido.');
    }

    /**
     * Mostrar formulario "Nuevo Pedido" usando diseño temporal ya subido.
     */
    public function nuevoPedido()
    {
        if (!session()->has('disenoTemporal')) {
            return redirect()->route('pedidos.personalizar')
                             ->with('error', 'Primero sube tu diseño.');
        }

        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get();

        $clientesNaturales = ClienteNatural::where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->get();

        // Sin depender de la BD para métodos de pago
        return view('pedidos.nuevo', compact('productos', 'tallas', 'clientesNaturales', 'clientesEstablecimientos'));
    }

    /**
     * Guardar pedido desde formulario único usando diseño temporal.
     */
    public function guardarNuevoPedido(Request $request)
    {
        if (!session()->has('disenoTemporal')) {
            return redirect()->route('pedidos.personalizar')
                             ->with('error', 'No se encontró el diseño subido.');
        }

        $request->validate([
            'tipoCliente' => 'required|in:natural,establecimiento',
            'idCliente' => 'required_if:tipoCliente,natural',
            'idEstablecimiento' => 'required_if:tipoCliente,establecimiento',
            'fechaEntrega' => 'required|date|after:today',
            'lugarEntrega' => 'required|string|max:200',
            'idProducto' => 'required|exists:productos,idProducto',
            'idTalla' => 'required|exists:tallas,idTalla',
            'cantidad' => 'required|integer|min:1',
            'nombrePersonalizado' => 'nullable|string|max:50',
            'numeroPersonalizado' => 'nullable|string|max:10',
            'textoAdicional' => 'nullable|string|max:200',
            // Pago
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'pagoInicial' => 'nullable|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($request->idProducto);
        $talla = Talla::findOrFail($request->idTalla);
        $rutaDiseno = session()->get('disenoTemporal');

        DB::beginTransaction();
        try {
            $precioUnitario = $producto->precioVenta;
            $subtotal = $precioUnitario * $request->cantidad;

            $venta = Venta::create([
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0',
                'saldo' => $subtotal,
                'estado' => 1,
                'idEmpleado' => auth()->user()->empleado->idEmpleado ?? 1,
                'idCliente' => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
                'idUser' => auth()->id()
            ]);

            DetalleVenta::create([
                'cantidad' => $request->cantidad,
                'nombrePersonalizado' => $request->nombrePersonalizado,
                'numeroPersonalizado' => $request->numeroPersonalizado,
                'textoAdicional' => $request->textoAdicional,
                'observacion' => $request->observaciones,
                'precioUnitario' => $precioUnitario,
                'subtotal' => $subtotal,
                'estado' => 1,
                'idTalla' => $talla->idTalla,
                'idVenta' => $venta->idVenta,
                'idProducto' => $producto->idProducto,
                'idUser' => auth()->id()
            ]);

            // Registrar transacción de pago inicial si corresponde (Opción B)
            $pagoInicial = (float) ($request->pagoInicial ?? 0);
            if ($pagoInicial > 0) {
                if ($pagoInicial > $subtotal) {
                    throw new \Exception('El pago inicial no puede ser mayor que el total.');
                }
                Transaccion::create([
                    'tipoTransaccion' => $request->tipoTransaccion ?? 'efectivo',
                    'monto' => $pagoInicial,
                    'metodoPago' => $request->tipoTransaccion ?? 'efectivo',
                    'observaciones' => $request->observaciones,
                    'estado' => 1,
                    'idVenta' => $venta->idVenta,
                    'idUser' => auth()->id(),
                ]);

                // Actualizar saldo de la venta
                $venta->saldo = max($subtotal - $pagoInicial, 0);
                $venta->save();
            }

            DB::commit();

            // Limpiar diseño temporal tras guardar
            session()->forget('disenoTemporal');

            return redirect()->route('pedidos.confirmacion', $venta->idVenta)
                             ->with('success', 'Pedido creado exitosamente');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar nuevo pedido', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Error al guardar el pedido: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Agregar producto configurado al carrito (sesión)
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

        // Procesar diseño personalizado si se subió
        $rutaDisenoPersonalizado = null;
        if ($request->hasFile('disenoPersonalizado')) {
            $rutaDisenoPersonalizado = $request->file('disenoPersonalizado')
                ->store('disenos_personalizados', 'public');
        } elseif (session()->has('disenoTemporal')) {
            // Usar diseño subido previamente en el paso "Personalizar mi diseño"
            $rutaDisenoPersonalizado = session()->get('disenoTemporal');
        }

        // Crear item del carrito
        $itemCarrito = [
            'id' => uniqid(), // ID único para el item
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

        // Agregar al carrito en sesión
        $carrito = session()->get('carrito', []);
        $carrito[] = $itemCarrito;
        session()->put('carrito', $carrito);

        // Limpiar diseño temporal si se utilizó
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
        
        // Obtener clientes para el formulario
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
            $total = collect($carrito)->sum('subtotal');

            // Crear la venta/pedido
            $venta = Venta::create([
                'subtotal' => $total,
                'total' => $total,
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0', // Solicitado
                'saldo' => $total,
                'estado' => 1,
                'idEmpleado' => auth()->user()->empleado->idEmpleado ?? 1, // Usuario actual o default
                'idCliente' => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
                'idUser' => auth()->id()
            ]);

            // Crear detalles de venta para cada item del carrito
            foreach ($carrito as $item) {
                DetalleVenta::create([
                    'cantidad' => $item['cantidad'],
                    'nombrePersonalizado' => $item['nombrePersonalizado'],
                    'numeroPersonalizado' => $item['numeroPersonalizado'],
                    'textoAdicional' => $item['textoAdicional'],
                    'observacion' => $request->observaciones,
                    'precioUnitario' => $item['precioUnitario'],
                    'subtotal' => $item['subtotal'],
                    'estado' => 1,
                    'idTalla' => $item['idTalla'],
                    'idVenta' => $venta->idVenta,
                    'idProducto' => $item['idProducto'],
                    'idUser' => auth()->id()
                ]);
            }

            DB::commit();

            // Limpiar carrito
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
            'detalleVentas.producto',
            'detalleVentas.talla',
            'clienteNatural',
            'clienteEstablecimiento'
        ])->findOrFail($idVenta);

        return view('pedidos.confirmacion', compact('venta'));
    }

    /**
     * Listar pedidos (para administración)
     */
    public function index()
    {
        $pedidos = Venta::with([
            'clienteNatural',
            'clienteEstablecimiento',
            'empleado',
            'detalleVentas'
        ])->orderBy('created_at', 'desc')
          ->paginate(20);

        return view('pedidos.index', compact('pedidos'));
    }

    /**
     * Ver detalle de pedido
     */
    public function show($idVenta)
    {
        $pedido = Venta::with([
            'detalleVentas.producto.categoria',
            'detalleVentas.talla',
            'clienteNatural',
            'clienteEstablecimiento',
            'empleado'
        ])->findOrFail($idVenta);

        return view('pedidos.show', compact('pedido'));
    }

    /**
     * API: Búsqueda unificada de clientes por CI/NIT, nombre y teléfono
     */
    public function apiBuscarClientes(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['results' => []]);
        }

        $qLike = '%'.$q.'%';

        // Clientes naturales (join con users)
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
                $label = trim(($doc ? 'CI: '.$doc.' - ' : '').($row->name ?: 'Cliente').($row->telefono ? ' - Tel: '.$row->telefono : ''));
                return [
                    'type' => 'natural',
                    'value' => 'natural:'.$row->idCliente,
                    'label' => $label,
                ];
            });

        // Establecimientos (join con representante users)
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
                $label = trim(($doc ? 'NIT: '.$doc.' - ' : '').$nom.($tel ? ' - Tel: '.$tel : ''));
                return [
                    'type' => 'establecimiento',
                    'value' => 'establecimiento:'.$row->idEstablecimiento,
                    'label' => $label,
                ];
            });

        $results = $naturales->concat($establecimientos)->values();

        return response()->json(['results' => $results]);
    }

    /**
     * Actualizar estado de pedido
     */
    public function actualizarEstado(Request $request, $idVenta)
    {
        $request->validate([
            'estadoPedido' => 'required|in:0,1,2,3'
        ]);

        $pedido = Venta::findOrFail($idVenta);
        $pedido->update(['estadoPedido' => $request->estadoPedido]);

        return redirect()->back()
                        ->with('success', 'Estado del pedido actualizado exitosamente');
    }
}
