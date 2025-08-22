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
    public function up()
    {
        // 1) Quitar FK de idEmpleado (si existe) con SQL explícito para evitar error 1832
        try {
            DB::statement('ALTER TABLE `disenos` DROP FOREIGN KEY `disenos_idempleado_foreign`');
        } catch (\Throwable $e) {
            // Ignorar si no existe
        }

        // 2) Modificar columnas a NULLABLE con SQL explícito (evita dependencia de doctrine/dbal)
        DB::statement('ALTER TABLE `disenos` MODIFY `idEmpleado` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `disenos` MODIFY `idDiseñador` BIGINT UNSIGNED NULL');

        // 3) Recrear FK de idEmpleado con ON DELETE SET NULL
        DB::statement('ALTER TABLE `disenos` ADD CONSTRAINT `disenos_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados`(`idEmpleado`) ON DELETE SET NULL ON UPDATE NO ACTION');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Quitar FK para poder revertir a NOT NULL
        try {
            DB::statement('ALTER TABLE `disenos` DROP FOREIGN KEY `disenos_idempleado_foreign`');
        } catch (\Throwable $e) {
        }

        DB::statement('ALTER TABLE `disenos` MODIFY `idEmpleado` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `disenos` MODIFY `idDiseñador` BIGINT UNSIGNED NOT NULL');

        // Recrear FK como en la creación inicial (NO ACTION)
        DB::statement('ALTER TABLE `disenos` ADD CONSTRAINT `disenos_idempleado_foreign` FOREIGN KEY (`idEmpleado`) REFERENCES `empleados`(`idEmpleado`) ON DELETE NO ACTION ON UPDATE NO ACTION');
    }
};
