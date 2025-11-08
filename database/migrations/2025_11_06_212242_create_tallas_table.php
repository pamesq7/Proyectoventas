<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tallas', function (Blueprint $table) {
            $table->tinyIncrements('idTallas'); // TINYINT UNSIGNED AI
            $table->string('nombre', 50)->nullable();
            $table->tinyInteger('estado')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('tallas');
    }
};
