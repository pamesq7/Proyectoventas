<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variante_caracteristicas', function (Blueprint $table) {
            $table->tinyIncrements('idVarianteCaracteristica');
            $table->string('nombre', 50);
            $table->string('descripcion', 45);
            $table->tinyInteger('estado')->default(1);
            $table->decimal('precioAdicional', 8, 2)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unsignedTinyInteger('idCaracteristica');
            $table->unsignedTinyInteger('idVariante');

            $table->foreign('idCaracteristica')->references('idCaracteristica')->on('caracteristicas')->onDelete('cascade');
            $table->foreign('idVariante')->references('idVariante')->on('variantes')->onDelete('cascade');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('variante_caracteristicas');
    }
};
