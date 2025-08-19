<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Variante;
use App\Models\Caracteristica;
use App\Models\Opcion;
use App\Models\VarianteCaracteristica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cargar productos con categoría, variante y diseño
        $productos = Producto::with([
            'categoria',
            'variante',
            'diseno'
        ])->orderBy('nombre', 'asc')
          ->get();
          
        // Log para debug
        Log::info('Productos encontrados:', [
            'total' => $productos->count(),
            'productos' => $productos->map(function($p) {
                return [
                    'id' => $p->idProducto,
                    'nombre' => $p->nombre,
                    'estado' => $p->estado,
                    'created_at' => $p->created_at
                ];
            })
        ]);
          
        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::where('estado', 1)->get();
        $variantes = \App\Models\Variante::where('estado', 1)->get();
        $opciones = Opcion::with(['caracteristicas' => function($query) {
            $query->where('estado', 1);
        }])->where('estado', 1)->get();
        
        return view('productos.create', compact('categorias', 'variantes', 'opciones'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('=== INICIO STORE PRODUCTO ===');
        Log::info('Datos recibidos:', $request->all());
        
        try {
            // Validación de datos
            $validator = Validator::make($request->all(), [
                'SKU' => 'required|string|max:50|unique:productos,SKU',
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string|max:1000',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'cantidad' => 'nullable|integer|min:0',
                'precioVenta' => 'required|integer|min:0',
                'precioProduccion' => 'nullable|integer|min:0',
                'pedidoMinimo' => 'required|integer|min:1',
                'estado' => 'required|boolean',
                'idCategoria' => 'required|exists:categorias,idCategoria',
                'idVariante' => 'nullable|exists:variantes,idVariante'
            ], [
                'SKU.required' => 'El SKU es obligatorio.',
                'SKU.unique' => 'Ya existe un producto con este SKU.',
                'nombre.required' => 'El nombre del producto es obligatorio.',
                'foto.image' => 'El archivo debe ser una imagen.',
                'foto.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
                'foto.max' => 'La imagen no puede ser mayor a 2MB.',
                'cantidad.integer' => 'La cantidad debe ser un número entero.',
                'cantidad.min' => 'La cantidad no puede ser negativa.',
                'precioVenta.required' => 'El precio de venta es obligatorio.',
                'precioVenta.integer' => 'El precio de venta debe ser un número entero.',
                'precioProduccion.integer' => 'El precio de producción debe ser un número entero.',
                'pedidoMinimo.required' => 'El pedido mínimo es obligatorio.',
                'estado.required' => 'El estado es obligatorio.',
                'idCategoria.required' => 'Debes seleccionar una categoría.',
                'idCategoria.exists' => 'La categoría seleccionada no existe.',
                'idVariante.exists' => 'La variante seleccionada no existe.'
            ]);

            if ($validator->fails()) {
                Log::error('Errores de validación:', $validator->errors()->toArray());
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            Log::info('Validación exitosa, procediendo a crear producto...');

            // Manejo de imagen
            $nombreImagen = null;
            if ($request->hasFile('foto')) {
                Log::info('Procesando imagen...');
                $imagen = $request->file('foto');
                $nombreImagen = 'productos/' . time() . '_' . $imagen->getClientOriginalName();
                $imagen->storeAs('public', $nombreImagen);
                Log::info('Imagen guardada:', ['nombre' => $nombreImagen]);
            }

            // Obtener el primer diseño seleccionado para la relación uno-a-muchos
            $idDisenoSeleccionado = null;
            if ($request->filled('disenos_vinculados')) {
                $disenosIds = explode(',', $request->disenos_vinculados);
                $disenosIds = array_filter($disenosIds);
                if (!empty($disenosIds)) {
                    $idDisenoSeleccionado = $disenosIds[0]; // Tomar el primer diseño como principal
                }
            }

            // Datos para crear el producto
            $datosProducto = [
                'SKU' => $request->SKU,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'foto' => $nombreImagen,
                'cantidad' => $request->cantidad ?? 0,
                'precioVenta' => $request->precioVenta,
                'precioProduccion' => $request->precioProduccion,
                'pedidoMinimo' => $request->pedidoMinimo,
                'estado' => $request->estado ?? 1,
                'idCategoria' => $request->idCategoria,
                'idVariante' => $request->idVariante,
                'idDiseno' => $idDisenoSeleccionado // Relación uno-a-muchos: un diseño puede tener muchos productos
            ];

            Log::info('Datos preparados para crear producto:', $datosProducto);

            // Crear el producto
            $producto = Producto::create($datosProducto);

            if ($producto) {
                Log::info('Producto creado exitosamente:', [
                    'id' => $producto->idProducto,
                    'sku' => $producto->SKU,
                    'nombre' => $producto->nombre
                ]);

                // Log del diseño vinculado (relación uno-a-muchos)
                if ($idDisenoSeleccionado) {
                    Log::info('Producto vinculado al diseño:', ['idDiseno' => $idDisenoSeleccionado]);
                }

                return redirect()->route('productos.index')
                    ->with('success', 'Producto creado exitosamente' . ($idDisenoSeleccionado ? ' con diseño vinculado.' : '.'));
            } else {
                Log::error('ERROR: Producto::create() retornó false o null');
                return redirect()->back()
                    ->with('error', 'Error al crear el producto.')
                    ->withInput();
            }

        } catch (\Exception $e) {
            Log::error('EXCEPCIÓN en store():', [
                'mensaje' => $e->getMessage(),
                'archivo' => $e->getFile(),
                'linea' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Error interno: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load([
            'categoria',
            'variante.varianteCaracteristicas.caracteristica.opcion'
        ]);
        
        // Cargar opciones para el modal de nueva variante (si es necesario)
        $opciones = \App\Models\Opcion::with(['caracteristicas' => function($query) {
            $query->where('estado', 1)->orderBy('nombre');
        }])->where('estado', 1)->orderBy('nombre')->get();
        
        return view('productos.show', compact('producto', 'opciones'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $producto = Producto::with(['categoria', 'variante.varianteCaracteristicas.caracteristica.opcion'])->findOrFail($id);
        $categorias = Categoria::where('estado', 1)->get();
        $variantes = \App\Models\Variante::where('estado', 1)->get(); // Para el dropdown de selección
        $opciones = Opcion::with(['caracteristicas' => function($query) {
            $query->where('estado', 1);
        }])->where('estado', 1)->get();
        
        return view('productos.edit', compact('producto', 'categorias', 'variantes', 'opciones'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'SKU' => 'required|string|max:50|unique:productos,SKU,' . $producto->idProducto . ',idProducto',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'precioVenta' => 'required|numeric|min:0',
            'precioProduccion' => 'nullable|numeric|min:0',
            'estado' => 'required|boolean',
            'idCategoria' => 'required|exists:categorias,idCategoria',
            'idVariante' => 'nullable|exists:variantes,idVariante',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ], [
            'SKU.required' => 'El SKU es obligatorio.',
            'SKU.unique' => 'Ya existe un producto con este SKU.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'precioVenta.required' => 'El precio es obligatorio.',
            'precioVenta.min' => 'El precio no puede ser negativo.',
            'precioProduccion.min' => 'El precio de producción no puede ser negativo.',
            'estado.required' => 'El estado es obligatorio.',
            'idCategoria.required' => 'Debes seleccionar una categoría.',
            'idCategoria.exists' => 'La categoría seleccionada no existe.',
            'idVariante.exists' => 'La variante seleccionada no existe.',
            'foto.image' => 'El archivo debe ser una imagen.',
            'foto.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif.',
            'foto.max' => 'La imagen no puede ser mayor a 2MB.'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $fotoPath = $producto->foto; // Mantener foto actual por defecto
            
            // Manejar subida de nueva imagen
            if ($request->hasFile('foto')) {
                // Eliminar foto anterior si existe
                if ($producto->foto && Storage::disk('public')->exists($producto->foto)) {
                    Storage::disk('public')->delete($producto->foto);
                }
                
                $foto = $request->file('foto');
                $nombreArchivo = time() . '_' . Str::slug($request->nombre) . '.' . $foto->getClientOriginalExtension();
                $fotoPath = $foto->storeAs('productos', $nombreArchivo, 'public');
            }

            $producto->update([
                'SKU' => $request->SKU,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'precioVenta' => $request->precioVenta,
                'precioProduccion' => $request->precioProduccion,
                'estado' => $request->estado,
                'idCategoria' => $request->idCategoria,
                'idVariante' => $request->idVariante,
                'foto' => $fotoPath
            ]);

            return redirect()->route('productos.index')
                ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al actualizar el producto: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Producto $producto)
    {
        try {
            // Verificar si el producto tiene ventas asociadas
            // Aquí puedes agregar lógica para verificar si tiene detalles de venta
            // $ventasAsociadas = $producto->detalleVentas()->count();
            
            // Eliminación lógica: cambiar estado a inactivo
            $producto->update(['estado' => 0]);

            return redirect()->route('productos.index')
                ->with('successdelete', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function getCaracteristicasByOpcion($opcionId)
    {
        $caracteristicas = Caracteristica::where('idOpcion', $opcionId)
            ->where('estado', 1)
            ->get();
            
        return response()->json($caracteristicas);
    }

    /**
     * Generate automatic variants for a product
     */
    public function generarVariantesAutomaticas(Request $request, $productoId)
    {
        try {
            $request->validate([
                'caracteristicas' => 'required|array|min:1',
                'caracteristicas.*' => 'exists:caracteristicas,idCaracteristica'
            ]);

            $producto = Producto::findOrFail($productoId);
            $caracteristicasIds = $request->caracteristicas;

            // Obtener características agrupadas por opción
            $caracteristicas = Caracteristica::whereIn('idCaracteristica', $caracteristicasIds)
                ->with('opcion')
                ->get()
                ->groupBy('idOpcion');

            if ($caracteristicas->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron características válidas'
                ], 400);
            }

            // Generar todas las combinaciones posibles
            $combinaciones = $this->generarCombinaciones($caracteristicas);

            if (empty($combinaciones)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se pudieron generar combinaciones'
                ], 400);
            }

            $variantesCreadas = 0;
            $variantesExistentes = 0;

            foreach ($combinaciones as $combinacion) {
                // Verificar si ya existe una variante con esta combinación
                $varianteExistente = $this->verificarVarianteExistente($producto->idProducto, $combinacion);
                
                if ($varianteExistente) {
                    $variantesExistentes++;
                    continue;
                }

                // Crear nueva variante
                $nombreVariante = $this->generarNombreVariante($producto->nombre, $combinacion);
                $skuVariante = $this->generarSkuVariante($producto->sku, $combinacion);

                $variante = Variante::create([
                    'nombre' => $nombreVariante,
                    'sku' => $skuVariante,
                    'precio' => $producto->precioVenta, // Precio base del producto
                    'stock' => 0, // Stock inicial 0
                    'estado' => 1, // Activa por defecto
                    'idProducto' => $producto->idProducto
                ]);

                // Asociar características a la variante
                foreach ($combinacion as $caracteristicaId) {
                    VarianteCaracteristica::create([
                        'idVariante' => $variante->idVariante,
                        'idCaracteristica' => $caracteristicaId,
                        'precioAdicional' => 0 // Precio adicional inicial 0
                    ]);
                }

                $variantesCreadas++;
            }

            $mensaje = "Se crearon {$variantesCreadas} variantes nuevas.";
            if ($variantesExistentes > 0) {
                $mensaje .= " {$variantesExistentes} variantes ya existían.";
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'variantes_creadas' => $variantesCreadas,
                'variantes_existentes' => $variantesExistentes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar variantes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asociar una variante existente a un producto
     */
    public function attachVariante(Request $request, Producto $producto)
    {
        $request->validate([
            'idVariante' => 'required|exists:variantes,idVariante',
            'precioAdicional' => 'nullable|numeric|min:0',
            'stockVariante' => 'nullable|integer|min:0',
            'estado' => 'boolean'
        ]);

        // Verificar si la relación ya existe
        if ($producto->variantes()->where('idVariante', $request->idVariante)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta variante ya está asociada al producto'
            ], 400);
        }

        // Asociar la variante al producto
        $producto->variantes()->attach($request->idVariante, [
            'precioAdicional' => $request->precioAdicional ?? 0,
            'stockVariante' => $request->stockVariante ?? 0,
            'estado' => $request->estado ?? 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variante asociada exitosamente al producto'
        ]);
    }

    /**
     * Desasociar una variante de un producto
     */
    public function detachVariante(Producto $producto, $idVariante)
    {
        if (!$producto->variantes()->where('idVariante', $idVariante)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta variante no está asociada al producto'
            ], 404);
        }

        $producto->variantes()->detach($idVariante);

        return response()->json([
            'success' => true,
            'message' => 'Variante desasociada exitosamente del producto'
        ]);
    }

    /**
     * Actualizar los datos de la relación producto-variante
     */
    public function updateVarianteRelation(Request $request, Producto $producto, $idVariante)
    {
        $request->validate([
            'precioAdicional' => 'nullable|numeric|min:0',
            'stockVariante' => 'nullable|integer|min:0',
            'estado' => 'boolean'
        ]);

        if (!$producto->variantes()->where('idVariante', $idVariante)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Esta variante no está asociada al producto'
            ], 404);
        }

        // Actualizar los datos de la tabla pivot
        $producto->variantes()->updateExistingPivot($idVariante, [
            'precioAdicional' => $request->precioAdicional ?? 0,
            'stockVariante' => $request->stockVariante ?? 0,
            'estado' => $request->estado ?? 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Relación producto-variante actualizada exitosamente'
        ]);
    }

    /**
     * Obtener todas las variantes de un producto con datos de la relación
     */
    public function getProductoVariantes(Producto $producto)
    {
        $variantes = $producto->variantes()
            ->withPivot('precioAdicional', 'stockVariante', 'estado', 'created_at', 'updated_at')
            ->get();

        return response()->json([
            'success' => true,
            'variantes' => $variantes->map(function ($variante) {
                return [
                    'idVariante' => $variante->idVariante,
                    'nombre' => $variante->nombre,
                    'descripcion' => $variante->descripcion,
                    'estado' => $variante->estado,
                    'pivot' => [
                        'precioAdicional' => $variante->pivot->precioAdicional,
                        'stockVariante' => $variante->pivot->stockVariante,
                        'estado' => $variante->pivot->estado,
                        'precioTotal' => $variante->pivot->precioAdicional + ($producto->precioVenta ?? 0)
                    ]
                ];
            })
        ]);
    }

    /**
     * Generar todas las combinaciones posibles de características
     */
    private function generarCombinaciones($caracteristicasPorOpcion)
    {
        $opciones = $caracteristicasPorOpcion->values()->toArray();
        
        if (empty($opciones)) {
            return [];
        }

        // Función recursiva para generar combinaciones
        function combinar($arrays, $i = 0, $current = []) {
            if ($i == count($arrays)) {
                return [$current];
            }
            
            $result = [];
            foreach ($arrays[$i] as $caracteristica) {
                $newCombinations = combinar($arrays, $i + 1, array_merge($current, [$caracteristica->idCaracteristica]));
                $result = array_merge($result, $newCombinations);
            }
            
            return $result;
        }

        return combinar($opciones);
    }

    /**
     * Verificar si ya existe una variante con esta combinación de características
     */
    private function verificarVarianteExistente($productoId, $combinacion)
    {
        $variantes = Variante::where('idProducto', $productoId)
            ->where('estado', 1)
            ->get();

        foreach ($variantes as $variante) {
            $caracteristicasVariante = $variante->caracteristicas->pluck('idCaracteristica')->sort()->values()->toArray();
            $combinacionOrdenada = collect($combinacion)->sort()->values()->toArray();
            
            if ($caracteristicasVariante === $combinacionOrdenada) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generar nombre para la variante basado en las características
     */
    private function generarNombreVariante($nombreProducto, $combinacion)
    {
        $caracteristicas = Caracteristica::whereIn('idCaracteristica', $combinacion)
            ->with('opcion')
            ->get();

        $nombres = $caracteristicas->map(function($caracteristica) {
            return $caracteristica->nombre;
        })->toArray();

        return $nombreProducto . ' - ' . implode(' + ', $nombres);
    }

    /**
     * Generar SKU para la variante
     */
    private function generarSkuVariante($skuProducto, $combinacion)
    {
        $caracteristicas = Caracteristica::whereIn('idCaracteristica', $combinacion)
            ->get();

        $sufijos = $caracteristicas->map(function($caracteristica) {
            return strtoupper(substr($caracteristica->nombre, 0, 2));
        })->toArray();

        return $skuProducto . '-' . implode('', $sufijos);
    }
}
