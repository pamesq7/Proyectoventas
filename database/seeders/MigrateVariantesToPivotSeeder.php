<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateVariantesToPivotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Iniciando migración de datos de variantes...\n";

        // Verificar que la tabla pivot existe
        if (!Schema::hasTable('producto_variantes')) {
            echo "❌ Error: La tabla 'producto_variantes' no existe. Ejecuta primero la migración de la tabla pivot.\n";
            return;
        }

        // Verificar que la columna idProducto aún existe en variantes
        if (!Schema::hasColumn('variantes', 'idProducto')) {
            echo "ℹ️ La columna 'idProducto' ya fue eliminada de la tabla variantes. Migración no necesaria.\n";
            return;
        }

        // Verificar si ya existen datos en la tabla pivot
        $existingPivotData = DB::table('producto_variantes')->count();
        if ($existingPivotData > 0) {
            echo "⚠️ La tabla 'producto_variantes' ya contiene {$existingPivotData} registros.\n";
            echo "¿Deseas continuar? Esto podría crear duplicados. (Continuando automáticamente...)\n";
        }

        // Obtener variantes con idProducto
        $variantesConProducto = DB::table('variantes')
            ->whereNotNull('idProducto')
            ->where('idProducto', '>', 0)
            ->get();

        if ($variantesConProducto->count() === 0) {
            echo "ℹ️ No se encontraron variantes con idProducto para migrar.\n";
            return;
        }

        echo "📊 Encontradas {$variantesConProducto->count()} variantes para migrar...\n";

        $migrated = 0;
        $skipped = 0;

        foreach ($variantesConProducto as $variante) {
            // Verificar si la relación ya existe en la tabla pivot
            $exists = DB::table('producto_variantes')
                ->where('idProducto', $variante->idProducto)
                ->where('idVariante', $variante->idVariante)
                ->exists();

            if ($exists) {
                echo "⏭️ Saltando variante {$variante->idVariante} - producto {$variante->idProducto} (ya existe)\n";
                $skipped++;
                continue;
            }

            // Verificar que el producto existe
            $productoExists = DB::table('productos')
                ->where('idProducto', $variante->idProducto)
                ->exists();

            if (!$productoExists) {
                echo "⚠️ Producto {$variante->idProducto} no existe, saltando variante {$variante->idVariante}\n";
                $skipped++;
                continue;
            }

            // Insertar en la tabla pivot
            try {
                DB::table('producto_variantes')->insert([
                    'idProducto' => $variante->idProducto,
                    'idVariante' => $variante->idVariante,
                    'precioAdicional' => 0.00, // Valor por defecto
                    'stockVariante' => 0, // Valor por defecto
                    'estado' => $variante->estado ?? 1,
                    'created_at' => $variante->created_at ?? now(),
                    'updated_at' => $variante->updated_at ?? now(),
                ]);

                echo "✅ Migrada variante {$variante->idVariante} → producto {$variante->idProducto}\n";
                $migrated++;

            } catch (\Exception $e) {
                echo "❌ Error migrando variante {$variante->idVariante}: {$e->getMessage()}\n";
                $skipped++;
            }
        }

        echo "\n📋 RESUMEN DE MIGRACIÓN:\n";
        echo "✅ Registros migrados: {$migrated}\n";
        echo "⏭️ Registros saltados: {$skipped}\n";
        echo "📊 Total procesados: " . ($migrated + $skipped) . "\n";

        if ($migrated > 0) {
            echo "\n🎉 ¡Migración completada exitosamente!\n";
            echo "💡 Ahora puedes ejecutar la migración para eliminar la columna idProducto de variantes.\n";
        } else {
            echo "\n⚠️ No se migraron nuevos registros.\n";
        }
    }
}
