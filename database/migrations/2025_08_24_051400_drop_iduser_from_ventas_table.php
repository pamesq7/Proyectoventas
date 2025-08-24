<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('ventas', 'idUser')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->dropColumn('idUser');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('ventas', 'idUser')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedInteger('idUser')->after('idEstablecimiento');
            });
        }
    }
};
