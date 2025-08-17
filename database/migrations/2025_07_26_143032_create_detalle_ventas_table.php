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
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->integer('iddetalleVenta', false, true)->autoIncrement();
            $table->integer('cantidad')->default(1);
            $table->string('nombrePersonalizado', 45)->nullable();
            $table->string('numeroPersonalizado', 10)->nullable();
            $table->string('textoAdicional', 45)->nullable();
            $table->string('observacion', 45)->nullable();
            $table->decimal('precioUnitario', 5, 2);
            $table->decimal('subtotal', 8, 2)->nullable();
            $table->decimal('descuento', 5, 2)->nullable();
            $table->string('descripcion', 60)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->smallInteger('idTalla', false, true);
            $table->integer('idVenta', false, true);
            $table->unsignedInteger('idEmpleado');
            
            $table->foreign('idTalla')
                  ->references('idTalla')
                  ->on('tallas')
                  ->onDelete('cascade');
                  
            $table->foreign('idVenta')
                  ->references('idVenta')
                  ->on('ventas')
                  ->onDelete('cascade');
                  
            $table->foreign('idEmpleado')
                  ->references('idEmpleado')
                  ->on('empleados')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
