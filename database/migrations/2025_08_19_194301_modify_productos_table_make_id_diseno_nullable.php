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
        Schema::table('productos', function (Blueprint $table) {
            // Primero eliminar la clave foránea existente
            $table->dropForeign(['idDiseno']);
            
            // Modificar el campo para permitir NULL
            $table->integer('idDiseno', false, true)->nullable()->change();
            
            // Recrear la clave foránea
            $table->foreign('idDiseno')
                  ->references('idDiseno')
                  ->on('disenos')
                  ->onDelete('set null')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            // Eliminar la clave foránea
            $table->dropForeign(['idDiseno']);
            
            // Revertir el campo a NOT NULL
            $table->integer('idDiseno', false, true)->change();
            
            // Recrear la clave foránea original
            $table->foreign('idDiseno')
                  ->references('idDiseno')
                  ->on('disenos')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }
};
