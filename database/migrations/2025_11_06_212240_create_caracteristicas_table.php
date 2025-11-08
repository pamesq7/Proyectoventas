<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('caracteristicas', function (Blueprint $table) {
            $table->tinyIncrements('idCaracteristica');
            $table->string('nombre', 50);
            $table->string('descripcion', 45);
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unsignedTinyInteger('idOpcion');
            $table->foreign('idOpcion')->references('idOpcion')->on('opcions')->onDelete('cascade');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('caracteristicas');
    }
};
