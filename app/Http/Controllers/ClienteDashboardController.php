<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Venta;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use Illuminate\Support\Facades\DB;


class ClienteDashboardController extends Controller
{
    /**
     * Dashboard principal para clientes
     */
    public function index()
    {
        $user = Auth::user();

        // Obtener información del cliente
        $clienteNatural = $user->clienteNatural;
        $clienteEstablecimiento = $user->clienteEstablecimiento;

        // ✅ CORREGIDO: Usar la relación definida en el modelo User
        $totalPedidos = $user->pedidos()->count();
        $pedidosPendientes = $user->pedidos()->where('estadoPedido', 0)->count(); // 0 = pendiente
        $pedidosCompletados = $user->pedidos()->where('estadoPedido', 3)->count(); // 3 = entregado

        return view('dashboard.cliente', compact(
            'clienteNatural',
            'clienteEstablecimiento',
            'totalPedidos',
            'pedidosPendientes',
            'pedidosCompletados'
        ));
    }

    /**
     * ✅ CORREGIDO: Historial de pedidos del cliente - SOLO SUS PROPIOS PEDIDOS
     */
    public function historial()
    {
        $user = Auth::user();

        // ✅ CORREGIDO: Usar la relación pedidos() del modelo User
        // Esto garantiza que SOLO vea sus propios pedidos
        $pedidos = $user->pedidos()
            ->with(['clienteNatural', 'clienteEstablecimiento', 'detalleVentas.producto', 'detalleVentas.talla'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // ✅ CORREGIDO: Estadísticas usando la misma relación segura
        $estadisticas = (object) [
            'total_pedidos' => $user->pedidos()->count(),
            'pedidos_completados' => $user->pedidos()->where('estadoPedido', 3)->count(), // 3 = entregado
            'pedidos_activos' => $user->pedidos()->whereIn('estadoPedido', [0, 1, 2])->count(), // 0,1,2 = pendiente, proceso, completado
            'total_gastado' => $user->pedidos()->sum('total')
        ];

        return view('rolCliente.historial', compact('pedidos', 'estadisticas'));
    }

    /**
     * ✅ CORREGIDO: Mostrar detalles de un pedido específico - SOLO SI ES SUYO
     */
    public function detallePedido($idVenta)
    {
        $user = Auth::user();

        // ✅ CORREGIDO: Usar la relación pedidos() para verificar propiedad
        $venta = $user->pedidos()
            ->where('idVenta', $idVenta)
            ->with(['detalleVentas.producto', 'detalleVentas.talla', 'transacciones'])
            ->firstOrFail();

        return view('rolCliente.detalle-pedido', compact('venta'));
    }

    /**
     * Método adicional para obtener estadísticas en tiempo real
     */
    public function getEstadisticas()
    {
        $user = Auth::user();
        
        $estadisticas = [
            'total_pedidos' => $user->pedidos()->count(),
            'pedidos_pendientes' => $user->pedidos()->where('estadoPedido', 0)->count(),
            'pedidos_proceso' => $user->pedidos()->where('estadoPedido', 1)->count(),
            'pedidos_completados' => $user->pedidos()->where('estadoPedido', 3)->count(),
            'total_gastado' => $user->pedidos()->sum('total'),
            'saldo_pendiente' => $user->pedidos()->sum('saldo')
        ];

        return response()->json($estadisticas);
    }

    /**
     * Actualizar perfil del cliente
     */
    public function actualizarPerfil(Request $request)
    {
        $user = Auth::user();
        
        // Validar datos según el tipo de cliente
        if ($user->clienteNatural) {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'apellido_paterno' => 'required|string|max:255',
                'apellido_materno' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:20',
                'email' => 'required|email|unique:users,email,' . $user->idUser . ',idUser',
            ]);
            
            $cliente = $user->clienteNatural;
            $cliente->update($request->only(['nombre', 'apellido_paterno', 'apellido_materno', 'telefono']));
        } elseif ($user->clienteEstablecimiento) {
            $request->validate([
                'nombre_establecimiento' => 'required|string|max:255',
                'nit' => 'required|string|max:20',
                'direccion' => 'required|string|max:500',
                'telefono' => 'required|string|max:20',
                'email' => 'required|email|unique:users,email,' . $user->idUser . ',idUser',
            ]);
            
            $establecimiento = $user->clienteEstablecimiento;
            $establecimiento->update($request->only(['nombre_establecimiento', 'nit', 'direccion', 'telefono']));
        }
        
        // Actualizar email del usuario
        $user->update(['email' => $request->email]);
        
        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }
    public function perfil()
    {
        $user = Auth::user();
        $clienteNatural = $user->clienteNatural;
        $clienteEstablecimiento = $user->clienteEstablecimiento;

        return view('rolCliente.perfil', compact('clienteNatural', 'clienteEstablecimiento'));
    }

    /**
     * Vista de consulta general
     */
    public function consulta()
    {
        return view('rolCliente.consulta');
    }
}