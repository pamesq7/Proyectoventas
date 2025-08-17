<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('variante_opcion', function (Blueprint $table) {
            $table->integer('id', false, true)->autoIncrement();
            $table->string('nombre', 50);
            $table->string('descripcion', 45)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
            $table->tinyInteger('idOpcion', false, true);
            $table->tinyInteger('idCaracteristica', false, true);
            
            $table->foreign('idOpcion')
                  ->references('idOpcion')
                  ->on('opcions')
                  ->onDelete('no action')
                  ->onUpdate('no action');
                  
            $table->foreign('idCaracteristica')
                  ->references('id')
                  ->on('variantes')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('variante_opcion');
    }
};
