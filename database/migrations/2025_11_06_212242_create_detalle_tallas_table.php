<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detalle_tallas', function (Blueprint $table) {
            $table->increments('idDetalleTalla');
            $table->unsignedInteger('idDetalleVenta');
            $table->string('nombre', 45)->nullable();
            $table->unsignedTinyInteger('numero')->nullable();
            $table->string('adicional', 45)->nullable();
            $table->unsignedTinyInteger('idTallas');
            $table->char('estado', 1);

            $table->foreign('idDetalleVenta')->references('idDetalleVenta')->on('detalle_ventas');
            $table->foreign('idTallas')->references('idTallas')->on('tallas');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('detalle_tallas');
    }
};
