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
        Schema::create('producto_opcions', function (Blueprint $table) {
            $table->tinyIncrements('idProductoOpcion'); 
            $table->string('nombre', 50);
            $table->string('descripcion', 45);
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->unsignedInteger('idProducto');
            $table->unsignedTinyInteger('idOpcion');

            $table->foreign('idProducto')->references('idProducto')->on('productos')->onDelete('cascade');
            $table->foreign('idOpcion')->references('idOpcion')->on('opcions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_opcions');
    }
};
