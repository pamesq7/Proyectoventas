<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::create(['nombre' => 'Fútbol', 'descripcion' => 'Productos relacionados con fútbol']);
        Categoria::create(['nombre' => 'Básquet', 'descripcion' => 'Productos relacionados con básquet']);
        Categoria::create(['nombre' => 'Deportivo', 'descripcion' => 'Ropa y accesorios deportivos generales']);
        // Agrega más si necesitas
    }
}