<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Agregar clave foránea de productos a variantes
        Schema::table('productos', function (Blueprint $table) {
            $table->foreign('idVariante')
                  ->references('id')
                  ->on('variantes')
                  ->onDelete('set null')
                  ->onUpdate('no action');
        });
        
        // Agregar clave foránea de disenos a detalle_ventas
        Schema::table('disenos', function (Blueprint $table) {
            $table->foreign('iddetalleVenta')
                  ->references('iddetalleVenta')
                  ->on('detalle_ventas')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['idVariante']);
        });
        
        Schema::table('disenos', function (Blueprint $table) {
            $table->dropForeign(['iddetalleVenta']);
        });
    }
};
