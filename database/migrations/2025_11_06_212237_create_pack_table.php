<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pack', function (Blueprint $table) {
            $table->integerIncrements('idPack'); // INT AI (no unsigned requerido por no FK invertida)
            $table->string('nombre', 45)->nullable();
            $table->string('descripcion', 80)->nullable();
            $table->tinyInteger('estado');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('update_at')->nullable();

        });
    }
    public function down(): void
    {
        Schema::dropIfExists('pack');
    }
};
