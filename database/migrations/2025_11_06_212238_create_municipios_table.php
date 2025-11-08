<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('municipios', function (Blueprint $table) {
            $table->increments('idMunicipio');
            $table->string('nombreMunicipio', 30);
            $table->char('estado', 1);
            $table->unsignedInteger('idProvincia');

            $table->foreign('idProvincia')->references('idProvincia')->on('provincias');


        });
    }
    public function down(): void
    {
        Schema::dropIfExists('municipios');
    }
};
