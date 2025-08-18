<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Usar SQL directo para modificar los campos a nullable
        DB::statement('ALTER TABLE disenos MODIFY COLUMN idEmpleado BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE disenos MODIFY COLUMN idDiseñador BIGINT UNSIGNED NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los campos a NOT NULL
        DB::statement('ALTER TABLE disenos MODIFY COLUMN idEmpleado BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE disenos MODIFY COLUMN idDiseñador BIGINT UNSIGNED NOT NULL');
    }
};
