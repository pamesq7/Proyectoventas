<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cliente_establecimientos', function (Blueprint $table) {
            $table->unsignedInteger('idEstablecimiento')->autoIncrement();
            $table->unsignedBigInteger('nit')->nullable();
            $table->string('razonSocial', 100);
            $table->string('tipoEstablecimiento', 50);
            $table->string('domicilioFiscal', 255)->nullable();
            $table->unsignedInteger('idRepresentante');
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('idRepresentante')->references('idUser')->on('users')->onDelete('cascade');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('cliente_establecimientos');
    }
};
