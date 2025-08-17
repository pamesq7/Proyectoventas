<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Opcion;
use App\Models\Caracteristica;

class ConfiguracionSeeder extends Seeder
{
    public function run()
    {
        // Crear Categorías
        $categorias = [
            ['nombreCategoria' => 'Camisetas', 'descripcion' => 'Camisetas deportivas y casuales'],
            ['nombreCategoria' => 'Pantalones', 'descripcion' => 'Pantalones deportivos y casuales'],
            ['nombreCategoria' => 'Zapatos', 'descripcion' => 'Calzado deportivo y casual'],
            ['nombreCategoria' => 'Accesorios', 'descripcion' => 'Gorras, medias y complementos'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::create($categoria + ['estado' => 1]);
        }

        // Crear Opciones
        $opciones = [
            ['nombre' => 'Color', 'descripcion' => 'Colores disponibles para productos'],
            ['nombre' => 'Material', 'descripcion' => 'Tipos de materiales'],
            ['nombre' => 'Estilo', 'descripcion' => 'Estilos de productos'],
        ];

        foreach ($opciones as $opcion) {
            Opcion::create($opcion + ['estado' => 1]);
        }

        // Crear Características
        $caracteristicas = [
            // Colores
            ['nombre' => 'Rojo', 'descripcion' => 'Color rojo vibrante', 'idOpcion' => 1],
            ['nombre' => 'Azul', 'descripcion' => 'Color azul marino', 'idOpcion' => 1],
            ['nombre' => 'Negro', 'descripcion' => 'Color negro clásico', 'idOpcion' => 1],
            ['nombre' => 'Blanco', 'descripcion' => 'Color blanco puro', 'idOpcion' => 1],
            
            // Materiales
            ['nombre' => 'Algodón', 'descripcion' => '100% algodón natural', 'idOpcion' => 2],
            ['nombre' => 'Poliéster', 'descripcion' => 'Material sintético duradero', 'idOpcion' => 2],
            ['nombre' => 'Cuero', 'descripcion' => 'Cuero genuino', 'idOpcion' => 2],
            
            // Estilos
            ['nombre' => 'Casual', 'descripcion' => 'Estilo casual y cómodo', 'idOpcion' => 3],
            ['nombre' => 'Deportivo', 'descripcion' => 'Estilo deportivo y funcional', 'idOpcion' => 3],
            ['nombre' => 'Formal', 'descripcion' => 'Estilo formal y elegante', 'idOpcion' => 3],
        ];

        foreach ($caracteristicas as $caracteristica) {
            Caracteristica::create($caracteristica + ['estado' => 1]);
        }
    }
}
