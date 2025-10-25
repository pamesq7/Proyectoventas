<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Transaccion;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Producto;
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
            ->map(function ($producto) {
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

        return view('reportes.index', compact(
            'estadisticas',
            'ventasMesActual',
            'topProductos',
            'estadosPedidos',
            'estadosPago'
        ));
    }
    /**
     * Reporte de ventas mensuales (como tu imagen)
     */
    public function ventasMensuales(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Consulta para ventas mensuales
        $ventasMensuales = Venta::whereYear('created_at', $year)
            ->where('estado', 1)
            ->selectRaw('MONTH(created_at) as mes_numero, SUM(total) as total_mes, COUNT(*) as cantidad_ventas')
            ->groupBy('mes_numero')
            ->orderBy('mes_numero')
            ->get()
            ->map(function ($item) {
                return [
                    'mes' => $this->getNombreMes($item->mes_numero),
                    'total' => $item->total_mes ?? 0,
                    'cantidad_ventas' => $item->cantidad_ventas,
                    'mes_numero' => $item->mes_numero
                ];
            });

        // Llenar meses faltantes con cero
        $ventasCompletas = $this->completarMesesFaltantes($ventasMensuales, $year);

        // Cálculos adicionales
        $totalAnual = $ventasCompletas->sum('total');
        $promedioMensual = $totalAnual > 0 ? $totalAnual / 12 : 0;

        $mesMayorVenta = $ventasCompletas->where('total', $ventasCompletas->max('total'))->first();
        $mesMenorVenta = $ventasCompletas->where('total', $ventasCompletas->min('total'))->first();

        $añosDisponibles = $this->getAñosDisponibles();

        return view('reportes.ventas-mensuales', compact(
            'ventasCompletas',
            'year',
            'totalAnual',
            'promedioMensual',
            'mesMayorVenta',
            'mesMenorVenta',
            'añosDisponibles'
        ));
    }

    /**
     * Reporte por rango de fechas
     */
    public function ventasRangoFechas(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $ventas = Venta::whereBetween('created_at', [$startDate, $endDate])
            ->where('estado', 1)
            ->selectRaw('DATE(created_at) as fecha, SUM(total) as total_dia, COUNT(*) as cantidad_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $totalRango = $ventas->sum('total_dia');
        $cantidadVentas = $ventas->sum('cantidad_ventas');

        return view('reportes.ventas-rango', compact(
            'ventas',
            'startDate',
            'endDate',
            'totalRango',
            'cantidadVentas'
        ));
    }

    /**
     * Comparativa anual - VERSIÓN CORREGIDA (solo una declaración)
     */
    public function comparativaAnual(Request $request)
    {
        // Obtener años seleccionados o usar años por defecto
        $selectedYears = $request->get('years');

        if (empty($selectedYears)) {
            $currentYear = date('Y');
            $selectedYears = [$currentYear - 1, $currentYear];
        }

        // Asegurarse de que sea un array
        if (!is_array($selectedYears)) {
            $selectedYears = [$selectedYears];
        }

        $comparativa = [];
        foreach ($selectedYears as $year) {
            $ventasAnio = Venta::whereYear('created_at', $year)
                ->where('estado', 1)
                ->selectRaw('MONTH(created_at) as mes, SUM(total) as total')
                ->groupBy('mes')
                ->get()
                ->keyBy('mes');

            // Completar todos los meses
            $mesesCompletos = [];
            for ($mes = 1; $mes <= 12; $mes++) {
                $mesesCompletos[$mes] = $ventasAnio->get($mes)->total ?? 0;
            }

            $comparativa[$year] = $mesesCompletos;
        }

        $añosDisponibles = $this->getAñosDisponibles();

        return view('reportes.comparativa-anual', compact('comparativa', 'selectedYears', 'añosDisponibles'));
    }

    /**
     * Helper: Obtener nombre del mes
     */
    private function getNombreMes($numeroMes)
    {
        $meses = [
            1 => 'ENERO',
            2 => 'FEBRERO',
            3 => 'MARZO',
            4 => 'ABRIL',
            5 => 'MAYO',
            6 => 'JUNIO',
            7 => 'JULIO',
            8 => 'AGOSTO',
            9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVIEMBRE',
            12 => 'DICIEMBRE'
        ];

        return $meses[$numeroMes] ?? 'DESCONOCIDO';
    }

    /**
     * Helper: Completar meses faltantes
     */
    private function completarMesesFaltantes($ventasMensuales, $year)
    {
        $mesesCompletos = collect();

        for ($mes = 1; $mes <= 12; $mes++) {
            $ventaMes = $ventasMensuales->firstWhere('mes_numero', $mes);

            $mesesCompletos->push([
                'mes' => $this->getNombreMes($mes),
                'total' => $ventaMes['total'] ?? 0,
                'cantidad_ventas' => $ventaMes['cantidad_ventas'] ?? 0,
                'mes_numero' => $mes
            ]);
        }

        return $mesesCompletos;
    }

    /**
     * Helper: Obtener años disponibles
     */
    private function getAñosDisponibles()
    {
        $años = Venta::where('estado', 1)
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Si no hay años, usar el año actual
        return empty($años) ? [date('Y')] : $años;
    }
}
