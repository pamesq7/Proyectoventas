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

        return view('reportes.index', compact(
            'estadisticas', 'ventasMesActual', 'topProductos', 
            'estadosPedidos', 'estadosPago'
        ));
    }
}
