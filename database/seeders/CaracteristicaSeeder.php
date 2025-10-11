<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Caracteristica;

class CaracteristicaSeeder extends Seeder
{
    public function run(): void
    {
        Caracteristica::create(['nombre' => 'Algodón', 'descripcion' => 'Tela de algodón suave']);
        Caracteristica::create(['nombre' => 'Poliéster', 'descripcion' => 'Tela resistente al agua']);
        Caracteristica::create(['nombre' => 'Rojo', 'descripcion' => 'Color rojo intenso']);
        Caracteristica::create(['nombre' => 'Azul', 'descripcion' => 'Color azul estándar']);
        // Agrega más características relevantes
    }
}