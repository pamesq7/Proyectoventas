<?php

namespace App\Http\Controllers;

use App\Models\ClienteNatural;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClienteNaturalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener solo clientes activos con sus usuarios activos
        $clientes = ClienteNatural::with(['user' => function($query) {
            $query->where('estado', 1);
        }])
        ->where('estado', 1)
        ->whereHas('user', function($query) {
            $query->where('estado', 1);
        })
        ->get();

        // Inicializar estadísticas básicas
        $clientes->each(function($cliente) {
            $cliente->total_ventas = 0;
            $cliente->monto_total = 0;
            $cliente->ultima_compra = null;
        });

        // Calcular estadísticas generales
        $totalClientes = ClienteNatural::count();
        $clientesActivos = $clientes->count();
        $clientesInactivos = $totalClientes - $clientesActivos;

        $estadisticas = [
            'total_clientes' => $totalClientes,
            'clientes_activos' => $clientesActivos,
            'clientes_inactivos' => $clientesInactivos,
            'total_ventas' => 0,
            'monto_total' => 0,
        ];

        return view('clientes.naturales.index', compact('clientes', 'estadisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.naturales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'ci' => 'required|string|max:20|unique:users,ci',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundApellido' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'nit' => 'nullable|string|max:20|unique:cliente_naturals,nit',
        ], [
            'ci.required' => 'La cédula de identidad es obligatoria.',
            'ci.unique' => 'Ya existe un usuario con esta cédula de identidad.',
            'name.required' => 'El nombre es obligatorio.',
            'primerApellido.required' => 'El primer apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nit.unique' => 'Ya existe un cliente con este NIT.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear usuario
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

            // Crear cliente natural
            ClienteNatural::create([
                'idCliente' => $user->idUser,
                'nit' => $request->nit,
                'estado' => 1,
            ]);

            DB::commit();

            return redirect()->route('clienteNatural.index')
                ->with('success', 'Cliente natural creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClienteNatural $clienteNatural)
    {
        // Cargar relaciones
        $clienteNatural->load(['user', 'ventas.detalleVentas.producto']);

        // Estadísticas del cliente
        $estadisticas = [
            'total_ventas' => $clienteNatural->ventas->count(),
            'monto_total' => $clienteNatural->ventas->sum('total'),
            'venta_promedio' => $clienteNatural->ventas->count() > 0 ? $clienteNatural->ventas->avg('total') : 0,
            'ultima_venta' => $clienteNatural->ventas->max('fechaVenta'),
            'productos_comprados' => $clienteNatural->ventas->flatMap->detalleVentas->pluck('producto.nombre')->unique()->count(),
        ];

        // Últimas ventas
        $ultimasVentas = $clienteNatural->ventas()
            ->with(['detalleVentas.producto'])
            ->orderBy('fechaVenta', 'desc')
            ->limit(5)
            ->get();

        return view('clientes.naturales.show', compact('clienteNatural', 'estadisticas', 'ultimasVentas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClienteNatural $clienteNatural)
    {
        $clienteNatural->load('user');
        return view('clientes.naturales.edit', compact('clienteNatural'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClienteNatural $clienteNatural)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'ci' => 'required|string|max:20|unique:users,ci,' . $clienteNatural->user->idUser . ',idUser',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundApellido' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $clienteNatural->user->idUser . ',idUser',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'nit' => 'nullable|string|max:20|unique:cliente_naturals,nit,' . $clienteNatural->idCliente . ',idCliente',
            'estado' => 'required|boolean',
            'estado_cliente' => 'required|boolean',
        ], [
            'ci.required' => 'La cédula de identidad es obligatoria.',
            'ci.unique' => 'Ya existe un usuario con esta cédula de identidad.',
            'name.required' => 'El nombre es obligatorio.',
            'primerApellido.required' => 'El primer apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nit.unique' => 'Ya existe un cliente con este NIT.',
            'estado.required' => 'El estado del usuario es obligatorio.',
            'estado_cliente.required' => 'El estado del cliente es obligatorio.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar usuario
            $userData = [
                'ci' => $request->ci,
                'name' => $request->name,
                'primerApellido' => $request->primerApellido,
                'segundApellido' => $request->segundApellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'estado' => $request->estado,
            ];

            // Solo actualizar contraseña si se proporcionó una nueva
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $clienteNatural->user->update($userData);

            // Actualizar cliente natural
            $clienteNatural->update([
                'nit' => $request->nit,
                'estado' => $request->estado_cliente,
            ]);

            DB::commit();

            return redirect()->route('clienteNatural.index')
                ->with('success', 'Cliente natural actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el cliente: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClienteNatural $clienteNatural)
    {
        try {
            // Verificar si tiene ventas
            $ventasActivas = $clienteNatural->ventas()->count();
            if ($ventasActivas > 0) {
                // Marcar como inactivo pero informar sobre las ventas
                DB::beginTransaction();
                $clienteNatural->update(['estado' => 0]);
                $clienteNatural->user->update(['estado' => 0]);
                DB::commit();
                
                return redirect()->route('clienteNatural.index')
                    ->with('warning', 'El cliente "' . $clienteNatural->user->nombre_completo . '" ha sido desactivado. No se puede eliminar completamente porque tiene ' . $ventasActivas . ' venta(s) asociada(s).');
            }

            DB::beginTransaction();

            // Eliminación lógica: marcar como inactivo
            $clienteNatural->update(['estado' => 0]);
            $clienteNatural->user->update(['estado' => 0]);

            DB::commit();

            return redirect()->route('clienteNatural.index')
                ->with('success', 'El cliente "' . $clienteNatural->user->nombre_completo . '" ha sido eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('clienteNatural.index')
                ->with('error', 'Error al eliminar el cliente: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas del cliente para dashboard
     */
    public function estadisticas(ClienteNatural $clienteNatural)
    {
        $clienteNatural->load(['ventas']);

        $estadisticas = [
            'ventas_por_mes' => $clienteNatural->ventas()
                ->selectRaw('MONTH(fechaVenta) as mes, COUNT(*) as total')
                ->whereYear('fechaVenta', date('Y'))
                ->groupBy('mes')
                ->pluck('total', 'mes'),
            'productos_favoritos' => $clienteNatural->ventas()
                ->with('detalleVentas.producto')
                ->get()
                ->flatMap->detalleVentas
                ->groupBy('idProducto')
                ->map(function ($detalles) {
                    return [
                        'producto' => $detalles->first()->producto->nombre,
                        'cantidad' => $detalles->sum('cantidad'),
                        'total' => $detalles->sum('subtotal')
                    ];
                })
                ->sortByDesc('cantidad')
                ->take(5),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Activar/Desactivar cliente
     */
    public function toggleEstado(ClienteNatural $clienteNatural)
    {
        try {
            DB::beginTransaction();

            $nuevoEstado = $clienteNatural->estado == 1 ? 0 : 1;
            $clienteNatural->update(['estado' => $nuevoEstado]);
            $clienteNatural->user->update(['estado' => $nuevoEstado]);

            DB::commit();

            $mensaje = $nuevoEstado == 1 ? 'Cliente activado exitosamente.' : 'Cliente desactivado exitosamente.';
            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}
