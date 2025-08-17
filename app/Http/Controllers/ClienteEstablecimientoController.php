<?php

namespace App\Http\Controllers;

use App\Models\ClienteEstablecimiento;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClienteEstablecimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener establecimientos ACTIVOS (independientemente del estado del representante)
        $clientesEstablecimientos = ClienteEstablecimiento::with(['representante', 'ventas'])
            ->where('estado', 1)
            ->get()
            ->map(function ($cliente) {
                $cliente->total_ventas = $cliente->ventas->count();
                $cliente->monto_total_ventas = $cliente->ventas->sum('total');
                $cliente->ultima_venta = $cliente->ventas->max('created_at');
                return $cliente;
            });

        // Estadísticas generales
        $estadisticas = [
            'total_establecimientos' => $clientesEstablecimientos->count(),
            'establecimientos_activos' => $clientesEstablecimientos->count(),
            'establecimientos_inactivos' => ClienteEstablecimiento::where('estado', 0)->count(),
            'representantes_inactivos' => $clientesEstablecimientos->where('representante.estado', 0)->count(),
            'total_ventas' => $clientesEstablecimientos->sum('total_ventas'),
            'monto_total' => $clientesEstablecimientos->sum('monto_total_ventas'),
            'tipos_establecimiento' => $clientesEstablecimientos->groupBy('tipoEstablecimiento')->map->count(),
        ];

        return view('clientes.establecimientos.index', compact('clientesEstablecimientos', 'estadisticas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('clientes.establecimientos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            // Datos del representante
            'ci' => 'required|string|max:20|unique:users,ci',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundApellido' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'telefono' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            // Datos del establecimiento
            'nit_establecimiento' => 'required|string|max:20|unique:cliente_establecimientos,nit',
            'razonSocial' => 'required|string|max:255',
            'tipoEstablecimiento' => 'required|string|in:Empresa Privada,Institución Pública,ONG,Cooperativa,Otro',
            'domicilioFiscal' => 'required|string|max:500',
        ], [
            // Mensajes para representante
            'ci.required' => 'La cédula de identidad del representante es obligatoria.',
            'ci.unique' => 'Ya existe un usuario con esta cédula de identidad.',
            'name.required' => 'El nombre del representante es obligatorio.',
            'primerApellido.required' => 'El primer apellido del representante es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            // Mensajes para establecimiento
            'nit_establecimiento.required' => 'El NIT del establecimiento es obligatorio.',
            'nit_establecimiento.unique' => 'Ya existe un establecimiento con este NIT.',
            'razonSocial.required' => 'La razón social es obligatoria.',
            'tipoEstablecimiento.required' => 'El tipo de establecimiento es obligatorio.',
            'tipoEstablecimiento.in' => 'El tipo de establecimiento seleccionado no es válido.',
            'domicilioFiscal.required' => 'El domicilio fiscal es obligatorio.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear usuario representante
            $representante = User::create([
                'ci' => $request->ci,
                'name' => $request->name,
                'primerApellido' => $request->primerApellido,
                'segundApellido' => $request->segundApellido,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'password' => Hash::make($request->password),
                'estado' => 1,
            ]);

            // Crear cliente establecimiento
            ClienteEstablecimiento::create([
                'nit' => $request->nit_establecimiento,
                'razonSocial' => $request->razonSocial,
                'tipoEstablecimiento' => $request->tipoEstablecimiento,
                'domicilioFiscal' => $request->domicilioFiscal,
                'idRepresentante' => $representante->idUser,
                'estado' => 1,
            ]);

            DB::commit();

            return redirect()->route('clienteEstablecimiento.index')
                ->with('success', 'Cliente establecimiento "' . $request->razonSocial . '" registrado exitosamente. El representante legal puede acceder al sistema con su email y contraseña.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al crear el establecimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ClienteEstablecimiento $clienteEstablecimiento)
    {
        // Cargar relaciones
        $clienteEstablecimiento->load(['representante', 'ventas.detalleVentas.producto']);

        // Estadísticas del establecimiento
        $estadisticas = [
            'total_ventas' => $clienteEstablecimiento->ventas->count(),
            'monto_total' => $clienteEstablecimiento->ventas->sum('total'),
            'venta_promedio' => $clienteEstablecimiento->ventas->count() > 0 ? $clienteEstablecimiento->ventas->avg('total') : 0,
            'ultima_venta' => $clienteEstablecimiento->ventas->max('created_at'),
            'productos_comprados' => $clienteEstablecimiento->ventas->flatMap->detalleVentas->pluck('producto.nombre')->unique()->count(),
            'ventas_este_mes' => $clienteEstablecimiento->ventas()->whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count(),
        ];

        // Últimas ventas
        $ultimasVentas = $clienteEstablecimiento->ventas()
            ->with(['detalleVentas.producto'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('clientes.establecimientos.show', compact('clienteEstablecimiento', 'estadisticas', 'ultimasVentas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ClienteEstablecimiento $clienteEstablecimiento)
    {
        $clienteEstablecimiento->load('representante');
        return view('clientes.establecimientos.edit', compact('clienteEstablecimiento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ClienteEstablecimiento $clienteEstablecimiento)
    {
        // Validación de datos (igual que ClienteNatural)
        $validator = Validator::make($request->all(), [
            'ci' => 'required|string|max:20|unique:users,ci,' . $clienteEstablecimiento->representante->idUser . ',idUser',
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundApellido' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $clienteEstablecimiento->representante->idUser . ',idUser',
            'telefono' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'nit_establecimiento' => 'required|numeric|digits_between:7,15|unique:cliente_establecimientos,nit,' . $clienteEstablecimiento->idEstablecimiento . ',idEstablecimiento',
            'razonSocial' => 'required|string|max:255',
            'tipoEstablecimiento' => 'required|string|in:Empresa Privada,Institución Pública,ONG,Cooperativa,Otro',
            'domicilioFiscal' => 'required|string|max:500',
            'estado' => 'required|boolean',
            'estado_establecimiento' => 'required|boolean',
        ], [
            'ci.required' => 'La cédula de identidad del representante es obligatoria.',
            'ci.unique' => 'Ya existe un usuario con esta cédula de identidad.',
            'name.required' => 'El nombre del representante es obligatorio.',
            'primerApellido.required' => 'El primer apellido del representante es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'Ya existe un usuario con este correo electrónico.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'nit_establecimiento.required' => 'El NIT del establecimiento es obligatorio.',
            'nit_establecimiento.unique' => 'Ya existe un establecimiento con este NIT.',
            'razonSocial.required' => 'La razón social es obligatoria.',
            'tipoEstablecimiento.required' => 'El tipo de establecimiento es obligatorio.',
            'tipoEstablecimiento.in' => 'El tipo de establecimiento seleccionado no es válido.',
            'domicilioFiscal.required' => 'El domicilio fiscal es obligatorio.',
            'estado.required' => 'El estado del representante es obligatorio.',
            'estado_establecimiento.required' => 'El estado del establecimiento es obligatorio.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Actualizar representante (igual que ClienteNatural)
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

            $clienteEstablecimiento->representante->update($userData);

            // Actualizar establecimiento
            $clienteEstablecimiento->update([
                'nit' => $request->nit_establecimiento,
                'razonSocial' => $request->razonSocial,
                'tipoEstablecimiento' => $request->tipoEstablecimiento,
                'domicilioFiscal' => $request->domicilioFiscal,
                'estado' => $request->estado_establecimiento,
            ]);

            DB::commit();

            return redirect()->route('clienteEstablecimiento.index')
                ->with('success', 'Los datos del establecimiento "' . $request->razonSocial . '" han sido actualizados correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar el establecimiento: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ClienteEstablecimiento $clienteEstablecimiento)
    {
        try {
            // Verificar si tiene ventas
            $ventasActivas = $clienteEstablecimiento->ventas()->count();
            if ($ventasActivas > 0) {
                // Marcar como inactivo pero informar sobre las ventas
                DB::beginTransaction();
                $clienteEstablecimiento->update(['estado' => 0]);
                $clienteEstablecimiento->representante->update(['estado' => 0]);
                DB::commit();
                
                return redirect()->route('clienteEstablecimiento.index')
                    ->with('warning', 'El establecimiento "' . $clienteEstablecimiento->razonSocial . '" ha sido desactivado. No se puede eliminar completamente porque tiene ' . $ventasActivas . ' venta(s) asociada(s).');
            }

            DB::beginTransaction();

            // Eliminación lógica: marcar como inactivo
            $clienteEstablecimiento->update(['estado' => 0]);
            $clienteEstablecimiento->representante->update(['estado' => 0]);

            DB::commit();

            return redirect()->route('clienteEstablecimiento.index')
                ->with('success', 'El establecimiento "' . $clienteEstablecimiento->razonSocial . '" ha sido eliminado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('clienteEstablecimiento.index')
                ->with('error', 'Error al eliminar el establecimiento: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas del establecimiento para dashboard
     */
    public function estadisticas(ClienteEstablecimiento $clienteEstablecimiento)
    {
        $clienteEstablecimiento->load(['ventas']);

        $estadisticas = [
            'ventas_por_mes' => $clienteEstablecimiento->ventas()
                ->selectRaw('MONTH(created_at) as mes, COUNT(*) as total, SUM(total) as monto')
                ->whereYear('created_at', date('Y'))
                ->groupBy('mes')
                ->get()
                ->pluck('total', 'mes'),
            'productos_favoritos' => $clienteEstablecimiento->ventas()
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
                ->sortByDesc('total')
                ->take(5),
            'ventas_por_tipo' => $clienteEstablecimiento->ventas()
                ->selectRaw('MONTH(created_at) as mes, SUM(total) as monto')
                ->whereYear('created_at', date('Y'))
                ->groupBy('mes')
                ->pluck('monto', 'mes'),
        ];

        return response()->json($estadisticas);
    }

    /**
     * Activar/Desactivar establecimiento
     */
    public function toggleEstado(ClienteEstablecimiento $clienteEstablecimiento)
    {
        try {
            DB::beginTransaction();

            $nuevoEstado = $clienteEstablecimiento->estado == 1 ? 0 : 1;
            $clienteEstablecimiento->update(['estado' => $nuevoEstado]);
            $clienteEstablecimiento->representante->update(['estado' => $nuevoEstado]);

            DB::commit();

            $accion = $nuevoEstado == 1 ? 'activado' : 'desactivado';
            $mensaje = 'El establecimiento "' . $clienteEstablecimiento->razonSocial . '" ha sido ' . $accion . ' correctamente.';
            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener establecimientos por tipo
     */
    public function porTipo($tipo)
    {
        $establecimientos = ClienteEstablecimiento::with(['representante', 'ventas'])
            ->where('tipoEstablecimiento', $tipo)
            ->where('estado', 1)
            ->get();

        return view('clientes.establecimientos.por-tipo', compact('establecimientos', 'tipo'));
    }
}
