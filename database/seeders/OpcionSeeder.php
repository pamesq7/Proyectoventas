<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Opcion;

class OpcionSeeder extends Seeder
{
    public function run(): void
    {
        Opcion::create(['nombre' => 'Cuello', 'descripcion' => 'Tipo de cuello de la prenda']);
        Opcion::create(['nombre' => 'Manga', 'descripcion' => 'Estilo de manga']);
        Opcion::create(['nombre' => 'Tela', 'descripcion' => 'Material de la tela']);
        // Agrega más opciones según tu lógica de negocio
    }
}