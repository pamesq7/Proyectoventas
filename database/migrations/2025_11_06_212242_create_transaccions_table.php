<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transaccions', function (Blueprint $table) {
            $table->tinyIncrements('idTransaccion');
            $table->string('tipoTransaccion', 20);
            $table->decimal('monto', 8, 2);
            $table->string('metodoPago', 20);
            $table->string('observaciones', 255)->nullable();
            $table->tinyInteger('estado');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unsignedInteger('idVenta');
            $table->foreign('idVenta')->references('idVenta')->on('ventas');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('transaccions');
    }
};
