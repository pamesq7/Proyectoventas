<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProductoApiController extends Controller
{
    // Obtener productos relacionados (misma variante o relacionados por lógica de negocio)
    public function productosRelacionados($idProducto)
    {
        // Primero obtenemos el producto actual
        $productoActual = DB::table('productos')
            ->where('idProducto', $idProducto)
            ->first();

        if (!$productoActual) {
            return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        // Buscamos productos relacionados por lógica de negocio
        // Por ejemplo, si es una polera (idVariante=1), buscamos su par corto (idVariante=2)
        $idVarianteRelacionada = null;
        
        // Mapeo de variantes relacionadas (esto es un ejemplo, ajústalo según tu lógica)
        $relacionesVariantes = [
            1 => 2,  // Polera -> Corto
            2 => 1,  // Corto -> Polera
            3 => 4,  // Chamarra -> Buzo
            4 => 3   // Buzo -> Chamarra
        ];

        if (isset($relacionesVariantes[$productoActual->idVariante])) {
            $idVarianteRelacionada = $relacionesVariantes[$productoActual->idVariante];
        }

        $productosRelacionados = [];

        // Si encontramos una variante relacionada, buscamos productos de esa variante
        if ($idVarianteRelacionada) {
            $productosRelacionados = DB::table('productos')
                ->where('idVariante', $idVarianteRelacionada)
                ->where('estado', 1) // Solo productos activos
                ->select('idProducto', 'nombre', 'idVariante')
                ->get();
        }

        // Siempre incluimos el producto actual
        $resultado = [
            [
                'idProducto' => $productoActual->idProducto,
                'nombre' => $productoActual->nombre,
                'idVariante' => $productoActual->idVariante,
                'esPrincipal' => true
            ]
        ];

        // Agregamos los productos relacionados
        foreach ($productosRelacionados as $relacionado) {
            $resultado[] = [
                'idProducto' => $relacionado->idProducto,
                'nombre' => $relacionado->nombre,
                'idVariante' => $relacionado->idVariante,
                'esPrincipal' => false
            ];
        }

        return response()->json([
            'success' => true,
            'productos' => $resultado
        ]);
    }

    // Obtener opciones y características para un producto específico
    public function opcionesPorProducto($idProducto)
    {
        try {
            // Obtener las opciones del producto
            $opciones = DB::table('producto_opcions as po')
                ->join('opcions as o', 'o.idOpcion', '=', 'po.idOpcion')
                ->where('po.idProducto', $idProducto)
                ->select('o.idOpcion', 'o.nombre as nombreOpcion')
                ->orderBy('o.nombre')
                ->get();

            // Obtener las características para cada opción
            $opcionesConCaracteristicas = [];
            foreach ($opciones as $opcion) {
                $caracteristicas = DB::table('caracteristicas as c')
                    ->where('c.idOpcion', $opcion->idOpcion)
                    ->select('idCaracteristica', 'nombre')
                    ->orderBy('nombre')
                    ->get();

                $opcionesConCaracteristicas[] = [
                    'idOpcion' => (int)$opcion->idOpcion,
                    'nombreOpcion' => $opcion->nombreOpcion,
                    'caracteristicas' => $caracteristicas
                ];
            }

            return response()->json([
                'success' => true,
                'opciones' => $opcionesConCaracteristicas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar las opciones del producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
