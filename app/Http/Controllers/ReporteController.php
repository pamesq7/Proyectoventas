<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;

class ReporteController extends Controller
{
    /**
     * Vista principal de reportes con estadísticas básicas
     */
    public function index()
    {
        // Estadísticas generales
        $estadisticas = [
            'total_usuarios' => User::count(),
            'total_productos' => Producto::count(),
            'total_pedidos' => Venta::count(),
            'total_ventas' => Venta::sum('total') ?? 0,
            'saldo_pendiente' => Venta::sum('saldo') ?? 0,
            'clientes_naturales' => ClienteNatural::count(),
            'clientes_establecimientos' => ClienteEstablecimiento::count(),
        ];

        // Ventas del mes actual
        $ventasMesActual = Venta::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total') ?? 0;

        // Productos activos (sin consulta de ventas por ahora)
        $topProductos = Producto::where('estado', 1)
            ->orderBy('nombre')
            ->limit(5)
            ->get(['nombre'])
            ->map(function($producto) {
                $producto->total_vendido = 0; // Placeholder
                return $producto;
            });

        // Estados de pedidos
        $estadosPedidos = [
            'solicitados' => Venta::where('estado', '0')->count(),
            'en_diseno' => Venta::where('estado', '1')->count(),
            'en_confeccion' => Venta::where('estado', '2')->count(),
            'entregados' => Venta::where('estado', '3')->count(),
        ];

        // Estados de pago
        $estadosPago = [
            'pagados' => Venta::where('saldo', 0)->count(),
            'pendientes' => Venta::where('saldo', '>', 0)->count(),
        ];

    /**
     * Reporte de ventas mensuales
     */
    public function ventasMensuales(Request $request)
    {
        $añoSeleccionado = $request->get('año', date('Y'));

        // Obtener años disponibles
        $añosDisponibles = Venta::selectRaw('YEAR(created_at) as año')
            ->distinct()
            ->orderBy('año', 'desc')
            ->pluck('año');

        // Ventas mensuales del año seleccionado
        $ventasMensuales = Venta::selectRaw('
                MONTH(created_at) as mes_numero,
                SUM(total) as total,
                COUNT(*) as cantidad_ventas
            ')
            ->whereYear('created_at', $añoSeleccionado)
            ->groupBy('mes_numero')
            ->orderBy('mes_numero')
            ->get();

        // Crear array con todos los meses
        $meses = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];

        $ventasCompletas = [];
        foreach ($meses as $index => $mes) {
            $ventaMes = $ventasMensuales->where('mes_numero', $index + 1)->first();
            $ventasCompletas[] = [
                'mes' => $mes,
                'total' => $ventaMes ? $ventaMes->total : 0,
                'cantidad_ventas' => $ventaMes ? $ventaMes->cantidad_ventas : 0
            ];
        }

        // Cálculos
        $totalAnual = array_sum(array_column($ventasCompletas, 'total'));
        $promedioMensual = count($ventasCompletas) > 0 ? $totalAnual / count($ventasCompletas) : 0;

        // Mes con mayor venta
        $mesMayorVenta = collect($ventasCompletas)->sortByDesc('total')->first();
        $mesMayorVenta = $mesMayorVenta ?: ['mes' => 'N/A', 'total' => 0];

        // Mes con menor venta
        $mesMenorVenta = collect($ventasCompletas)->sortBy('total')->first();
        $mesMenorVenta = $mesMenorVenta ?: ['mes' => 'N/A', 'total' => 0];

        $reporte = [
            'ventasMensuales' => $ventasCompletas,
            'totalAnual' => $totalAnual,
            'promedioMensual' => $promedioMensual,
            'mesMayorVenta' => $mesMayorVenta,
            'mesMenorVenta' => $mesMenorVenta,
            'añoSeleccionado' => $añoSeleccionado,
            'añosDisponibles' => $añosDisponibles
        ];

        return view('reportes.ventas-mensuales', compact('reporte'));
    }
