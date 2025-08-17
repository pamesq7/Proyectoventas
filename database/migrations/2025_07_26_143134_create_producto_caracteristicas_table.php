<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('producto_caracteristicas', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement();
            $table->string('nombre', 50);
            $table->string('descripcion', 45)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->decimal('precioAdicional', 8, 2)->nullable();
            $table->timestamps();
            $table->integer('idProducto', false, true);
            $table->tinyInteger('idCaracteristica', false, true);
            
            $table->foreign('idProducto')
                  ->references('idProducto')
                  ->on('productos')
                  ->onDelete('no action')
                  ->onUpdate('no action');
                  
            $table->foreign('idCaracteristica')
                  ->references('idCaracteristica')
                  ->on('caracteristicas')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('producto_caracteristicas');
    }
};
