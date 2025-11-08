<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('idProducto'); // INT UNSIGNED AI
            $table->string('SKU', 45);
            $table->string('nombre', 60);
            $table->string('descripcion', 250)->nullable();
            $table->string('foto', 250)->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('precioVenta', 10, 2);
            $table->decimal('precioProduccion', 10, 2)->nullable();
            $table->tinyInteger('pedidoMinimo');
            $table->string('stock', 45)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unsignedTinyInteger('idCategoria');
            $table->unsignedTinyInteger('idVariante')->nullable();

            $table->foreign('idCategoria')->references('idCategoria')->on('categorias')->onDelete('cascade');
            $table->foreign('idVariante')->references('idVariante')->on('variantes')->onDelete('set null');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
