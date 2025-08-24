<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('detalle_ventas', 'subtotal')) {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->dropColumn('subtotal');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('detalle_ventas', 'subtotal')) {
            Schema::table('detalle_ventas', function (Blueprint $table) {
                $table->decimal('subtotal', 8, 2)->nullable()->after('precioUnitario');
            });
        }
    }
};
