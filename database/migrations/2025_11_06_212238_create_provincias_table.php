<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('provincias', function (Blueprint $table) {
            $table->increments('idProvincia');
            $table->string('nombreProvincia', 30);
            $table->char('estado', 1);
            $table->unsignedTinyInteger('idDepartamento');

            $table->foreign('idDepartamento')->references('idDepartamento')->on('departamentos');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('provincias');
    }
};
