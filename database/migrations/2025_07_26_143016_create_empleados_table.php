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
        Schema::create('empleados', function (Blueprint $table) {
            $table->unsignedInteger('idEmpleado')->primary(); // mismo tipo que idUser
            $table->string('cargo', 45);
            $table->enum('rol', ['administrador', 'diseñador', 'operador', 'cliente', 'vendedor']);
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();

            $table->foreign('idEmpleado')->references('idUser')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
