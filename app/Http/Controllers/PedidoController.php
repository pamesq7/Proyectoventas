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
            // Arrays de items
            'idTalla' => 'required|array|min:1',
            'idTalla.*' => 'required|exists:tallas,idTalla',
            'cantidad' => 'required|array|min:1',
            'cantidad.*' => 'required|integer|min:1',
            'nombrePersonalizado' => 'nullable|array',
            'numeroPersonalizado' => 'nullable|array',
            'observaciones' => 'nullable|array',
            // Pago
            'tipoTransaccion' => 'nullable|in:efectivo,qr,cheque,transferencia',
            'montoAdelanto' => 'nullable|numeric|min:0',
        ]);

        $producto = Producto::findOrFail($request->idProducto);
        $rutaDiseno = session()->get('disenoTemporal');

        $idsTalla = $request->input('idTalla', []);
        $cantidades = $request->input('cantidad', []);
        $nombres = $request->input('nombrePersonalizado', []);
        $numeros = $request->input('numeroPersonalizado', []);
        $observs = $request->input('observaciones', []);

        DB::beginTransaction();
        try {
            $total = 0.0;
            $itemsCalculados = [];

            // Obtener un idEmpleado válido (evitar FK inválida)
            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar la venta. Cree un empleado o asocie uno al usuario actual.');
            }

            foreach ($idsTalla as $i => $idTalla) {
                $cant = (int)($cantidades[$i] ?? 0);
                if ($cant <= 0) { continue; }
                // Precio unitario = precio base del producto + adicional por talla (si existe)
                $precioAdicional = (float) (ProductoTalla::where('idProducto', $producto->idProducto)
                    ->where('idTalla', $idTalla)
                    ->value('precioAdicional') ?? 0);
                $precioUnit = (float) ($producto->precioVenta ?? 0) + $precioAdicional;
                $sub = $precioUnit * $cant;
                $total += $sub;
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

            // Crear la venta
            $venta = Venta::create([
                'subtotal' => $total,
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

            // Crear detalles por cada fila
            foreach ($itemsCalculados as $it) {
                DetalleVenta::create([
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
            }

            // Registrar adelanto como pago (si corresponde)
            $montoAdelanto = (float) ($request->montoAdelanto ?? 0);
            if ($montoAdelanto > 0) {
                if ($montoAdelanto > $total) {
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
                $venta->saldo = max($total - $montoAdelanto, 0);
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

            // Obtener un idEmpleado válido (evitar FK inválida)
            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar la venta. Cree un empleado o asocie uno al usuario actual.');
            }

            // Crear la venta/pedido
            $venta = Venta::create([
                'subtotal' => $total,
                'total' => $total,
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0', // Solicitado
                'saldo' => $total,
                'estado' => 1,
                'idEmpleado' => $idEmpleadoSeguro,
                'idCliente' => $request->tipoCliente === 'natural' ? $request->idCliente : null,
                'idEstablecimiento' => $request->tipoCliente === 'establecimiento' ? $request->idEstablecimiento : null,
            ]);

            // Crear detalles de venta para cada item del carrito
            foreach ($carrito as $item) {
                DetalleVenta::create([
                    'cantidad' => $item['cantidad'],
                    'nombrePersonalizado' => $item['nombrePersonalizado'] ?? null,
                    'numeroPersonalizado' => $item['numeroPersonalizado'] ?? null,
                    'textoAdicional' => $item['textoAdicional'] ?? null,
                    'observacion' => $request->observaciones,
                    'precioUnitario' => $item['precioUnitario'],
                    'estado' => 1,
                    'idTalla' => $item['idTalla'],
                    'idVenta' => $venta->idVenta,
                    'idEmpleado' => $idEmpleadoSeguro,
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
             'detalleVentas.talla',
             'clienteNatural',
             'clienteEstablecimiento',
             'transacciones'
         ])->findOrFail($idVenta);

        // Tallas activas para el formulario de agregar detalle
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get(['idTalla','nombre']);

        // Lista fija de métodos de pago (no depende de tabla)
        $metodosPago = collect([
            ['id' => null, 'nombre' => 'Efectivo', 'codigo' => 'efectivo'],
            ['id' => null, 'nombre' => 'QR', 'codigo' => 'qr'],
            ['id' => null, 'nombre' => 'Cheque', 'codigo' => 'cheque'],
            ['id' => null, 'nombre' => 'Transferencia bancaria', 'codigo' => 'transferencia'],
        ]);

        return view('pedidos.confirmacion', compact('venta', 'metodosPago', 'tallas'));
    }

    /**
     * Agregar un detalle de venta desde la confirmación y recalcular totales/saldo
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
        ]);

        DB::beginTransaction();
        try {
            $venta = Venta::with('transacciones')->findOrFail($idVenta);

            // Obtener un idEmpleado válido
            $idEmpleadoSeguro = optional(optional(auth()->user())->empleado)->idEmpleado;
            if (!$idEmpleadoSeguro) {
                $idEmpleadoSeguro = DB::table('empleados')->value('idEmpleado');
            }
            if (!$idEmpleadoSeguro) {
                return back()->with('error', 'No existe ningún empleado registrado para asociar el detalle.');
            }

            // Crear el detalle
            DetalleVenta::create([
                'cantidad' => (int) $request->cantidad,
                'nombrePersonalizado' => $request->nombrePersonalizado,
                'numeroPersonalizado' => $request->numeroPersonalizado,
                'textoAdicional' => $request->textoAdicional,
                'observacion' => $request->observacion,
                'descripcion' => $request->descripcion,
                'precioUnitario' => (float) $request->precioUnitario,
                'estado' => 1,
                'idTalla' => (int) $request->idTalla,
                'idVenta' => $venta->idVenta,
                'idEmpleado' => $idEmpleadoSeguro,
            ]);

            // Recalcular subtotal/total y saldo
            $nuevoSubtotal = DetalleVenta::where('idVenta', $venta->idVenta)
                ->selectRaw('COALESCE(SUM(cantidad * precioUnitario),0) as s')
                ->value('s');

            $venta->subtotal = $nuevoSubtotal;
            $venta->total = $nuevoSubtotal; // ajustar si hay recargos/descuentos

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
            return redirect()->back()->with('error', 'No se pudo agregar el detalle: '.$e->getMessage());
        }
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

    /**
     * Registrar un pago para una venta (sin modificar el esquema de BD)
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
            // Bloqueo pesimista para evitar condiciones de carrera al calcular saldo
            $venta = Venta::where('idVenta', $idVenta)->lockForUpdate()->firstOrFail();

            $monto = (float) $request->monto;

            // Recalcular saldo en base a pagos ya registrados
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

            // Actualizar saldo persistido tomando en cuenta el nuevo pago
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
}
