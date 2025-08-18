<?php

require_once 'vendor/autoload.php';

// Cargar la aplicación Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // Ejecutar SQL directo para hacer los campos nullable
    DB::statement('ALTER TABLE disenos MODIFY COLUMN idEmpleado BIGINT UNSIGNED NULL');
    echo "✓ Campo idEmpleado modificado a nullable\n";
    
    DB::statement('ALTER TABLE disenos MODIFY COLUMN idDiseñador BIGINT UNSIGNED NULL');
    echo "✓ Campo idDiseñador modificado a nullable\n";
    
    // Verificar la estructura
    $columns = DB::select('DESCRIBE disenos');
    echo "\nEstructura actual de la tabla disenos:\n";
    foreach ($columns as $column) {
        if (in_array($column->Field, ['idEmpleado', 'idDiseñador'])) {
            echo "- {$column->Field}: {$column->Type} | Null: {$column->Null}\n";
        }
    }
    
    echo "\n✅ Cambios aplicados exitosamente\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
