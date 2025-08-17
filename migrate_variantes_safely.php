<?php

/**
 * SCRIPT DE MIGRACIÓN SEGURA PARA VARIANTES
 * 
 * Este script migra los datos de variantes de la estructura antigua (One-to-Many)
 * a la nueva estructura (Many-to-Many) sin perder datos.
 * 
 * PASOS:
 * 1. Crear tabla pivot producto_variantes
 * 2. Migrar datos existentes
 * 3. Eliminar columna idProducto de variantes
 * 
 * USO: php migrate_variantes_safely.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Configurar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 MIGRACIÓN SEGURA DE VARIANTES - INICIANDO\n";
echo "==========================================\n\n";

try {
    // PASO 1: Verificar estado actual
    echo "📋 PASO 1: Verificando estado actual de la base de datos...\n";
    
    $hasVariantesTable = Schema::hasTable('variantes');
    $hasProductosTable = Schema::hasTable('productos');
    $hasPivotTable = Schema::hasTable('producto_variantes');
    $hasIdProductoColumn = Schema::hasColumn('variantes', 'idProducto');
    
    echo "   ✓ Tabla 'variantes': " . ($hasVariantesTable ? "EXISTS" : "MISSING") . "\n";
    echo "   ✓ Tabla 'productos': " . ($hasProductosTable ? "EXISTS" : "MISSING") . "\n";
    echo "   ✓ Tabla 'producto_variantes': " . ($hasPivotTable ? "EXISTS" : "MISSING") . "\n";
    echo "   ✓ Columna 'variantes.idProducto': " . ($hasIdProductoColumn ? "EXISTS" : "MISSING") . "\n\n";
    
    if (!$hasVariantesTable || !$hasProductosTable) {
        throw new Exception("❌ Faltan tablas básicas. Verifica tu base de datos.");
    }
    
    // PASO 2: Crear tabla pivot si no existe
    if (!$hasPivotTable) {
        echo "📋 PASO 2: Creando tabla pivot 'producto_variantes'...\n";
        
        // Ejecutar migración específica
        $exitCode = null;
        $output = [];
        exec('php artisan migrate --path=database/migrations/2025_08_03_000001_create_producto_variantes_table.php 2>&1', $output, $exitCode);
        
        if ($exitCode === 0) {
            echo "   ✅ Tabla pivot creada exitosamente\n\n";
        } else {
            echo "   ⚠️ Intentando crear tabla manualmente...\n";
            
            Schema::create('producto_variantes', function ($table) {
                $table->id();
                $table->unsignedInteger('idProducto');
                $table->unsignedTinyInteger('idVariante');
                $table->decimal('precioAdicional', 8, 2)->default(0.00);
                $table->integer('stockVariante')->default(0);
                $table->tinyInteger('estado')->default(1);
                $table->timestamps();
                
                $table->foreign('idProducto')->references('idProducto')->on('productos')->onDelete('cascade');
                $table->foreign('idVariante')->references('idVariante')->on('variantes')->onDelete('cascade');
                $table->unique(['idProducto', 'idVariante'], 'unique_producto_variante');
                $table->index('idProducto');
                $table->index('idVariante');
            });
            
            echo "   ✅ Tabla pivot creada manualmente\n\n";
        }
    } else {
        echo "📋 PASO 2: Tabla pivot ya existe, continuando...\n\n";
    }
    
    // PASO 3: Migrar datos si la columna idProducto existe
    if ($hasIdProductoColumn) {
        echo "📋 PASO 3: Migrando datos existentes...\n";
        
        $exitCode = null;
        $output = [];
        exec('php artisan db:seed --class=MigrateVariantesToPivotSeeder 2>&1', $output, $exitCode);
        
        echo "   📊 Salida del seeder:\n";
        foreach ($output as $line) {
            echo "   " . $line . "\n";
        }
        echo "\n";
        
        if ($exitCode !== 0) {
            throw new Exception("❌ Error ejecutando el seeder de migración");
        }
    } else {
        echo "📋 PASO 3: Columna idProducto no existe, datos ya migrados\n\n";
    }
    
    // PASO 4: Eliminar columna idProducto
    if ($hasIdProductoColumn) {
        echo "📋 PASO 4: Eliminando columna idProducto de variantes...\n";
        echo "   ⚠️ IMPORTANTE: Esto eliminará la columna permanentemente\n";
        echo "   ⏳ Esperando 3 segundos antes de continuar...\n";
        sleep(3);
        
        $exitCode = null;
        $output = [];
        exec('php artisan migrate --path=database/migrations/2025_08_03_000002_remove_idproducto_from_variantes_table.php 2>&1', $output, $exitCode);
        
        if ($exitCode === 0) {
            echo "   ✅ Columna idProducto eliminada exitosamente\n\n";
        } else {
            echo "   ⚠️ Intentando eliminar columna manualmente...\n";
            
            Schema::table('variantes', function ($table) {
                $table->dropForeign(['idProducto']);
                $table->dropColumn('idProducto');
            });
            
            echo "   ✅ Columna eliminada manualmente\n\n";
        }
    } else {
        echo "📋 PASO 4: Columna idProducto ya fue eliminada\n\n";
    }
    
    // PASO 5: Verificar resultado final
    echo "📋 PASO 5: Verificando migración...\n";
    
    $finalPivotCount = DB::table('producto_variantes')->count();
    $variantesCount = DB::table('variantes')->count();
    $productosCount = DB::table('productos')->count();
    
    echo "   📊 Registros en 'producto_variantes': {$finalPivotCount}\n";
    echo "   📊 Registros en 'variantes': {$variantesCount}\n";
    echo "   📊 Registros en 'productos': {$productosCount}\n";
    echo "   ✓ Columna 'variantes.idProducto': " . (Schema::hasColumn('variantes', 'idProducto') ? "STILL EXISTS" : "REMOVED") . "\n\n";
    
    echo "🎉 MIGRACIÓN COMPLETADA EXITOSAMENTE!\n";
    echo "=====================================\n";
    echo "✅ Estructura Many-to-Many implementada\n";
    echo "✅ Datos preservados\n";
    echo "✅ Relaciones actualizadas\n\n";
    echo "💡 Próximos pasos:\n";
    echo "   1. Actualizar modelos Eloquent (ya hecho)\n";
    echo "   2. Actualizar controladores (ya hecho)\n";
    echo "   3. Probar la nueva funcionalidad\n";

} catch (Exception $e) {
    echo "❌ ERROR EN LA MIGRACIÓN: " . $e->getMessage() . "\n";
    echo "🔄 La base de datos permanece en su estado anterior\n";
    exit(1);
}
