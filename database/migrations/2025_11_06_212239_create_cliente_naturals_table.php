<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cliente_naturals', function (Blueprint $table) {
            $table->unsignedInteger('idCliente')->primary();
            $table->unsignedBigInteger('nit')->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('idCliente')->references('idUser')->on('users');


        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cliente_naturals');
    }
};
