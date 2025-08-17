<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaccions', function (Blueprint $table) {
            $table->tinyInteger('idTransaccion', false, true)->autoIncrement();
            $table->string('tipoTransaccion', 20);
            $table->decimal('monto', 8, 2);
            $table->string('metodoPago', 20);
            $table->string('observaciones', 255)->nullable();
            $table->tinyInteger('estado');
            $table->timestamps();
            $table->integer('idVenta', false, true);
            $table->integer('idUser', false, true)->nullable();
            
            $table->foreign('idVenta')
                  ->references('idVenta')
                  ->on('ventas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('transaccions');
    }
};
