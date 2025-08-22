<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Talla;
use App\Models\Variante;
use App\Models\ProductoVariante;
use App\Models\Caracteristica;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\ProductoOpcion;
use App\Models\Opcion;
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
     * API: Opciones y características por producto (sin selector de variante)
     * Origen: tabla producto_opcions (ProductoOpcion) + Caracteristica por idOpcion
     */
    public function apiOpcionesPorProducto($idProducto)
    {
        try {
            // Opciones configuradas para el producto
            $productoOpciones = ProductoOpcion::with(['opcion'])
                ->where('idProducto', $idProducto)
                ->where('estado', 1)
                ->get();

            $resultado = [];

            foreach ($productoOpciones as $po) {
                $op = $po->opcion; // instancia de Opcion
                if (!$op || (int)($op->estado) !== 1) { continue; }

                // Características activas de esta opción
                $caracteristicas = Caracteristica::where('idOpcion', $op->idOpcion)
                    ->where('estado', 1)
                    ->orderBy('nombre')
                    ->get(['idCaracteristica','nombre']);

                $resultado[] = [
                    'idOpcion' => $op->idOpcion,
                    'nombreOpcion' => $op->nombre,
                    'caracteristicas' => $caracteristicas->map(fn($c)=>[
                        'idCaracteristica' => $c->idCaracteristica,
                        'nombre' => $c->nombre,
                    ])->values(),
                ];
            }

            return response()->json(['opciones' => array_values($resultado)]);
        } catch (\Throwable $e) {
            Log::error('apiOpcionesPorProducto error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error obteniendo opciones del producto', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Listar variantes activas (para UI sin selector de producto)
     */
    public function apiVariantesActivas()
    {
        try {
            $variantes = Variante::where('estado', 1)
                ->orderBy('nombre')
                ->get(['id','nombre'])
                ->map(function($v){
                    return [
                        'idVariante' => $v->id,
                        'nombre' => $v->nombre,
                    ];
                })
                ->values();
            return response()->json(['variantes' => $variantes]);
        } catch (\Throwable $e) {
            Log::error('apiVariantesActivas error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error obteniendo variantes', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Productos asociados a una variante (vía pivote producto_variantes o campo directo)
     */
    public function apiProductosPorVariante($idVariante)
    {
        // Buscar por pivote
        $pivotes = ProductoVariante::with('producto')
            ->where('idVariante', $idVariante)
            ->activo()
            ->whereHas('producto', function($q){
                $q->whereIn('idProducto', [1,2,3,4]);
            })
            ->get();

        $productos = $pivotes->map(function ($pv) {
            return [
                'idProducto' => $pv->producto->idProducto ?? null,
                'nombre' => $pv->producto->nombre ?? null,
            ];
        })->filter(fn($p) => !is_null($p['idProducto']))->values();

        // Fallback: productos que apuntan directamente a la variante
        if ($productos->isEmpty()) {
            $directos = Producto::where('idVariante', $idVariante)
                ->where('estado', 1)
                ->whereIn('idProducto', [1,2,3,4])
                ->orderBy('nombre')
                ->get(['idProducto','nombre']);
            $productos = $directos->map(fn($p) => ['idProducto'=>$p->idProducto,'nombre'=>$p->nombre]);
        }

        return response()->json(['productos' => $productos]);
    }

    /**
     * API: Variantes disponibles para un producto
     */
    public function apiVariantesPorProducto($idProducto)
    {
        $producto = Producto::where('idProducto', $idProducto)->firstOrFail();

        // Obtener variantes vía pivote producto_variantes
        $pivotes = ProductoVariante::with('variante')
            ->where('idProducto', $idProducto)
            ->activo()
            ->get();

        $variantes = $pivotes
            ->map(function ($pv) {
                return [
                    'idVariante' => $pv->variante->id ?? null,
                    'nombre' => $pv->variante->nombre ?? 'Variante',
                ];
            })
            ->filter(fn($v) => !is_null($v['idVariante']))
            ->values();

        // Fallback: si el producto tiene una variante directa asociada
        if ($variantes->isEmpty() && !empty($producto->idVariante)) {
            $v = Variante::find($producto->idVariante);
            if ($v && (int)($v->estado) === 1) {
                $variantes = collect([[
                    'idVariante' => $v->id,
                    'nombre' => $v->nombre,
                ]]);
            }
        }

        return response()->json([
            'producto' => ['idProducto' => $producto->idProducto, 'nombre' => $producto->nombre],
            'variantes' => $variantes,
        ]);
    }

    /**
     * API: Características agrupadas por opción para una variante
     */
    public function apiCaracteristicasDeVariante($idVariante)
    {
        try {
            $variante = Variante::with([
                    'varianteCaracteristicas' => function ($q) {
                        $q->where('estado', 1);
                    },
                    'varianteCaracteristicas.caracteristica' => function ($q) {
                        $q->where('estado', 1);
                    },
                    'varianteCaracteristicas.caracteristica.opcion' => function ($q) {
                        $q->where('estado', 1);
                    }
                ])
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
                'idVariante' => $variante->id,
                'nombreVariante' => $variante->nombre,
                'opciones' => $resultado,
            ]);
        } catch (\Throwable $e) {
            Log::error('apiCaracteristicasDeVariante error: '.$e->getMessage());
            return response()->json(['error' => 'Error obteniendo características', 'detail' => $e->getMessage()], 500);
        }
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
        $tallas = Talla::where('estado', 1)->orderBy('idTalla')->get();

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
                                ->whereIn('idProducto', [1,2,3,4])
                                ->orderBy('nombre')
                                ->get(['idProducto', 'nombre']);

        $tallas = Talla::where('estado', 1)->orderBy('idTalla')->get();

        return view('pedidos.personalizar', compact('productosBase', 'tallas'));
    }

    /**
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

        $productos = Producto::where('estado', 1)
                             ->whereIn('idProducto', [1,2,3,4])
                             ->orderBy('nombre')
                             ->get();
        $tallas = Talla::where('estado', 1)->orderBy('idTalla')->get();

        $clientesNaturales = ClienteNatural::where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->get();

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
            'clienteSeleccionado' => 'required|string', // formato esperado: natural:ID o establecimiento:ID
            'fechaEntrega' => 'required|date|after:today',
            'lugarEntrega' => 'required|string|max:200',
            'idProducto' => 'required|exists:productos,idProducto',
            // Arrays de items
            'idTalla' => 'required|array',
            'idTalla.*' => 'required|exists:tallas,idTalla',
            'cantidad' => 'required|array',
            'cantidad.*' => 'required|integer|min:1',
            'nombrePersonalizado' => 'array',
            'nombrePersonalizado.*' => 'nullable|string|max:50',
            'numeroPersonalizado' => 'array',
            'numeroPersonalizado.*' => 'nullable|string|max:10',
            'observaciones' => 'array',
            'observaciones.*' => 'nullable|string|max:200',
            'textoAdicional' => 'nullable|string|max:200',
        ]);

        $producto = Producto::findOrFail($request->idProducto);
        $rutaDiseno = session()->get('disenoTemporal');

        // Parsear cliente seleccionado
        $clienteSel = $request->input('clienteSeleccionado');
        $tipo = null; $idSel = null;
        if (strpos($clienteSel, ':') !== false) {
            [$tipo, $idSel] = explode(':', $clienteSel, 2);
        }
        $tipo = $tipo === 'establecimiento' ? 'establecimiento' : 'natural';
        $idSel = (int) $idSel;

        DB::beginTransaction();
        try {
            $precioUnitario = $producto->precioVenta;
            $cantidades = $request->input('cantidad', []);
            $tallasReq = $request->input('idTalla', []);
            $nombres = $request->input('nombrePersonalizado', []);
            $numeros = $request->input('numeroPersonalizado', []);
            $observs = $request->input('observaciones', []);

            // Calcular total del pedido sumando todas las filas
            $subtotalTotal = 0;
            foreach ($cantidades as $c) {
                $subtotalTotal += ((int)$c) * $precioUnitario;
            }

            $venta = Venta::create([
                'subtotal' => $subtotalTotal,
                'total' => $subtotalTotal,
                'fechaEntrega' => $request->fechaEntrega,
                'lugarEntrega' => $request->lugarEntrega,
                'estadoPedido' => '0',
                'saldo' => $subtotalTotal,
                'estado' => 1,
                'idEmpleado' => auth()->user()->empleado->idEmpleado ?? 1,
                'idCliente' => $tipo === 'natural' ? $idSel : null,
                'idEstablecimiento' => $tipo === 'establecimiento' ? $idSel : null,
                'idUser' => auth()->id()
            ]);

            // Crear un detalle por cada fila enviada
            $numItems = count($tallasReq);
            for ($i = 0; $i < $numItems; $i++) {
                $idTallaItem = (int) ($tallasReq[$i] ?? 0);
                $cantidadItem = (int) ($cantidades[$i] ?? 0);
                if ($idTallaItem <= 0 || $cantidadItem <= 0) { continue; }

                $tallaItem = Talla::findOrFail($idTallaItem);
                $nombreItem = $nombres[$i] ?? null;
                $numeroItem = $numeros[$i] ?? null;
                $obsItem = $observs[$i] ?? null;

                DetalleVenta::create([
                    'cantidad' => $cantidadItem,
                    'nombrePersonalizado' => $nombreItem,
                    'numeroPersonalizado' => $numeroItem,
                    'textoAdicional' => $request->textoAdicional,
                    'observacion' => $obsItem,
                    'precioUnitario' => $precioUnitario,
                    'subtotal' => $precioUnitario * $cantidadItem,
                    'estado' => 1,
                    'idTalla' => $tallaItem->idTalla,
                    'idVenta' => $venta->idVenta,
                    'idProducto' => $producto->idProducto,
                    'idUser' => auth()->id()
                ]);
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
