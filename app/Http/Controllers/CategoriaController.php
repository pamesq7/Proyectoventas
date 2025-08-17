<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Solo mostrar categorías activas (estado = 1)
        $categorias = Categoria::where('estado', 1)->orderBy('nombreCategoria', 'asc')->get();
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nombreCategoria' => 'required|string|max:100|unique:categorias,nombreCategoria',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable'
        ], [
            'nombreCategoria.required' => 'El nombre de la categoría es obligatorio.',
            'nombreCategoria.unique' => 'Ya existe una categoría con este nombre.',
            'nombreCategoria.max' => 'El nombre no puede exceder 100 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Convertir estado a boolean
            $estado = $request->has('estado') && $request->estado == '1' ? 1 : 0;
            
            Categoria::create([
                'nombreCategoria' => $request->nombreCategoria,
                'descripcion' => $request->descripcion,
                'estado' => $estado
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría creada exitosamente.'
                ]);
            }

            return redirect()->route('configuracion.index')
                ->with('success', 'Categoría creada exitosamente.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al crear la categoría: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al crear la categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        if (request()->ajax()) {
            try {
                return response()->json([
                    'idCategoria' => $categoria->idCategoria,
                    'nombreCategoria' => $categoria->nombreCategoria,
                    'descripcion' => $categoria->descripcion,
                    'estado' => $categoria->estado,
                    'productos_count' => $categoria->productos()->count()
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Error al obtener la categoría: ' . $e->getMessage()
                ], 500);
            }
        }
        
        $categoria->load('productos'); // Cargar productos relacionados
        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'nombreCategoria' => 'required|string|max:100|unique:categorias,nombreCategoria,' . $categoria->idCategoria . ',idCategoria',
            'descripcion' => 'nullable|string|max:500',
            'estado' => 'nullable'
        ], [
            'nombreCategoria.required' => 'El nombre de la categoría es obligatorio.',
            'nombreCategoria.unique' => 'Ya existe una categoría con este nombre.',
            'nombreCategoria.max' => 'El nombre no puede exceder 100 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Convertir estado a boolean
            $estado = $request->has('estado') && $request->estado == '1' ? 1 : 0;
            
            $categoria->update([
                'nombreCategoria' => $request->nombreCategoria,
                'descripcion' => $request->descripcion,
                'estado' => $estado
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría actualizada exitosamente.'
                ]);
            }

            return redirect()->route('configuracion.index')
                ->with('success', 'Categoría actualizada exitosamente.');
                
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al actualizar la categoría: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Categoria $categoria)
    {
        try {
            // Verificar si la categoría tiene productos activos asociados
            $productosActivos = $categoria->productos()->where('estado', 1)->count();
            if ($productosActivos > 0) {
                return redirect()->route('categorias.index')
                    ->with('error', 'No se puede eliminar la categoría porque tiene productos activos asociados. Desactiva primero los productos.');
            }

            // Eliminación lógica: cambiar estado a inactivo
            $categoria->update(['estado' => 0]);

            return redirect()->route('categorias.index')
                ->with('successdelete', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('categorias.index')
                ->with('error', 'Error al eliminar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Display a listing of deleted (inactive) categories.
     */
    public function deleted()
    {
        $categorias = Categoria::where('estado', 0)->orderBy('nombreCategoria', 'asc')->get();
        return view('categorias.deleted', compact('categorias'));
    }

    /**
     * Restore a deleted (inactive) category.
     */
    public function restore($id)
    {
        try {
            $categoria = Categoria::findOrFail($id);
            
            if ($categoria->estado == 1) {
                return redirect()->route('categorias.deleted')
                    ->with('error', 'La categoría ya está activa.');
            }

            $categoria->update(['estado' => 1]);

            return redirect()->route('categorias.deleted')
                ->with('success', 'Categoría restaurada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('categorias.deleted')
                ->with('error', 'Error al restaurar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of the specified category.
     */
    public function toggleEstado(Categoria $categoria)
    {
        try {
            $categoria->estado = !$categoria->estado;
            $categoria->save();
            
            $mensaje = $categoria->estado ? 'activada' : 'desactivada';
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Categoría ' . $mensaje . ' correctamente',
                    'estado' => $categoria->estado
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Categoría ' . $mensaje . ' correctamente');
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
