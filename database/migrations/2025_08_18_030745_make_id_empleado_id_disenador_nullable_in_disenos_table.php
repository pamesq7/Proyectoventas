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
        Schema::table('disenos', function (Blueprint $table) {
            // Primero eliminar las claves foráneas
            $table->dropForeign(['idEmpleado']);
            $table->dropForeign(['idDiseñador']);
            
            // Modificar las columnas para permitir NULL
            $table->bigInteger('idEmpleado', false, true)->nullable()->change();
            $table->bigInteger('idDiseñador', false, true)->nullable()->change();
            
            // Recrear las claves foráneas
            $table->foreign('idEmpleado')
                  ->references('idEmpleado')
                  ->on('empleados')
                  ->onDelete('set null');
                  
            $table->foreign('idDiseñador')
                  ->references('idEmpleado')
                  ->on('empleados')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('disenos', function (Blueprint $table) {
            $table->unsignedBigInteger('idEmpleado')->nullable(false)->change();
            $table->unsignedBigInteger('idDiseñador')->nullable(false)->change();
        });
    }
};
