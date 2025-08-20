<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Talla;
use App\Models\Variante;
use App\Models\Caracteristica;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
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
