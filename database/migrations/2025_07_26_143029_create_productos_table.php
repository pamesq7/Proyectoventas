<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->integer('idProducto', false, true)->autoIncrement();
            $table->string('SKU', 45);
            $table->string('nombre', 60);
            $table->string('descripcion', 250)->nullable();
            $table->string('foto', 250)->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('precioVenta', 5, 2);
            $table->decimal('precioProduccion', 5, 2)->nullable();
            $table->tinyInteger('pedidoMinimo');
            $table->string('stock', 45)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->tinyInteger('idCategoria', false, true);
            $table->integer('idDiseno', false, true);
            $table->tinyInteger('idVariante', false, true)->nullable();
            
            $table->foreign('idCategoria')
                  ->references('idCategoria')
                  ->on('categorias')
                  ->onDelete('cascade');
                  
            $table->foreign('idDiseno')
                  ->references('idDiseno')
                  ->on('disenos')
                  ->onDelete('no action')
                  ->onUpdate('no action');
                  
            // $table->foreign('idVariante')
            //       ->references('id')
            //       ->on('variantes')
            //       ->onDelete('set null')
            //       ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
