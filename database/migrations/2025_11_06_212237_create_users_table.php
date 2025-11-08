<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('idUser'); // INT UNSIGNED AI
            $table->string('ci')->nullable(false);
            $table->string('name');
            $table->string('primerApellido');
            $table->string('segundApellido')->nullable();
            $table->string('email')->unique();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('telefono')->nullable();
            $table->string('password');
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};
