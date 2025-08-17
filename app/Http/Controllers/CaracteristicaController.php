<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caracteristica;
use App\Models\Opcion;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CaracteristicaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Redirigir a la vista unificada de configuración
        return redirect()->route('configuracion.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Redirigir a la vista unificada de configuración
        return redirect()->route('configuracion.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Log para debug
        Log::info('CaracteristicaController::store - Datos recibidos:', $request->all());
        
        try {
            // Validar datos básicos
            $validatedData = $request->validate([
                'idOpcion' => 'required|exists:opcions,idOpcion',
                'nombre' => 'required|string|max:100',
                'descripcion' => 'nullable|string|max:255'
            ]);
            
            Log::info('Datos validados correctamente:', $validatedData);

            $caracteristica = Caracteristica::create([
                'nombre' => $validatedData['nombre'],
                'descripcion' => $validatedData['descripcion'] ?? null,
                'estado' => 1, // Siempre activa por defecto
                'idOpcion' => $validatedData['idOpcion']
            ]);
            
            Log::info('Característica creada exitosamente:', ['id' => $caracteristica->idCaracteristica]);
            
            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Característica creada correctamente'
                ]);
            }
            
            return redirect()->route('caracteristicas.index')
                ->with('success', 'Característica creada correctamente');
                
        } catch (\Exception $e) {
            // Log detallado del error
            Log::error('Error al crear característica:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la característica: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear la característica: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Caracteristica $caracteristica)
    {
        $caracteristica->load('opcion');
        return view('configuracion.caracteristicas.show', compact('caracteristica'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Caracteristica $caracteristica)
    {
        try {
            // Eliminar las variantes asociadas primero (si las hay)
            $caracteristica->variantesCaracteristicas()->delete();
            
            // Eliminar la característica
            $caracteristica->delete();
            
            // Si es una petición AJAX, devolver JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Característica eliminada correctamente'
                ]);
            }
            
            return redirect()->route('caracteristicas.index')
                ->with('success', 'Característica eliminada correctamente');
        } catch (\Exception $e) {
            $errorMessage = 'Error al eliminar la característica: ' . $e->getMessage();
            
            // Si es una petición AJAX, devolver error JSON
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Toggle the status of the specified resource.
     */
    public function toggleEstado(Caracteristica $caracteristica)
    {
        try {
            $caracteristica->estado = !$caracteristica->estado;
            $caracteristica->save();
            
            $mensaje = $caracteristica->estado ? 'activada' : 'desactivada';
            
            return redirect()->back()
                ->with('success', 'Característica ' . $mensaje . ' correctamente');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al cambiar el estado: ' . $e->getMessage());
        }
    }

    /**
     * Get characteristics by option (AJAX)
     */
    public function getByOpcion($idOpcion)
    {
        try {
            $caracteristicas = Caracteristica::where('idOpcion', $idOpcion)
                ->orderBy('nombre', 'asc')
                ->get(['idCaracteristica', 'nombre', 'descripcion', 'estado']);
            
            return response()->json($caracteristicas);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las características: ' . $e->getMessage()
            ], 500);
        }
    }
}
