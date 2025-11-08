<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('variantes', function (Blueprint $table) {
            $table->tinyIncrements('idVariante');
            $table->string('nombre', 50);
            $table->string('descripcion', 45);
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('variantes');
    }
};
