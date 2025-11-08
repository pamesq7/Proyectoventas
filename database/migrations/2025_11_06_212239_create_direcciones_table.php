<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('direcciones', function (Blueprint $table) {
            $table->increments('idDireccion');
            $table->string('nombreDireccion', 255)->nullable();
            $table->char('estado', 1);
            $table->unsignedInteger('idMunicipio');

            $table->foreign('idMunicipio')->references('idMunicipio')->on('municipios');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('direcciones');
    }
};
