<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('variante_caracteristicas', function (Blueprint $table) {
            $table->tinyIncrements('idVariantesCaracteristicas');
            $table->string('nombre', 50);
            $table->string('descripcion', 45);
            $table->tinyInteger('estado')->default(1);
            $table->decimal('precioAdicional', 8, 2)->nullable();
            $table->timestamps();
            $table->unsignedTinyInteger('idCaracteristica');
            $table->unsignedTinyInteger('idVariante');

            $table->foreign('idVariante')->references('id')->on('variantes')->onDelete('cascade');
            $table->foreign('idCaracteristica')->references('idCaracteristica')->on('caracteristicas')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variante_caracteristicas');
    }
};
