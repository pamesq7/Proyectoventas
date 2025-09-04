<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Empleado;
use App\Models\Transaccion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    /**
     * Módulo principal de ventas - muestra ventas saldadas y pendientes
     */
    public function index(Request $request)
    {
        $query = Venta::with(['empleado', 'clienteNatural', 'clienteEstablecimiento', 'detalleVentas.talla', 'transacciones'])
            ->orderBy('created_at', 'desc');

        // Filtro por estado de pago
        if ($request->filled('estado_pago')) {
            if ($request->estado_pago == 'saldado') {
                $query->where('saldo', '<=', 0);
            } elseif ($request->estado_pago == 'pendiente') {
                $query->where('saldo', '>', 0);
            }
        }

        // Filtro por fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        // Filtro por estado del pedido
        if ($request->filled('estado_pedido')) {
            $query->where('estado', $request->estado_pedido);
        }

        // Filtro por tipo de cliente
        if ($request->filled('tipo_cliente')) {
            if ($request->tipo_cliente == 'natural') {
                $query->whereNotNull('idCliente');
            } elseif ($request->tipo_cliente == 'establecimiento') {
                $query->whereNotNull('idEstablecimiento');
            }
        }

        $ventas = $query->paginate(15);

        // Estadísticas rápidas
        $estadisticas = [
            'total_ventas' => Venta::count(),
            'ventas_saldadas' => Venta::where('saldo', '<=', 0)->count(),
            'ventas_pendientes' => Venta::where('saldo', '>', 0)->count(),
            'monto_pendiente' => Venta::where('saldo', '>', 0)->sum('saldo'),
        ];

        return view('ventas.index', compact('ventas', 'estadisticas'));
    }

    /**
     * Mostrar detalle de una venta específica
     */
    public function show($id)
    {
        $venta = Venta::with([
            'empleado', 
            'clienteNatural', 
            'clienteEstablecimiento', 
            'detalleVentas.talla',
            'transacciones.user',
            'disenos'
        ])->findOrFail($id);

        return view('ventas.show', compact('venta'));
    }

    /**
     * Formulario para registrar nueva venta/pago
     */
    public function create()
    {
        $ventasPendientes = Venta::with(['clienteNatural', 'clienteEstablecimiento'])
            ->where('saldo', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ventas.create', compact('ventasPendientes'));
    }

    /**
     * Registrar nueva transacción/pago
     */
    public function store(Request $request)
    {
        $request->validate([
            'idVenta' => 'required|exists:ventas,idVenta',
            'monto' => 'required|numeric|min:0.01',
            'metodoPago' => 'required|string|max:50',
            'tipoTransaccion' => 'required|in:pago,abono,descuento',
            'observaciones' => 'nullable|string|max:500'
        ]);

        $venta = Venta::findOrFail($request->idVenta);

        // Verificar que el monto no exceda el saldo pendiente
        if ($request->monto > $venta->saldo) {
            return back()->withErrors(['monto' => 'El monto no puede ser mayor al saldo pendiente']);
        }

        // Crear la transacción
        $transaccion = Transaccion::create([
            'tipoTransaccion' => $request->tipoTransaccion,
            'monto' => $request->monto,
            'metodoPago' => $request->metodoPago,
            'observaciones' => $request->observaciones,
            'estado' => 1,
            'idVenta' => $request->idVenta,
            'idUser' => Auth::id()
        ]);

        // Actualizar saldo de la venta
        $venta->saldo = $venta->saldo - $request->monto;
        $venta->save();

        return redirect()->route('ventas.show', $request->idVenta)
            ->with('success', 'Pago registrado correctamente. Saldo actualizado.');
    }

    /**
     * Consultar clientes morosos (con saldo pendiente)
     */
    public function clientesMorosos()
    {
        $clientesMorosos = collect();

        // Clientes naturales con saldo pendiente (pagos incompletos)
        $naturales = DB::table('ventas')
            ->join('cliente_naturals', 'ventas.idCliente', '=', 'cliente_naturals.idCliente')
            ->join('users', 'cliente_naturals.idCliente', '=', 'users.idUser')
            ->where('ventas.saldo', '>', 0) // Solo pagos incompletos
            ->select(
                'cliente_naturals.idCliente as id_cliente',
                DB::raw("CONCAT(users.name, ' ', users.primerApellido, COALESCE(CONCAT(' ', users.segundApellido), '')) as nombre_cliente"),
                'users.telefono',
                DB::raw("'Natural' as tipo_cliente"),
                DB::raw('SUM(ventas.saldo) as saldo_total'),
                DB::raw('COUNT(ventas.idVenta) as ventas_pendientes'),
                DB::raw('AVG(DATEDIFF(NOW(), ventas.created_at)) as dias_atraso_promedio')
            )
            ->groupBy('cliente_naturals.idCliente', 'users.name', 'users.primerApellido', 'users.segundApellido', 'users.telefono')
            ->get();

        // Clientes establecimientos con saldo pendiente (pagos incompletos)
        $establecimientos = DB::table('ventas')
            ->join('cliente_establecimientos', 'ventas.idEstablecimiento', '=', 'cliente_establecimientos.idEstablecimiento')
            ->join('users as representante', 'cliente_establecimientos.idRepresentante', '=', 'representante.idUser')
            ->where('ventas.saldo', '>', 0) // Solo pagos incompletos
            ->select(
                'cliente_establecimientos.idEstablecimiento as id_cliente',
                'cliente_establecimientos.razonSocial as nombre_cliente',
                'representante.telefono',
                DB::raw("'Establecimiento' as tipo_cliente"),
                DB::raw('SUM(ventas.saldo) as saldo_total'),
                DB::raw('COUNT(ventas.idVenta) as ventas_pendientes'),
                DB::raw('AVG(DATEDIFF(NOW(), ventas.created_at)) as dias_atraso_promedio')
            )
            ->groupBy('cliente_establecimientos.idEstablecimiento', 'cliente_establecimientos.razonSocial', 'representante.telefono')
            ->get();

        // Combinar ambos tipos de clientes y ordenar por saldo total descendente
        $clientesMorosos = $naturales->concat($establecimientos)->sortByDesc('saldo_total');

        return view('ventas.morosos', compact('clientesMorosos'));
    }

    /**
     * Dashboard de ventas con estadísticas
     */
    public function dashboard()
    {
        $estadisticas = [
            'total_ventas' => Venta::count(),
            'ventas_mes' => Venta::whereMonth('created_at', now()->month)->count(),
            'ingresos_mes' => Venta::whereMonth('created_at', now()->month)->sum('total'),
            'ventas_saldadas' => Venta::where('saldo', '<=', 0)->count(),
            'ventas_pendientes' => Venta::where('saldo', '>', 0)->count(),
            'monto_pendiente' => Venta::where('saldo', '>', 0)->sum('saldo'),
            'clientes_morosos' => Venta::where('saldo', '>', 0)->distinct('idCliente', 'idEstablecimiento')->count()
        ];

        $ventasRecientes = Venta::with(['clienteNatural', 'clienteEstablecimiento'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('ventas.dashboard', compact('estadisticas', 'ventasRecientes'));
    }

    /**
     * Actualizar estado del pedido
     */
    public function actualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:0,1,2,3'
        ]);

        $venta = Venta::findOrFail($id);
        $venta->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'nuevo_estado' => $venta->estado_texto
        ]);
    }
}
