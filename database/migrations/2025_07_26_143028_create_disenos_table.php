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
        Schema::create('disenos', function (Blueprint $table) {
            $table->integer('idDiseno', false, true)->autoIncrement();
            $table->string('archivo', 255)->nullable();
            $table->string('comentario', 45)->nullable();
            $table->tinyInteger('estado');
            $table->timestamps();
            $table->integer('idDiseñador', false, true);
            $table->enum('estadoDiseño', ['en proceso', 'terminado'])->default('en proceso');
            $table->integer('iddetalleVenta', false, true)->nullable();
            $table->unsignedInteger('idEmpleado');
            
            // $table->foreign('iddetalleVenta')
            //       ->references('iddetalleVenta')
            //       ->on('detalle_ventas')
            //       ->onDelete('no action')
            //       ->onUpdate('no action');
                  
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
        Schema::dropIfExists('disenos');
    }
};
