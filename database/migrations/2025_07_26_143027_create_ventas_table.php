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
        Schema::create('ventas', function (Blueprint $table) {
            $table->increments('idVenta'); 
            $table->decimal('subtotal', 8, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->date('fechaEntrega');
            $table->string('lugarEntrega', 100)->default('Por definir');
            $table->tinyInteger('estadoPedido')->default(0);
            $table->decimal('saldo', 8, 2)->nullable();
            $table->char('estado', 1)->default('1')->comment('0: Solicitado, 1: Diseño, 2: Confeccion, 3: Entregado');
            $table->timestamps(); 

            $table->unsignedInteger('idEmpleado');
            $table->unsignedInteger('idCliente')->nullable();
            $table->unsignedInteger('idEstablecimiento')->nullable();
            $table->unsignedInteger('idUser');

            $table->foreign('idEmpleado')->references('idEmpleado')->on('empleados')->onDelete('cascade');
            $table->foreign('idCliente')->references('idCliente')->on('cliente_naturals')->onDelete('cascade');
            $table->foreign('idEstablecimiento')->references('idEstablecimiento')->on('cliente_establecimientos')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
