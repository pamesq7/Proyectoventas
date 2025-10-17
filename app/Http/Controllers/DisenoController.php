<?php

namespace App\Http\Controllers;

use App\Models\Diseno;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DisenoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Mostrar los diseños del diseñador actual
     */
    public function misDisenos(Request $request)
    {
        // Obtener el ID del empleado asociado al usuario autenticado
        $idEmpleado = auth()->user()->empleado->idEmpleado;

        $query = Diseno::with('empleado')
            ->where('idEmpleado', $idEmpleado);

        // Filtro por estado del diseño
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $disenos = Diseno::where('idEmpleado', auth()->user()->empleado->idEmpleado)
            ->latest()
            ->paginate(10);

        return view('disenos.mis-disenos', compact('disenos'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Diseno::with([
            'empleado.user',
            'detalleVenta.venta.clienteNatural.user',
            'detalleVenta.venta.clienteEstablecimiento.representante'
        ]);

        // Filtro por estado del diseño
        if ($request->filled('estadoDiseno')) {
            $query->where('estadoDiseño', $request->estadoDiseno);
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Búsqueda por comentario
        if ($request->filled('buscar')) {
            $query->where('comentario', 'like', '%' . $request->buscar . '%');
        }

        $disenos = $query->orderBy('created_at', 'desc')->paginate(12);

        // Estados disponibles para filtro
        $estadosDiseno = ['no realizado', 'en proceso', 'terminado'];
        // Agregar numeración consecutiva
        $contador = ($disenos->currentPage() - 1) * $disenos->perPage() + 1;
        $disenos->each(function ($diseno) use (&$contador) {
            $diseno->contador = $contador++;
        });


        return view('disenos.index', compact('disenos', 'estadosDiseno'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $empleados = Empleado::where('estado', 1)->get();
        return view('disenos.create', compact('empleados'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'comentario' => 'nullable|string|max:45',
            'estado' => 'required|integer|in:0,1',
            'idDiseñador' => 'nullable|integer',
            'estadoDiseño' => 'required|in:no realizado,en proceso,terminado',
            'idEmpleado' => 'nullable|exists:empleados,idEmpleado',
            'archivo' => 'nullable|file|mimes:svg,ai,psd,pdf,zip,jpg,png|max:10240'
        ], [
            'estado.required' => 'El estado es obligatorio.',
            'estadoDiseño.required' => 'El estado del diseño es obligatorio.',
            'idEmpleado.exists' => 'El empleado seleccionado no existe.',
            'archivo.mimes' => 'El archivo debe ser de tipo: svg, ai, psd, pdf, zip, jpg, png.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $archivoPath = null;

            // Manejar subida de archivo
            if ($request->hasFile('archivo')) {
                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . Str::slug($request->comentario ?? 'diseno') . '.' . $archivo->getClientOriginalExtension();
                $archivoPath = $archivo->storeAs('disenos_personalizados', $nombreArchivo, 'public');
            }

            Diseno::create([
                'archivo' => $archivoPath,
                'comentario' => $request->comentario,
                'estado' => $request->estado,
                'idDiseñador' => $request->idDiseñador,
                'estadoDiseño' => $request->estadoDiseño,
                'iddetalleVenta' => $request->iddetalleVenta, // nullable
                'idEmpleado' => $request->idEmpleado
            ]);

            return redirect()->route('disenos.index')
                ->with('success', 'Diseño creado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al crear el diseño: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Diseno $diseno)
    {
        try {
            // Verificar que el diseño pertenece al diseñador autenticado (si es diseñador)
            if (auth()->user()->empleado && $diseno->idEmpleado !== auth()->user()->empleado->idEmpleado) {
                // Si no es administrador, no permitir acceso a diseños de otros
                if (auth()->user()->rol !== 'administrador') {
                    abort(403, 'No tienes permiso para ver este diseño.');
                }
            }

            $diseno->load('empleado');
            return view('disenos.show', compact('diseno'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cargar el diseño: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diseno $diseno)
    {
        try {
            // Verificar que el diseño pertenece al diseñador autenticado (si es diseñador)
            if (auth()->user()->empleado && $diseno->idEmpleado !== auth()->user()->empleado->idEmpleado) {
                // Si no es administrador, no permitir editar diseños de otros
                if (auth()->user()->rol !== 'administrador') {
                    abort(403, 'No tienes permiso para editar este diseño.');
                }
            }

            // Solo permitir editar si no está completado
            if ($diseno->estadoDiseño === 'completado') {
                return redirect()->back()
                    ->with('warning', 'No se puede editar un diseño completado.');
            }

            $empleados = Empleado::where('estado', 1)->get();
            return view('disenos.edit', compact('diseno', 'empleados'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diseno $diseno)
    {
        $validator = Validator::make($request->all(), [
            'comentario' => 'nullable|string|max:45',
            'estado' => 'required|integer|in:0,1',
            'idDiseñador' => 'nullable|integer',
            'estadoDiseño' => 'required|in:no realizado,en proceso,terminado',
            'idEmpleado' => 'nullable|exists:empleados,idEmpleado',
            'archivo' => 'nullable|file|mimes:svg,ai,psd,pdf,zip,jpg,png|max:10240'
        ], [
            'estado.required' => 'El estado es obligatorio.',
            'estadoDiseño.required' => 'El estado del diseño es obligatorio.',
            'idEmpleado.exists' => 'El empleado seleccionado no existe.',
            'archivo.mimes' => 'El archivo debe ser de tipo: svg, ai, psd, pdf, zip, jpg, png.',
            'archivo.max' => 'El archivo no puede ser mayor a 10MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Manejar subida de archivo
            if ($request->hasFile('archivo')) {
                // Eliminar archivo anterior si existe
                if ($diseno->archivo) {
                    Storage::disk('public')->delete($diseno->archivo);
                }

                $archivo = $request->file('archivo');
                $nombreArchivo = time() . '_' . Str::slug($request->comentario ?? 'diseno') . '.' . $archivo->getClientOriginalExtension();
                $archivoPath = $archivo->storeAs('disenos_personalizados', $nombreArchivo, 'public');
                $diseno->archivo = $archivoPath;
            }

            $diseno->update([
                'comentario' => $request->comentario,
                'estado' => $request->estado,
                'idDiseñador' => $request->idDiseñador,
                'estadoDiseño' => $request->estadoDiseño,
                'iddetalleVenta' => $request->iddetalleVenta,
                'idEmpleado' => $request->idEmpleado
            ]);

            return redirect()->route('disenos.index')
                ->with('success', 'Diseño actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el diseño: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * API: Obtener diseños terminados para vincular con productos
     */
    public function getDisenosTerminados()
    {
        try {
            $disenos = Diseno::with('empleado')
                ->where('estadoDiseño', 'terminado')
                ->where('estado', 1) // Solo activos
                ->orderBy('created_at', 'desc')
                ->get();

            // Asegurar que la respuesta tenga la estructura correcta
            return response()->json($disenos->map(function ($diseno) {
                return [
                    'idDiseno' => $diseno->idDiseno, // o 'id' si la PK se llama id
                    'comentario' => $diseno->comentario,
                    'archivo' => $diseno->archivo,
                    'estadoDiseño' => $diseno->estadoDiseño,
                    'empleado' => $diseno->empleado ? [
                        'id' => $diseno->empleado->idEmpleado,
                        'nombre' => $diseno->empleado->nombre
                    ] : null,
                    'created_at' => $diseno->created_at
                ];
            }));
        } catch (\Exception $e) {
            \Log::error('Error en getDisenosTerminados: ' . $e->getMessage());
            return response()->json(['error' => 'Error al cargar diseños terminados'], 500);
        }
    }
}
