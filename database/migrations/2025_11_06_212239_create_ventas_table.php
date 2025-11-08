<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->integerIncrements('idVenta');
            $table->decimal('subtotal', 8, 2)->default(0.00);
            $table->decimal('total', 10, 2)->default(0.00);
            $table->date('fechaEntrega');
            $table->tinyInteger('estadoPedido')->default(0);
            $table->decimal('saldo', 8, 2);
            $table->char('estado', 1)->default('1')->comment('0: Solicitado, 1: Diseño, 2: Confeccion, 3: Entregado');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unsignedInteger('idCliente');         // ref cliente_naturals.idCliente
            $table->unsignedInteger('idEstablecimiento'); // ref cliente_establecimientos.idEstablecimiento
            $table->unsignedInteger('idDireccion');       // ref direcciones.idDireccion
            $table->unsignedInteger('idEmpleado');        // ref empleados.idEmpleado

            $table->foreign('idCliente')->references('idCliente')->on('cliente_naturals');
            $table->foreign('idEstablecimiento')->references('idEstablecimiento')->on('cliente_establecimientos');
            $table->foreign('idDireccion')->references('idDireccion')->on('direcciones');
            $table->foreign('idEmpleado')->references('idEmpleado')->on('empleados');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
