<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class EmpleadoController extends Controller
{
    /**
     * Mostrar lista de empleados
     */
    public function index(Request $request)
    {
        $query = Empleado::with('user');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('cargo', 'like', "%{$search}%")
                    ->orWhere('rol', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('primerApellido', 'like', "%{$search}%");
                    });
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por rol
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        $empleados = $query->orderBy('created_at', 'desc')->paginate(10);

        // Estadísticas
        $estadisticas = [
            'total' => Empleado::count(),
            'activos' => Empleado::where('estado', 1)->count(),
            'inactivos' => Empleado::where('estado', 0)->count(),
            'administradores' => Empleado::where('rol', 'administrador')->count(),
            'vendedores' => Empleado::where('rol', 'vendedor')->count(),
        ];

        return view('empleados.index', compact('empleados', 'estadisticas'));
    }

    /**
     * Mostrar formulario para crear empleado
     */
    public function create()
    {
        // Usuarios que no son empleados
        $usuariosDisponibles = User::whereNotIn('idUser', function ($query) {
            $query->select('idEmpleado')->from('empleados');
        })->get();

        $roles = ['administrador', 'diseñador', 'operador', 'cliente', 'vendedor'];

        return view('empleados.create', compact('usuariosDisponibles', 'roles'));
    }

    /**
     * Guardar nuevo empleado
     */
    public function store(Request $request)
    {
        // Primero validar datos del usuario
        $userValidator = Validator::make($request->all(), [
            'ci' => 'required|string|max:255|unique:users,ci',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Validar datos del empleado
        $empleadoValidator = Validator::make($request->all(), [
            'cargo' => 'required|string|max:45',
            'rol' => 'required|in:administrador,diseñador,operador,cliente,vendedor',
        ]);

        if ($userValidator->fails() || $empleadoValidator->fails()) {
            return redirect()->back()
                ->withErrors(array_merge(
                    $userValidator->errors()->toArray(),
                    $empleadoValidator->errors()->toArray()
                ))
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // 1. PRIMERO crear el User
            $user = User::create([
                'ci' => $request->ci,
                'name' => $request->name,
                'primerApellido' => $request->primerApellido,
                'segundApellido' => $request->segundApellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password),
                'estado' => 1,
            ]);

            // 2. LUEGO crear el Empleado usando el ID del User como idEmpleado
            $empleado = Empleado::create([
                'idEmpleado' => $user->idUser, // ← ESTA ES LA CLAVE
                'cargo' => $request->cargo,
                'rol' => $request->rol,
                'estado' => 1,
            ]);

            DB::commit();

            return redirect()->route('empleados.index')
                ->with('success', 'Empleado creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el empleado: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Mostrar empleado específico
     */
    public function show(Empleado $empleado)
    {
        $empleado->load('user', 'ventas', 'disenos');

        // Estadísticas del empleado
        $estadisticas = [
            'total_ventas' => $empleado->ventas->count(),
            'monto_ventas' => $empleado->ventas->sum('total'),
            'ventas_mes' => $empleado->ventas()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'total_disenos' => $empleado->disenos->count(),
        ];

        return view('empleados.show', compact('empleado', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Empleado $empleado)
    {
        $empleado->load('user');
        $roles = ['administrador', 'diseñador', 'operador', 'cliente', 'vendedor'];

        return view('empleados.edit', compact('empleado', 'roles'));
    }

    /**
     * Actualizar empleado
     */
    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'cargo' => 'required|string|max:45',
            'rol' => 'required|in:administrador,diseñador,operador,cliente,vendedor',
            'estado' => 'boolean'
        ]);

        try {
            $empleado->update([
                'cargo' => $request->cargo,
                'rol' => $request->rol,
                'estado' => $request->has('estado') ? 1 : 0,
            ]);

            return redirect()->route('empleados.index')
                ->with('success', 'Empleado actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar empleado: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar empleado
     */
    public function destroy(Empleado $empleado)
    {
        try {
            // Verificar si tiene ventas asociadas
            if ($empleado->ventas()->count() > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar el empleado porque tiene ventas asociadas.');
            }

            $empleado->delete();

            return redirect()->route('empleados.index')
                ->with('success', 'Empleado eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar empleado: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del empleado (activo/inactivo)
     */
    public function toggleEstado(Empleado $empleado)
    {
        try {
            $empleado->update([
                'estado' => !$empleado->estado
            ]);

            $estado = $empleado->estado ? 'activado' : 'desactivado';

            return redirect()->back()
                ->with('success', "Empleado {$estado} exitosamente.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Estadísticas del empleado
     */
    public function estadisticas(Empleado $empleado)
    {
        $empleado->load('user');

        $estadisticas = [
            'ventas_totales' => $empleado->ventas()->count(),
            'monto_total_ventas' => $empleado->ventas()->sum('total'),
            'ventas_este_mes' => $empleado->ventas()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'monto_este_mes' => $empleado->ventas()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'disenos_creados' => $empleado->disenos()->count(),
            'promedio_venta' => $empleado->ventas()->avg('total'),
        ];

        // Ventas por mes (últimos 6 meses)
        $ventasPorMes = $empleado->ventas()
            ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total, SUM(total) as monto')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        return response()->json([
            'empleado' => $empleado,
            'estadisticas' => $estadisticas,
            'ventas_por_mes' => $ventasPorMes
        ]);
    }
}
