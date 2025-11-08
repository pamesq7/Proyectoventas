<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('disenos', function (Blueprint $table) {
            $table->unsignedInteger('idDiseno', true);
            $table->string('archivo', 255)->nullable();
            $table->string('comentario', 45)->nullable();
            $table->tinyInteger('estado');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->enum('estadoDiseno', ['en proceso', 'terminado'])->default('en proceso');

            $table->unsignedInteger('idDetalleVenta');
            $table->unsignedInteger('idEmpleado');

            $table->foreign('idDetalleVenta')->references('idDetalleVenta')->on('detalle_ventas');
            $table->foreign('idEmpleado')->references('idEmpleado')->on('empleados');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('disenos');
    }
};
