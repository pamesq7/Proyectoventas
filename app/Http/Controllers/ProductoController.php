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
use Illuminate\Support\Str;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with(['categoria'])
            ->where('estado', 1)
            ->orderBy('nombre', 'asc')
            ->get();

        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $categorias = Categoria::where('estado', 1)->get();
        $variantes = Variante::where('estado', 1)->get();
        $opciones = Opcion::with(['caracteristicas' => function ($q) {
            $q->where('estado', 1);
        }])->where('estado', 1)->get();

        $productosIndividuales = Producto::where('estado', 1)
            ->where('tipoProducto', 'producto')
            ->orderBy('nombre')
            ->get();

        return view('productos.create', compact(
            'categorias',
            'variantes',
            'opciones',
            'productosIndividuales'
        ));
    }

    public function store(Request $request)
    {
        try {

            // VALIDAR
            $request->validate([
                'SKU' => 'required|unique:productos,SKU',
                'nombre' => 'required|string|max:255',
                'precioVenta' => 'required|numeric|min:0',
                'precioProduccion' => 'nullable|numeric|min:0',
                'pedidoMinimo' => 'required|integer|min:1',
                'idCategoria' => 'required|exists:categorias,idCategoria',
                'idVariante' => 'nullable|exists:variantes,idVariante',
                'tipoProducto' => 'required|in:producto,pack',
                'componentesPack' => 'required_if:tipoProducto,pack|json',
                'foto' => 'nullable|image|max:2048'
            ]);

            // GUARDAR FOTO
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('productos', 'public');
            }

            // CREAR PRODUCTO PRINCIPAL
            $producto = Producto::create([
                'SKU' => $request->SKU,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'foto' => $fotoPath,
                'cantidad' => $request->cantidad ?? 0,
                'precioVenta' => $request->precioVenta,
                'precioProduccion' => $request->precioProduccion,
                'pedidoMinimo' => $request->pedidoMinimo,
                'stock' => 0,
                'estado' => $request->estado ?? 1,
                'idCategoria' => $request->idCategoria,
                'idVariante' => $request->idVariante,
                'tipoProducto' => $request->tipoProducto,
                'idPackProducto' => null
            ]);

            // GUARDAR COMPONENTES EN TABLA PACK SI ES PACK
            if ($request->tipoProducto === 'pack') {

                $componentes = json_decode($request->componentesPack, true);

                foreach ($componentes as $comp) {
                    DB::table('pack')->insert([
                        'idPackProducto' => $producto->idProducto,
                        'idProducto' => $comp['idProducto'],
                    ]);
                }
            }

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }


    public function show($id)
    {
        $producto = Producto::with(['categoria', 'variante'])->findOrFail($id);
        return view('productos.show', compact('producto'));
    }


    public function edit($id)
    {
        $producto = Producto::findOrFail($id);
        $categorias = Categoria::where('estado', 1)->get();
        $variantes = Variante::where('estado', 1)->get();

        return view('productos.edit', compact(
            'producto',
            'categorias',
            'variantes'
        ));
    }


    public function update(Request $request, Producto $producto)
    {
        try {

            // VALIDACIÓN
            $request->validate([
                'SKU' => 'required|unique:productos,SKU,' . $producto->idProducto . ',idProducto',
                'nombre' => 'required|string|max:255',
                'precioVenta' => 'required|numeric|min:0',
                'precioProduccion' => 'nullable|numeric|min:0',
                'idCategoria' => 'required|exists:categorias,idCategoria',
                'idVariante' => 'nullable|exists:variantes,idVariante',
                'foto' => 'nullable|image|max:2048'
            ]);

            // FOTO NUEVA
            $fotoPath = $producto->foto;
            if ($request->hasFile('foto')) {

                if ($producto->foto && Storage::disk('public')->exists($producto->foto)) {
                    Storage::disk('public')->delete($producto->foto);
                }

                $fotoPath = $request->file('foto')->store('productos', 'public');
            }

            // ACTUALIZAR
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

            return redirect()
                ->route('productos.index')
                ->with('success', 'Producto actualizado correctamente.');
        } catch (\Exception $e) {
            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }
    }


    public function destroy(Producto $producto)
    {
        try {
            $producto->update(['estado' => 0]);

            return redirect()
                ->route('productos.index')
                ->with('successdelete', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()
                ->route('productos.index')
                ->with('error', $e->getMessage());
        }
    }
}
