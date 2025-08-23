<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodoPago;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['nombre' => 'Efectivo', 'codigo' => 'efectivo', 'estado' => 1],
            ['nombre' => 'QR',       'codigo' => 'qr',       'estado' => 1],
            ['nombre' => 'Cheque',   'codigo' => 'cheque',   'estado' => 1],
        ];

        foreach ($defaults as $d) {
            MetodoPago::updateOrCreate(['codigo' => $d['codigo']], $d);
        }
    }
}
