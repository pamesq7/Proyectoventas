<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Empleado;
use App\Models\Transaccion;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;



class MorosoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)  // Asegúrate de inyectar el Request
    {
        $query = Venta::with([
            'empleado.user',
            'clienteNatural.user',
            'clienteEstablecimiento',
            'detalleVentas.talla',
            'transacciones'
        ])
            ->where('ventas.estado', 1) // Solo ventas activas
            ->where('ventas.saldo', '>', 0) // Solo con saldo pendiente
            ->orderBy('created_at', 'desc');

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
            // Cálculo de nombre del cliente
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
            $montoPagado = $venta->transacciones ?
                $venta->transacciones->where('tipoTransaccion', 'pago')->sum('monto') : 0;

            $venta->monto_pagado = $montoPagado;
            $venta->saldo = max(0, $venta->total - $montoPagado);

            // Clasificar el estado de pago
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

            // Calcular días de atraso
            $venta->dias_atraso = 0;
            if ($venta->fechaEntrega) {
                $fechaEntrega = \Carbon\Carbon::parse($venta->fechaEntrega);
                $venta->dias_atraso = now()->diffInDays($fechaEntrega, false);
            }
        });

        // Estadísticas para la vista
        $estadisticas = [
            'total_morosos' => Venta::where('estado', 1)
                ->where('saldo', '>', 0)
                ->count(),

            'monto_total_pendiente' => Venta::where('estado', 1)
                ->where('saldo', '>', 0)
                ->sum('saldo'),

            'clientes_morosos' => Venta::where('estado', 1)
                ->where('saldo', '>', 0)
                ->selectRaw('COALESCE(idCliente, idEstablecimiento) as cliente_id')
                ->distinct()
                ->count(),

            'promedio_atraso' => Venta::where('estado', 1)
                ->where('saldo', '>', 0)
                ->whereNotNull('fechaEntrega')
                ->selectRaw('AVG(DATEDIFF(NOW(), fechaEntrega)) as atraso')
                ->value('atraso') ?? 0
        ];

        return view('morosos.index', compact('ventas', 'estadisticas'));
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $venta = Venta::with([
            'empleado.user',
            'clienteNatural.user',
            'clienteEstablecimiento',
            'detalleVentas.talla',
            'transacciones.user'
        ])->findOrFail($id);

        return view('ventas.show', compact('venta'));
    }

    public function confirmacion($idVenta)
    {
        $venta = Venta::with([
            'detalleVentas.talla',
            'clienteNatural',
            'clienteEstablecimiento',
            'transacciones'
        ])->findOrFail($idVenta);

        // Tallas activas para el formulario de agregar detalle
        $tallas = Talla::where('estado', 1)->orderBy('nombre')->get(['idTalla', 'nombre']);

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
    public function store(Request $request)
    {
        Log::info('Iniciando registro de pago', ['request' => $request->all()]);

        $request->validate([
            'idVenta' => 'required|exists:ventas,idVenta',
            'monto' => 'required|numeric|min:0.01',
            'metodoPago' => 'required|string|max:50', // Usa metodoPago como el método de pago (ej. 'efectivo')
            'observaciones' => 'nullable|string|max:500'
        ]);

        return DB::transaction(function () use ($request) {
            Log::info('Transacción iniciada', ['idVenta' => $request->idVenta]);

            $venta = Venta::lockForUpdate()->findOrFail($request->idVenta);

            // Verificar que el monto no exceda el saldo pendiente
            if ($request->monto > $venta->saldo) {
                Log::warning('Monto excede saldo', ['monto' => $request->monto, 'saldo' => $venta->saldo]);
                return back()->withErrors(['monto' => 'El monto no puede ser mayor al saldo pendiente']);
            }

            // Crear la transacción (usando metodoPago como el método de pago)
            Transaccion::create([
                'tipoTransaccion' => 'pago', // Tipo fijo para pagos
                'monto' => $request->monto,
                'metodoPago' => $request->metodoPago ?? 'efectivo', // Usa metodoPago del request como el método
                'observaciones' => $request->observaciones,
                'estado' => 1,
                'idVenta' => $request->idVenta,
                'idUser' => Auth::id()
            ]);

            // Actualizar saldo de la venta (evitar negativos)
            $venta->saldo = max(0, $venta->saldo - $request->monto);
            $venta->save();

            Log::info('Pago registrado exitosamente', ['idVenta' => $request->idVenta, 'nuevoSaldo' => $venta->saldo]);

            return redirect()->route('clientes.show', $request->idVenta)
                ->with('success', 'Pago registrado correctamente. Saldo actualizado.');
        });
    }
}
