<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop producto_caracteristicas if exists
        if (Schema::hasTable('producto_caracteristicas')) {
            Schema::drop('producto_caracteristicas');
        }

        // Drop variante_opcion if exists
        if (Schema::hasTable('variante_opcion')) {
            Schema::drop('variante_opcion');
        }
    }

    public function down(): void
    {
        // Recreate producto_caracteristicas minimal schema if needed to rollback
        if (!Schema::hasTable('producto_caracteristicas')) {
            Schema::create('producto_caracteristicas', function (Blueprint $table) {
                $table->integer('id', false, true)->autoIncrement();
                $table->string('nombre', 50);
                $table->string('descripcion', 45)->nullable();
                $table->tinyInteger('estado')->default(1);
                $table->decimal('precioAdicional', 10, 2)->default(0);
                $table->unsignedInteger('idCaracteristica');
                $table->unsignedInteger('idProducto');
                $table->timestamps();
                // Indexes only (FKs omitted intentionally to keep rollback simple)
                $table->index('idCaracteristica');
                $table->index('idProducto');
            });
        }

        // Recreate variante_opcion minimal schema if needed to rollback
        if (!Schema::hasTable('variante_opcion')) {
            Schema::create('variante_opcion', function (Blueprint $table) {
                $table->integer('id', false, true)->autoIncrement();
                $table->string('nombre', 50);
                $table->string('descripcion', 45)->nullable();
                $table->tinyInteger('estado')->default(1);
                $table->unsignedInteger('idOpcion');
                $table->unsignedInteger('idVariante');
                $table->timestamps();
                $table->index('idOpcion');
                $table->index('idVariante');
            });
        }
    }
};
