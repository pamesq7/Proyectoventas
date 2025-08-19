<?php

namespace App\Http\Controllers;

use App\Models\Diseno;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DisenoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Diseno::with('empleado');

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
                $archivoPath = $archivo->storeAs('disenos', $nombreArchivo, 'public');
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
        $diseno->load('empleado');
        return view('disenos.show', compact('diseno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diseno $diseno)
    {
        $empleados = Empleado::where('estado', 1)->get();
        return view('disenos.edit', compact('diseno', 'empleados'));
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
                $archivoPath = $archivo->storeAs('disenos', $nombreArchivo, 'public');
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
     * Remove the specified resource from storage.
     */
    public function destroy(Diseno $diseno)
    {
        try {
            // Eliminar archivo asociado
            if ($diseno->archivo && Storage::disk('public')->exists($diseno->archivo)) {
                Storage::disk('public')->delete($diseno->archivo);
            }

            $diseno->delete();

            return redirect()->route('disenos.index')
                ->with('success', 'Diseño eliminado exitosamente.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar el diseño: ' . $e->getMessage());
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

            return response()->json($disenos);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cargar diseños terminados'], 500);
        }
    }

}
