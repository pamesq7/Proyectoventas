<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Empleado;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
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
        $clientesNaturales = ClienteNatural::with('user')->where('estado', 1)->get();
        $clientesEstablecimientos = ClienteEstablecimiento::where('estado', 1)->get();

        $estadisticas = [
            'total_naturales' => $clientesNaturales->count(),
            'total_establecimientos' => $clientesEstablecimientos->count(),
            'total_general' => $clientesNaturales->count() + $clientesEstablecimientos->count()
        ];

        $data = [
            'clientes_naturales' => $clientesNaturales,
            'clientes_establecimientos' => $clientesEstablecimientos,
            'estadisticas' => $estadisticas,
            'fecha_generacion' => Carbon::now()->format('d/m/Y H:i:s')
        ];

        $pdf = Pdf::loadView('exports.clientes-consolidado-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('clientes_consolidado_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf');
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
