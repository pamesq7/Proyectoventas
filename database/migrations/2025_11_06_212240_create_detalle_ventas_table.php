<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->unsignedInteger('idDetalleVenta', true); // UNSIGNED AI
            $table->integer('cantidad')->default(1);
            $table->decimal('precioUnitario', 5, 2);
            $table->decimal('descuento', 5, 2)->nullable();
            $table->string('descripcion', 60)->nullable();
            $table->enum('tipo_pack', ['producto', 'pack']);
            $table->decimal('subtotal', 8, 2)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unsignedInteger('idProducto')->nullable();
            $table->unsignedInteger('idPack')->nullable();
            $table->unsignedInteger('idEmpleado');
            $table->unsignedInteger('idVenta');

            $table->foreign('idProducto')->references('idProducto')->on('productos');
            $table->foreign('idPack')->references('idPack')->on('pack');
            $table->foreign('idEmpleado')->references('idEmpleado')->on('empleados');
            $table->foreign('idVenta')->references('idVenta')->on('ventas');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
