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
        Schema::create('producto_disenos', function (Blueprint $table) {
            $table->id();
            $table->integer('idProducto', false, true);
            $table->integer('idDiseno', false, true);
            $table->boolean('es_principal')->default(false);
            $table->decimal('precio_personalizado', 8, 2)->nullable();
            $table->text('personalizaciones')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            
            // Claves foráneas
            $table->foreign('idProducto')
                  ->references('idProducto')
                  ->on('productos')
                  ->onDelete('cascade');
                  
            $table->foreign('idDiseno')
                  ->references('idDiseno')
                  ->on('disenos')
                  ->onDelete('cascade');
                  
            // Índice único para evitar duplicados
            $table->unique(['idProducto', 'idDiseno']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('producto_disenos');
    }
};
