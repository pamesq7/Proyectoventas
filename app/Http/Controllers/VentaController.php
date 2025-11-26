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
use Illuminate\Support\Facades\Log;

class VentaController extends Controller
{
    /**
     * Módulo principal de ventas - muestra ventas saldadas y pendientes
     */
    public function index(Request $request)
    {
        $query = Venta::with([
            'empleado.user',
            'clienteNatural.user',
            'clienteEstablecimiento',
            'detalleVentas.tallas',
            'transacciones' // Eliminamos el .user de aquí
        ])
            ->where('ventas.estado', 1)
            ->orderBy('created_at', 'desc');

        // Filtro por estado de pago
        if ($request->filled('estado_pago')) {
            if ($request->estado_pago == 'saldado') {
                $query->where('ventas.saldo', '<=', 0);
            } elseif ($request->estado_pago == 'pendiente') {
                $query->where('ventas.saldo', '>', 0);
            }
        }

        // Filtro por fechas
        if ($request->filled('fecha_desde')) {
            $query->whereDate('ventas.created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('ventas.created_at', '<=', $request->fecha_hasta);
        }

        // Filtro por tipo de cliente
        if ($request->filled('tipo_cliente')) {
            if ($request->tipo_cliente == 'natural') {
                $query->whereNotNull('ventas.idCliente');
            } elseif ($request->tipo_cliente == 'establecimiento') {
                $query->whereNotNull('ventas.idEstablecimiento');
            }
        }

        $ventas = $query->paginate(15);

        // Procesar datos para la vista
        $ventas->each(function ($venta) {
            // Calcular nombre del cliente
            if ($venta->clienteNatural && $venta->clienteNatural->user) {
                $venta->nombre_cliente = $venta->clienteNatural->user->name . ' ' .
                    $venta->clienteNatural->user->primerApellido;
                $venta->tipo_cliente = 'Natural';
            } elseif ($venta->clienteEstablecimiento) {
                $venta->nombre_cliente = $venta->clienteEstablecimiento->razonSocial;
                $venta->tipo_cliente = 'Establecimiento';
            } else {
                $venta->nombre_cliente = 'Cliente no especificado';
                $venta->tipo_cliente = 'N/A';
            }

            // Calcular estado de pago
            $montoPagado = $venta->transacciones ? $venta->transacciones->where('tipoTransaccion', 'pago')->sum('monto') : 0;
            $venta->monto_pagado = $montoPagado;
            $venta->saldo = max(0, $venta->total - $montoPagado);

            if ($montoPagado >= $venta->total) {
                $venta->estado_pago = 'PAGADO';
            } elseif ($montoPagado > 0) {
                $venta->estado_pago = 'PARCIAL';
                $venta->porcentaje_pagado = round(($montoPagado / $venta->total) * 100, 2);
            } else {
                $venta->estado_pago = 'PENDIENTE';
            }

            // Nombre del empleado
            if ($venta->empleado && $venta->empleado->user) {
                $venta->nombre_empleado = $venta->empleado->user->name . ' ' .
                    $venta->empleado->user->primerApellido;
            } else {
                $venta->nombre_empleado = 'No asignado';
            }

            // Calcular días de atraso si aplica
            $venta->dias_atraso = 0;
            if ($venta->fechaEntrega) {
                $fechaEntrega = \Carbon\Carbon::parse($venta->fechaEntrega);
                $venta->dias_atraso = \Carbon\Carbon::now()->diffInDays($fechaEntrega, false);
            }
        });

        // Estadísticas (solo de ventas con estado = 1)
        $estadisticas = [
            'total_ventas' => Venta::where('estado', 1)->count(),
            'ventas_saldadas' => Venta::where('estado', 1)->where('saldo', '<=', 0)->count(),
            'ventas_pendientes' => Venta::where('estado', 1)->where('saldo', '>', 0)->count(),
            'monto_pendiente' => Venta::where('estado', 1)->where('saldo', '>', 0)->sum('saldo'),
        ];


        return view('ventas.index', compact('ventas', 'estadisticas'));
    }

    /**
     * Mostrar detalle de una venta específica
     */
    public function show($id)
{
    $venta = Venta::with([
        'detalleVentas.detalleTallas.talla',
        'detalleVentas.tallas',
        'empleado.user',
        'clienteNatural.user',
        'clienteEstablecimiento',
        'transacciones' // Eliminamos el .user de aquí
    ])->findOrFail($id);

    return view('ventas.show', compact('venta'));
}

    /**
     * Formulario para registrar nueva venta/pago
     */
    public function create()
    {
        $ventasPendientes = Venta::with(['clienteNatural.user', 'clienteEstablecimiento'])
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
    Log::info('Iniciando registro de pago', ['request' => $request->all()]);

    $request->validate([
        'idVenta' => 'required|exists:ventas,idVenta',
        'monto' => 'required|numeric|min:0.01',
        'metodoPago' => 'required|string|max:50',
        'observaciones' => 'nullable|string|max:500'
    ]);

    return DB::transaction(function () use ($request) {
        try {
            Log::info('Transacción iniciada', ['idVenta' => $request->idVenta]);

            $venta = Venta::lockForUpdate()->findOrFail($request->idVenta);

            // Verificar que el monto no exceda el saldo pendiente
            if ($request->monto > $venta->saldo) {
                Log::warning('Monto excede saldo', ['monto' => $request->monto, 'saldo' => $venta->saldo]);
                return back()->withErrors(['monto' => 'El monto no puede ser mayor al saldo pendiente: Bs. ' . number_format($venta->saldo, 2)])->withInput();
            }

            // Crear la transacción
            Transaccion::create([
                'tipoTransaccion' => 'pago',
                'monto' => $request->monto,
                'metodoPago' => $request->metodoPago ?? 'efectivo',
                'observaciones' => $request->observaciones,
                'estado' => 1,
                'idVenta' => $request->idVenta,
                'idUser' => Auth::id()
            ]);

            // Actualizar saldo de la venta
            $nuevoSaldo = max(0, $venta->saldo - $request->monto);
            $venta->saldo = $nuevoSaldo;
            $venta->save();

            Log::info('Pago registrado exitosamente', [
                'idVenta' => $request->idVenta,
                'monto' => $request->monto,
                'nuevoSaldo' => $nuevoSaldo
            ]);

            return redirect()
                ->route('ventas.show', $request->idVenta)
                ->with('success', 'Pago registrado correctamente. Saldo actualizado: Bs. ' . number_format($nuevoSaldo, 2));

        } catch (\Exception $e) {
            Log::error('Error al registrar pago: ' . $e->getMessage(), [
                'exception' => $e,
                'venta_id' => $request->idVenta
            ]);
            return back()->with('error', 'Error al procesar el pago: ' . $e->getMessage())->withInput();
        }
    });
}

    public function confirmacion($idVenta)
    {
        $venta = Venta::with([
            'detalleVentas.detalleTallas.talla', // <-- así
            'clienteNatural',
            'clienteEstablecimiento',
            'transacciones'
        ])->findOrFail($idVenta);

        // Tallas activas para el formulario de agregar detalle
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get(['idTallas', 'nombre']);

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

        $ventasRecientes = Venta::with(['clienteNatural.user', 'clienteEstablecimiento'])
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
