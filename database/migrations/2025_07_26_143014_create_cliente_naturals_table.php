<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cliente_naturals', function (Blueprint $table) {
            $table->unsignedInteger('idCliente');
            $table->bigInteger('nit')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();

            $table->primary('idCliente');
            $table->foreign('idCliente')->references('idUser')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente_naturals');
    }
};
