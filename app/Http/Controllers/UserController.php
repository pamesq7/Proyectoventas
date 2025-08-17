<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;
use App\Models\Empleado;

class UserController extends Controller
{
    /**
     * Display a listing of the resource with user types.
     */
    public function index()
    {
        $users = User::with(['clienteNatural', 'clienteEstablecimiento', 'empleado'])
                    ->where('estado', 1)
                    ->orderBy('name', 'asc')
                    ->get();
        
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log de datos recibidos
        Log::info('UserController::store - Datos recibidos:', [
            'tipo_usuario' => $request->tipo_usuario,
            'all_data' => $request->except(['password', 'password_confirmation'])
        ]);
        
        // Validación base para todos los usuarios
        $validator = Validator::make($request->all(), [
            'tipo_usuario' => 'required|in:cliente_natural,cliente_establecimiento,empleado',
            'ci' => 'required|string|max:20|unique:users,ci',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundApellido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Validaciones específicas según tipo de usuario
        if ($request->tipo_usuario === 'cliente_natural') {
            $validator->addRules([
                'nit_cliente' => 'nullable|string|max:20',
            ]);
        } elseif ($request->tipo_usuario === 'cliente_establecimiento') {
            $validator->addRules([
                'nit_establecimiento' => 'required|string|max:20|unique:cliente_establecimientos,nit',
                'razonSocial' => 'required|string|max:255',
                'tipoEstablecimiento' => 'required|string|in:Empresa Privada,Institución Pública,ONG,Cooperativa,Otro',
                'domicilioFiscal' => 'required|string|max:500',
            ]);
        } elseif ($request->tipo_usuario === 'empleado') {
            $validator->addRules([
                'cargo' => 'required|string|max:45',
                'rol' => 'required|string|in:administrador,diseñador,operador,cliente,vendedor',
            ]);
        }

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear usuario base
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

            // Crear registro específico según tipo
            switch ($request->tipo_usuario) {
                case 'cliente_natural':
                    ClienteNatural::create([
                        'idCliente' => $user->idUser,
                        'nit' => $request->nit_cliente,
                        'estado' => 1,
                    ]);
                    break;

                case 'cliente_establecimiento':
                    ClienteEstablecimiento::create([
                        'nit' => $request->nit_establecimiento,
                        'razonSocial' => $request->razonSocial,
                        'tipoEstablecimiento' => $request->tipoEstablecimiento,
                        'domicilioFiscal' => $request->domicilioFiscal,
                        'idRepresentante' => $user->idUser, // Usando idUser como clave foránea
                        'estado' => 1,
                    ]);
                    break;

                case 'empleado':
                    Empleado::create([
                        'idEmpleado' => $user->idUser,
                        'cargo' => $request->cargo,
                        'rol' => $request->rol,
                        'estado' => 1,
                    ]);
                    break;
            }

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'Usuario creado exitosamente como ' . str_replace('_', ' ', $request->tipo_usuario) . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear usuario:', [
                'tipo_usuario' => $request->tipo_usuario,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);
            
            return redirect()->back()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['clienteNatural', 'clienteEstablecimiento', 'empleado']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $user->load(['clienteNatural', 'clienteEstablecimiento', 'empleado']);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Validación básica
        $validator = Validator::make($request->all(), [
            'ci' => 'required|string|max:20|unique:users,ci,' . $user->idUser . ',idUser',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundApellido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->idUser . ',idUser',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'estado' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Actualizar datos del usuario
            $userData = [
                'ci' => $request->ci,
                'name' => $request->name,
                'primerApellido' => $request->primerApellido,
                'segundApellido' => $request->segundApellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'estado' => $request->estado,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $user->update($userData);

            return redirect()->route('users.index')
                ->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(User $user)
    {
        try {
            // Eliminación lógica: cambiar estado a inactivo
            $user->update(['estado' => 0]);

            // También desactivar en las tablas relacionadas
            if ($user->clienteNatural) {
                $user->clienteNatural->update(['estado' => 0]);
            }
            if ($user->clienteEstablecimiento) {
                $user->clienteEstablecimiento->update(['estado' => 0]);
            }
            if ($user->empleado) {
                $user->empleado->update(['estado' => 0]);
            }

            return redirect()->route('users.index')
                ->with('successdelete', 'Usuario eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('users.index')
                ->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Get user type for display.
     */
    public function getUserType(User $user)
    {
        if ($user->clienteNatural) {
            return 'Cliente Natural';
        } elseif ($user->clienteEstablecimiento) {
            return 'Cliente Establecimiento';
        } elseif ($user->empleado) {
            return 'Empleado';
        }
        return 'Usuario Base';
    }
}
