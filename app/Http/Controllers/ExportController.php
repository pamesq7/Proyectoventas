<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empleado;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Exportar lista de usuarios a PDF
     */
    public function exportarUsuarios(Request $request)
    {
        $query = User::query();

        // Aplicar filtros si existen
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('ci', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $usuarios = $query->orderBy('name')->get();

        $data = [
            'usuarios' => $usuarios,
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'total_usuarios' => $usuarios->count(),
            'filtros_aplicados' => $this->obtenerFiltrosAplicados($request)
        ];

        $pdf = Pdf::loadView('exports.usuarios-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('usuarios_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar lista de empleados a PDF
     */
    public function exportarEmpleados(Request $request)
    {
        $query = Empleado::with('user');

        // Aplicar filtros si existen
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('cargo', 'like', "%{$search}%")
                  ->orWhere('rol', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        $empleados = $query->orderBy('created_at', 'desc')->get();

        // Calcular estadísticas
        $estadisticas = [
            'total' => $empleados->count(),
            'activos' => $empleados->where('estado', 1)->count(),
            'inactivos' => $empleados->where('estado', 0)->count(),
            'por_rol' => $empleados->groupBy('rol')->map->count()
        ];

        $data = [
            'empleados' => $empleados,
            'estadisticas' => $estadisticas,
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'filtros_aplicados' => $this->obtenerFiltrosAplicados($request)
        ];

        $pdf = Pdf::loadView('exports.empleados-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('empleados_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar lista de clientes naturales a PDF
     */
    public function exportarClientesNaturales(Request $request)
    {
        $query = ClienteNatural::with('user');

        // Aplicar filtros si existen
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%")
                         ->orWhere('ci', 'like', "%{$search}%")
                         ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $clientes = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'clientes' => $clientes,
            'tipo_cliente' => 'Naturales',
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'total_clientes' => $clientes->count(),
            'filtros_aplicados' => $this->obtenerFiltrosAplicados($request)
        ];

        $pdf = Pdf::loadView('exports.clientes-naturales-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('clientes_naturales_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar lista de clientes establecimientos a PDF
     */
    public function exportarClientesEstablecimientos(Request $request)
    {
        $query = ClienteEstablecimiento::query();

        // Aplicar filtros si existen
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('razonSocial', 'like', "%{$search}%")
                  ->orWhere('nit', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $clientes = $query->orderBy('created_at', 'desc')->get();

        $data = [
            'clientes' => $clientes,
            'tipo_cliente' => 'Establecimientos',
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'total_clientes' => $clientes->count(),
            'filtros_aplicados' => $this->obtenerFiltrosAplicados($request)
        ];

        $pdf = Pdf::loadView('exports.clientes-establecimientos-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('clientes_establecimientos_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Exportar reporte consolidado de todos los clientes
     */
    public function exportarClientesConsolidado(Request $request)
    {
        $clientes_naturales = ClienteNatural::with('user')->where('estado', true)->get();
        $clientes_establecimientos = ClienteEstablecimiento::where('estado', true)->get();
        
        $estadisticas = [
            'total_naturales' => $clientes_naturales->count(),
            'total_establecimientos' => $clientes_establecimientos->count(),
            'total_general' => $clientes_naturales->count() + $clientes_establecimientos->count()
        ];

        $data = [
            'clientes_naturales' => $clientes_naturales,
            'clientes_establecimientos' => $clientes_establecimientos,
            'estadisticas' => $estadisticas,
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        $pdf = Pdf::loadView('exports.clientes-consolidado-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('reporte-consolidado-clientes-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar lista de productos a PDF
     */
    public function exportarProductos(Request $request)
    {
        $query = \App\Models\Producto::query();
        
        // Aplicar filtros si existen
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('codigo', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $productos = $query->with(['categoria', 'diseno', 'variante'])->get();
        
        // Estadísticas
        $estadisticas = [
            'total_productos' => \App\Models\Producto::count(),
            'productos_activos' => \App\Models\Producto::where('estado', true)->count(),
            'productos_inactivos' => \App\Models\Producto::where('estado', false)->count(),
            'con_stock' => \App\Models\Producto::where('stock', '>', 0)->count(),
            'sin_stock' => \App\Models\Producto::where('stock', '<=', 0)->count(),
            'categorias_total' => \App\Models\Categoria::count()
        ];

        $data = [
            'productos' => $productos,
            'estadisticas' => $estadisticas,
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'filtros_aplicados' => $request->only(['buscar', 'categoria', 'estado'])
        ];

        $pdf = Pdf::loadView('exports.productos-pdf', $data);
        $pdf->setPaper('A4', 'portrait'); // Cambiar a vertical
        
        return $pdf->download('reporte-productos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar lista de diseños a PDF
     */
    public function exportarDisenos(Request $request)
    {
        $query = \App\Models\Diseno::query();
        
        // Aplicar filtros si existen
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $disenos = $query->with(['empleado.user'])->get();
        
        // Estadísticas
        $estadisticas = [
            'total_disenos' => \App\Models\Diseno::count(),
            'disenos_activos' => \App\Models\Diseno::where('estado', true)->count(),
            'disenos_inactivos' => \App\Models\Diseno::where('estado', false)->count(),
            'con_empleado' => \App\Models\Diseno::whereNotNull('idEmpleado')->count(),
        ];

        $data = [
            'disenos' => $disenos,
            'estadisticas' => $estadisticas,
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s'),
            'filtros_aplicados' => $request->only(['buscar', 'estado'])
        ];

        $pdf = Pdf::loadView('exports.disenos-pdf', $data);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('reporte-disenos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar pedidos (ventas) a PDF
     */
    public function exportarPedidos(Request $request)
    {
        // Obtener filtros de la request
        $estadoPedido = $request->get('estadoPedido');
        $estadoPedido = $request->get('estadoPedido');
        $fechaInicio = $request->get('fechaInicio');
        $fechaFin = $request->get('fechaFin');
        $idEmpleado = $request->get('idEmpleado');

        // Query base para pedidos (ventas) - solo en estado 1
        $query = Venta::with([
            'empleado.user', 
            'clienteNatural.user', 
            'clienteEstablecimiento', 
            'detalleVentas.talla'
        ])->where('estado', 1);

        // Aplicar filtros adicionales
        if ($estadoPedido) {
            $query->where('estadoPedido', $estadoPedido);
        }

        if ($estadoPedido) {
            if ($estadoPedido === 'pagado') {
                $query->where('saldo', 0);
            } elseif ($estadoPedido === 'pendiente') {
                $query->where('saldo', '>', 0);
            }
        }

        if ($fechaInicio) {
            $query->whereDate('created_at', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }

        if ($idEmpleado) {
            $query->where('idEmpleado', $idEmpleado);
        }

        $pedidos = $query->orderBy('created_at', 'desc')->get();

        // Estadísticas
        $totalPedidos = $pedidos->count();
        $pedidosPendientes = $pedidos->where('estadoPedido', 'pendiente')->count();
        $pedidosEnProceso = $pedidos->where('estadoPedido', 'en proceso')->count();
        $pedidosCompletados = $pedidos->where('estadoPedido', 'completado')->count();
        $pedidosCancelados = $pedidos->where('estadoPedido', 'cancelado')->count();
        
        $totalVentas = $pedidos->sum('total');
        $saldoPendiente = $pedidos->sum('saldo');
        $totalPagado = $totalVentas - $saldoPendiente;

        $estadisticas = [
            'total_pedidos' => $totalPedidos,
            'pedidos_pendientes' => $pedidosPendientes,
            'pedidos_en_proceso' => $pedidosEnProceso,
            'pedidos_completados' => $pedidosCompletados,
            'pedidos_cancelados' => $pedidosCancelados,
            'total_ventas' => $totalVentas,
            'total_pagado' => $totalPagado,
            'saldo_pendiente' => $saldoPendiente
        ];

        $pdf = PDF::loadView('exports.pedidos-pdf', compact('pedidos', 'estadisticas'));
        return $pdf->download('pedidos_' . date('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Obtener filtros aplicados para mostrar en el PDF
     */
    private function obtenerFiltrosAplicados(Request $request)
    {
        $filtros = [];
        
        if ($request->filled('search')) {
            $filtros[] = "Búsqueda: " . $request->search;
        }
        
        if ($request->filled('estado')) {
            $estado = $request->estado == '1' ? 'Activo' : 'Inactivo';
            $filtros[] = "Estado: " . $estado;
        }
        
        if ($request->filled('rol')) {
            $filtros[] = "Rol: " . ucfirst($request->rol);
        }
        
        return $filtros;
    }
}
