<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Normalizar valores existentes antes de aplicar ENUM
        // Mapear variantes comunes a los valores finales
        DB::statement("UPDATE transaccions SET metodoPago = 'efectivo' WHERE LOWER(TRIM(metodoPago)) IN ('efectivo','cash','contado')");
        DB::statement("UPDATE transaccions SET metodoPago = 'qr' WHERE LOWER(TRIM(metodoPago)) IN ('qr','qrcode','codigo qr')");
        DB::statement("UPDATE transaccions SET metodoPago = 'cheque' WHERE LOWER(TRIM(metodoPago)) IN ('cheque','check')");
        DB::statement("UPDATE transaccions SET metodoPago = 'transferencia' WHERE LOWER(TRIM(metodoPago)) IN ('transferencia','transferencia bancaria','bank transfer','transfer')");

        // Para cualquier otro valor no reconocido, usar 'efectivo' como fallback
        DB::statement("UPDATE transaccions SET metodoPago = 'efectivo' WHERE metodoPago IS NULL OR TRIM(metodoPago) = '' OR LOWER(TRIM(metodoPago)) NOT IN ('efectivo','qr','cheque','transferencia')");

        // Cambiar tipo de columna a ENUM con los valores requeridos
        DB::statement("ALTER TABLE transaccions MODIFY COLUMN metodoPago ENUM('efectivo','qr','transferencia','cheque') NOT NULL DEFAULT 'efectivo'");
    }

    public function down(): void
    {
        // Revertir a VARCHAR(50) (ajustar si tu esquema original difiere)
        DB::statement("ALTER TABLE transaccions MODIFY COLUMN metodoPago VARCHAR(50) NOT NULL");
    }
};
