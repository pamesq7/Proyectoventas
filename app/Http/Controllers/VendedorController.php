<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use Illuminate\Http\Request;

class VendedorController extends Controller
{
    // Método dashboard (estadísticas básicas para vendedor)
    public function dashboard()
    {
        if (!auth()->check()) {
            abort(403, 'No autenticado');
        }

        $estadisticas = [
            'totalVentas' => Venta::where('idEmpleado', auth()->user()->empleado->id)->count(),
            'ventasPendientes' => Venta::where('idEmpleado', auth()->user()->empleado->id)->where('estadoPago', 'pendiente')->count(),
            'totalIngresos' => Venta::where('idEmpleado', auth()->user()->empleado->id)->sum('total'),
            'ventasRecientes' => Venta::where('idEmpleado', auth()->user()->empleado->id)
                ->with(['clienteNatural.user', 'clienteEstablecimiento'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return view('rolVendedor.dashboard', compact('estadisticas'));
    }

    // Método index (opcional, si necesitas un índice simple)
    public function index()
    {
        return redirect()->route('rolVendedor.dashboard');
    }

    // Método catalogo (si necesitas mantenerlo)
    public function catalogo(Request $request)
    {
        // Lógica para catálogo si es necesario
        return view('rolVendedor.catalogo');
    }
}