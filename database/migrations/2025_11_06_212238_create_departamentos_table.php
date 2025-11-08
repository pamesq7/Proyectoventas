<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('departamentos', function (Blueprint $table) {
            $table->tinyIncrements('idDepartamento');
            $table->string('nombreDepartamento', 25);
            $table->char('estado', 1);

            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_spanish2_ci';
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('departamentos');
    }
};
