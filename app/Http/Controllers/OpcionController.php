<?php

namespace App\Http\Controllers;

use App\Models\Opcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpcionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $opciones = Opcion::with(['caracteristicas' => function($query) {
            $query->where('estado', 1);
        }])->orderBy('nombre', 'asc')->get();
        
        return view('configuracion.opciones.index', compact('opciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('configuracion.opciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:opcions,nombre',
            'descripcion' => 'required|string|max:45',
            'estado' => 'required|boolean'
        ]);

        try {
            Opcion::create($validated);
            
            return redirect()->route('opciones.index')
                ->with('success', 'Opción creada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la opción: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Opcion $opcion)
    {
        if (request()->ajax()) {
            $opcion->load('caracteristicas');
            return response()->json([
                'idOpcion' => $opcion->idOpcion,
                'nombre' => $opcion->nombre,
                'descripcion' => $opcion->descripcion,
                'estado' => $opcion->estado,
                'caracteristicas_count' => $opcion->caracteristicas->count()
            ]);
        }
        
        $opcion->load(['caracteristicas' => function($query) {
            $query->where('estado', 1);
        }]);
        
        return view('configuracion.opciones.show', compact('opcion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Opcion $opcion)
    {
        $opcion->load('caracteristicas');
        return view('configuracion.opciones.edit', compact('opcion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Opcion $opcion)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50|unique:opcions,nombre,' . $opcion->idOpcion . ',idOpcion',
            'descripcion' => 'required|string|max:45',
            'estado' => 'required|boolean'
        ]);

        try {
            $opcion->update($validated);
            
            return redirect()->route('opciones.index')
                ->with('success', 'Opción actualizada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la opción: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Opcion $opcion)
    {
        try {
            // Verificar si tiene características asociadas
            $caracteristicasCount = $opcion->caracteristicas()->count();
            
            if ($caracteristicasCount > 0) {
                return redirect()->back()
                    ->with('error', 'No se puede eliminar la opción porque tiene ' . $caracteristicasCount . ' características asociadas.');
            }
            
            $opcion->delete();
            
            return redirect()->route('opciones.index')
                ->with('success', 'Opción eliminada correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al eliminar la opción: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of the specified option.
     */
    public function toggleEstado(Opcion $opcion)
    {
        try {
            $opcion->estado = !$opcion->estado;
            $opcion->save();
            
            $mensaje = $opcion->estado ? 'activada' : 'desactivada';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Opción ' . $mensaje . ' correctamente',
                    'estado' => $opcion->estado
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Opción ' . $mensaje . ' correctamente');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al cambiar el estado: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }
}
