<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empleados', function (Blueprint $table) {
            $table->unsignedInteger('idEmpleado')->primary(); // debe ser UNSIGNED para FK a users.idUser
            $table->string('cargo', 45);
            $table->enum('rol', ['administrador', 'diseñador', 'operador', 'cliente', 'vendedor']);
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('idEmpleado')->references('idUser')->on('users');

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
