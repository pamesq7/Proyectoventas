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
        Schema::create('producto_tallas', function (Blueprint $table) {
            $table->smallInteger('idTalla', false, true);
            $table->string('precioAdicional', 45)->nullable();
            $table->string('stock', 45)->nullable();
            $table->integer('idProducto', false, true);
            
            $table->primary('idTalla');
            $table->foreign('idTalla')
                  ->references('idTalla')
                  ->on('tallas')
                  ->onDelete('no action')
                  ->onUpdate('no action');
                  
            $table->foreign('idProducto')
                  ->references('idProducto')
                  ->on('productos')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('producto_tallas');
    }
};
