<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pack_productos', function (Blueprint $table) {
            $table->increments('idPackProducto');      // UNSIGNED INT AI
            $table->unsignedInteger('idPack');         // <-- debe ser UNSIGNED para coincidir con pack.idPack
            $table->unsignedInteger('idProducto');     // productos.idProducto es UNSIGNED
            $table->string('nombre', 45)->nullable();
            $table->integer('cantidad')->nullable();
            $table->decimal('precio', 8, 2)->nullable();
            $table->tinyInteger('estado');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('update_at')->nullable(); // coincide con tu esquema (no es updated_at)

            // FKs
            $table->foreign('idProducto')
                  ->references('idProducto')->on('productos');

            $table->foreign('idPack')
                  ->references('idPack')->on('pack');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_productos');
    }
};
