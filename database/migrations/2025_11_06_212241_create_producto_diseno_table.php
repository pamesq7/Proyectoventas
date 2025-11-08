<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('producto_diseno', function (Blueprint $table) {
            $table->increments('idProductoDIseno');
            $table->unsignedInteger('idDiseno');
            $table->unsignedInteger('idProducto');
            $table->string('nombre', 45)->nullable();
            $table->decimal('precio', 8, 2)->nullable();
            $table->tinyInteger('estado')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->foreign('idDiseno')->references('idDiseno')->on('disenos');
            $table->foreign('idProducto')->references('idProducto')->on('productos');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('producto_diseno');
    }
};
