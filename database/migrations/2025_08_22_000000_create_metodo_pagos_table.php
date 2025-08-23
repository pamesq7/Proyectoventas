<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('metodo_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: Efectivo, QR, Cheque
            $table->string('codigo')->unique(); // ej: efectivo, qr, cheque
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metodo_pagos');
    }
};
