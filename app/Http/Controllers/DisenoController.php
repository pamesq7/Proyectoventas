<?php

namespace App\Http\Controllers;

use App\Models\Diseno;
use App\Models\Producto;
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
        $query = Diseno::query();

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
        $estadosDiseno = ['en proceso', 'terminado'];

        return view('disenos.index', compact('disenos', 'estadosDiseno'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('disenos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_diseno' => 'nullable|string|max:100',
            'precio_adicional' => 'required|numeric|min:0',
            'especificaciones' => 'nullable|string|max:2000',
            'colores_disponibles' => 'nullable|string',
            'tags' => 'nullable|string',
            'es_personalizable' => 'boolean',
            'estado' => 'required|boolean',
            'imagen_preview' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'archivo_diseno' => 'nullable|file|mimes:svg,ai,psd,pdf,zip|max:10240'
        ], [
            'nombre.required' => 'El nombre del diseño es obligatorio.',
            'precio_adicional.required' => 'El precio adicional es obligatorio.',
            'precio_adicional.min' => 'El precio adicional no puede ser negativo.',
            'imagen_preview.image' => 'El archivo debe ser una imagen.',
            'imagen_preview.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen_preview.max' => 'La imagen no puede ser mayor a 2MB.',
            'archivo_diseno.mimes' => 'El archivo debe ser de tipo: svg, ai, psd, pdf, zip.',
            'archivo_diseno.max' => 'El archivo no puede ser mayor a 10MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $imagenPath = null;
            $archivoPath = null;

            // Manejar subida de imagen preview
            if ($request->hasFile('imagen_preview')) {
                $imagen = $request->file('imagen_preview');
                $nombreImagen = time() . '_preview_' . Str::slug($request->nombre) . '.' . $imagen->getClientOriginalExtension();
                $imagenPath = $imagen->storeAs('disenos/previews', $nombreImagen, 'public');
            }

            // Manejar subida de archivo de diseño
            if ($request->hasFile('archivo_diseno')) {
                $archivo = $request->file('archivo_diseno');
                $nombreArchivo = time() . '_archivo_' . Str::slug($request->nombre) . '.' . $archivo->getClientOriginalExtension();
                $archivoPath = $archivo->storeAs('disenos/archivos', $nombreArchivo, 'public');
            }

            // Procesar colores y tags
            $colores = $request->colores_disponibles ? 
                array_map('trim', explode(',', $request->colores_disponibles)) : null;
            $tags = $request->tags ? 
                array_map('trim', explode(',', $request->tags)) : null;

            Diseno::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'imagen_preview' => $imagenPath,
                'archivo_diseno' => $archivoPath,
                'categoria_diseno' => $request->categoria_diseno,
                'precio_adicional' => $request->precio_adicional,
                'especificaciones' => $request->especificaciones,
                'colores_disponibles' => $colores,
                'tags' => $tags,
                'es_personalizable' => $request->has('es_personalizable'),
                'estado' => $request->estado
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
        $diseno->load('productos');
        return view('disenos.show', compact('diseno'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diseno $diseno)
    {
        return view('disenos.edit', compact('diseno'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Diseno $diseno)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_diseno' => 'nullable|string|max:100',
            'precio_adicional' => 'required|numeric|min:0',
            'especificaciones' => 'nullable|string|max:2000',
            'colores_disponibles' => 'nullable|string',
            'tags' => 'nullable|string',
            'es_personalizable' => 'boolean',
            'estado' => 'required|boolean',
            'imagen_preview' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'archivo_diseno' => 'nullable|file|mimes:svg,ai,psd,pdf,zip|max:10240'
        ], [
            'nombre.required' => 'El nombre del diseño es obligatorio.',
            'precio_adicional.required' => 'El precio adicional es obligatorio.',
            'precio_adicional.min' => 'El precio adicional no puede ser negativo.',
            'imagen_preview.image' => 'El archivo debe ser una imagen.',
            'imagen_preview.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'imagen_preview.max' => 'La imagen no puede ser mayor a 2MB.',
            'archivo_diseno.mimes' => 'El archivo debe ser de tipo: svg, ai, psd, pdf, zip.',
            'archivo_diseno.max' => 'El archivo no puede ser mayor a 10MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $imagenPath = $diseno->imagen_preview;
            $archivoPath = $diseno->archivo_diseno;

            // Manejar nueva imagen preview
            if ($request->hasFile('imagen_preview')) {
                // Eliminar imagen anterior
                if ($diseno->imagen_preview && Storage::disk('public')->exists($diseno->imagen_preview)) {
                    Storage::disk('public')->delete($diseno->imagen_preview);
                }

                $imagen = $request->file('imagen_preview');
                $nombreImagen = time() . '_preview_' . Str::slug($request->nombre) . '.' . $imagen->getClientOriginalExtension();
                $imagenPath = $imagen->storeAs('disenos/previews', $nombreImagen, 'public');
            }

            // Manejar nuevo archivo de diseño
            if ($request->hasFile('archivo_diseno')) {
                // Eliminar archivo anterior
                if ($diseno->archivo_diseno && Storage::disk('public')->exists($diseno->archivo_diseno)) {
                    Storage::disk('public')->delete($diseno->archivo_diseno);
                }

                $archivo = $request->file('archivo_diseno');
                $nombreArchivo = time() . '_archivo_' . Str::slug($request->nombre) . '.' . $archivo->getClientOriginalExtension();
                $archivoPath = $archivo->storeAs('disenos/archivos', $nombreArchivo, 'public');
            }

            // Procesar colores y tags
            $colores = $request->colores_disponibles ? 
                array_map('trim', explode(',', $request->colores_disponibles)) : null;
            $tags = $request->tags ? 
                array_map('trim', explode(',', $request->tags)) : null;

            $diseno->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'imagen_preview' => $imagenPath,
                'archivo_diseno' => $archivoPath,
                'categoria_diseno' => $request->categoria_diseno,
                'precio_adicional' => $request->precio_adicional,
                'especificaciones' => $request->especificaciones,
                'colores_disponibles' => $colores,
                'tags' => $tags,
                'es_personalizable' => $request->has('es_personalizable'),
                'estado' => $request->estado
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
            // Eliminar archivos asociados
            if ($diseno->imagen_preview && Storage::disk('public')->exists($diseno->imagen_preview)) {
                Storage::disk('public')->delete($diseno->imagen_preview);
            }

            if ($diseno->archivo_diseno && Storage::disk('public')->exists($diseno->archivo_diseno)) {
                Storage::disk('public')->delete($diseno->archivo_diseno);
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
     * Asociar un diseño a un producto
     */
    public function attachToProduct(Request $request, Diseno $diseno)
    {
        $request->validate([
            'idProducto' => 'required|exists:productos,idProducto',
            'es_principal' => 'boolean',
            'precio_personalizado' => 'nullable|numeric|min:0'
        ]);

        $producto = Producto::findOrFail($request->idProducto);

        // Si es principal, quitar el flag de otros diseños
        if ($request->es_principal) {
            $producto->disenos()->updateExistingPivot(
                $producto->disenos()->pluck('idDiseno')->toArray(),
                ['es_principal' => false]
            );
        }

        $producto->disenos()->attach($diseno->idDiseno, [
            'es_principal' => $request->es_principal ?? false,
            'precio_personalizado' => $request->precio_personalizado,
            'estado' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Diseño asociado al producto exitosamente.'
        ]);
    }
}
