<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Categoria;

class OperadorController extends Controller
{
    public function index()
    {
        $pedidos = Venta::with([
            'clienteNatural',
            'clienteEstablecimiento',
            'empleado',
            'detalleVentas',
            'disenos.empleado.user'
        ])->where('estado', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Agregar numeración consecutiva
        $contador = ($pedidos->currentPage() - 1) * $pedidos->perPage() + 1;
        $pedidos->each(function ($pedido) use (&$contador) {
            $pedido->contador = $contador++;
        });

        return view('rolOperador.index', compact('pedidos'));
    }  
    
    public function catalogo(Request $request)
    {
        $query = Producto::with(['categoria', 'variante'])
            ->where('estado', 1)
            ->orderBy('nombre', 'asc');

        // Búsqueda por nombre o descripción
        if ($request->filled('buscar')) {
            $search = $request->buscar;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%")
                    ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        // Filtrar por categoría
        if ($request->filled('categoria_id') && $request->categoria_id != 'todas') {
            $query->where('idCategoria', $request->categoria_id);
        }

        $productos = $query->paginate(12);
        $categorias = Categoria::where('estado', 1)->orderBy('nombreCategoria')->get();

        return view('catalogo.consulta', compact('productos', 'categorias'));
    }

    /**
     * Dashboard del operador
     */
    public function dashboard()
    {
        // Verificar que el usuario tenga rol de operador
        if (!auth()->user()->empleado || strtolower(auth()->user()->empleado->rol) !== 'operador') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return view('dashboard.operador');
    }

    /**
     * Ver detalle de pedido (SOLO show, sin editar/eliminar)
     */
    public function show($pedido)
    {
        $pedido = Venta::with([
            'detalleVentas.talla',
            'detalleVentas.diseno',
            'clienteNatural',
            'clienteEstablecimiento',
            'empleado'
        ])->findOrFail($pedido);

        // Verificar si hay un diseño temporal cargado en la sesión
        $disenoUrl = null;
        if (session()->has('disenoTemporal')) {
            $disenoUrl = asset('storage/' . session()->get('disenoTemporal'));
        }

        return view('rolOperador.show', compact('pedido', 'disenoUrl'));
    }
}