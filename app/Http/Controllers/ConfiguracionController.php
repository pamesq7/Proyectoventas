<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\Opcion;
use App\Models\Caracteristica;
use App\Models\Producto;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar la vista unificada de configuración
     */
    public function index()
    {
        try {
            // Obtener todas las categorías con conteo de productos
            $categorias = Categoria::withCount('productos')->orderBy('nombreCategoria')->get();
            
            // Obtener todas las opciones con sus características
            $opciones = Opcion::with(['caracteristicas' => function($query) {
                $query->orderBy('nombre');
            }])->orderBy('nombre')->get();
            
            // Obtener todas las características
            $caracteristicas = Caracteristica::with('opcion')->orderBy('nombre')->get();
            
            // Contar total de productos
            $totalProductos = Producto::count();
            
            return view('configuracion.index', compact(
                'categorias',
                'opciones', 
                'caracteristicas',
                'totalProductos'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al cargar la configuración: ' . $e->getMessage());
        }
    }
}
