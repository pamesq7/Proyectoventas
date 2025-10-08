<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\Categoria;

class TiendaController extends Controller
{
    public function index(Request $request)
    {
        // Usar 'estado' = 1 en lugar de 'activo'
        $query = Producto::where('estado', 1) // Productos activos
                        ->where('stock', '>', 0); // Con stock disponible
        
        // Filtro por categoría
        if ($request->has('categoria') && $request->categoria != 'all') {
            $query->where('idCategoria', $request->categoria); // idCategoria en lugar de categoria_id
        }
        
        // Filtro por búsqueda
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->search . '%');
            });
        }
        
        $productos = $query->paginate(12);
        $categorias = Categoria::all(); // Verifica si Categoria también tiene 'estado'
        
        return view('tienda.index', compact('productos', 'categorias'));
    }
    
    public function show($id)
    {
        $producto = Producto::where('estado', 1)->findOrFail($id);
        return view('tienda.show', compact('producto'));
    }
}