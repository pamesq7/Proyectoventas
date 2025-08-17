<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateToCorrectRelationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "🔄 Iniciando migración a relación correcta (variante → muchos productos)...\n";

        // Verificar que la columna idVariante existe en productos
        if (!Schema::hasColumn('productos', 'idVariante')) {
            echo "❌ Error: La columna 'idVariante' no existe en productos. Ejecuta primero la migración.\n";
            return;
        }

        // Verificar que la columna idProducto aún existe en variantes
        if (!Schema::hasColumn('variantes', 'idProducto')) {
            echo "ℹ️ La columna 'idProducto' no existe en variantes. No hay datos para migrar.\n";
            return;
        }

        // Obtener todas las relaciones existentes
        $relaciones = DB::table('variantes')
            ->whereNotNull('idProducto')
            ->where('idProducto', '>', 0)
            ->select('idVariante', 'idProducto')
            ->get();

        if ($relaciones->count() === 0) {
            echo "ℹ️ No se encontraron relaciones para migrar.\n";
            return;
        }

        echo "📊 Encontradas {$relaciones->count()} relaciones para migrar...\n";

        $migrated = 0;
        $skipped = 0;

        foreach ($relaciones as $relacion) {
            // Verificar que el producto existe
            $productoExists = DB::table('productos')
                ->where('idProducto', $relacion->idProducto)
                ->exists();

            if (!$productoExists) {
                echo "⚠️ Producto {$relacion->idProducto} no existe, saltando...\n";
                $skipped++;
                continue;
            }

            // Verificar que la variante existe
            $varianteExists = DB::table('variantes')
                ->where('idVariante', $relacion->idVariante)
                ->exists();

            if (!$varianteExists) {
                echo "⚠️ Variante {$relacion->idVariante} no existe, saltando...\n";
                $skipped++;
                continue;
            }

            // Verificar si el producto ya tiene una variante asignada
            $productoTieneVariante = DB::table('productos')
                ->where('idProducto', $relacion->idProducto)
                ->whereNotNull('idVariante')
                ->exists();

            if ($productoTieneVariante) {
                echo "⏭️ Producto {$relacion->idProducto} ya tiene variante asignada, saltando...\n";
                $skipped++;
                continue;
            }

            try {
                // Actualizar el producto con la variante
                DB::table('productos')
                    ->where('idProducto', $relacion->idProducto)
                    ->update([
                        'idVariante' => $relacion->idVariante,
                        'updated_at' => now()
                    ]);

                echo "✅ Producto {$relacion->idProducto} → Variante {$relacion->idVariante}\n";
                $migrated++;

            } catch (\Exception $e) {
                echo "❌ Error migrando producto {$relacion->idProducto}: {$e->getMessage()}\n";
                $skipped++;
            }
        }

        echo "\n📋 RESUMEN DE MIGRACIÓN:\n";
        echo "✅ Productos actualizados: {$migrated}\n";
        echo "⏭️ Registros saltados: {$skipped}\n";
        echo "📊 Total procesados: " . ($migrated + $skipped) . "\n";

        if ($migrated > 0) {
            echo "\n🎉 ¡Migración completada exitosamente!\n";
            echo "💡 Ahora puedes eliminar la columna idProducto de variantes.\n";
            
            // Mostrar estadísticas finales
            $productosConVariante = DB::table('productos')->whereNotNull('idVariante')->count();
            $totalProductos = DB::table('productos')->count();
            $totalVariantes = DB::table('variantes')->count();
            
            echo "\n📊 ESTADÍSTICAS FINALES:\n";
            echo "   📦 Productos con variante: {$productosConVariante}/{$totalProductos}\n";
            echo "   🏷️ Total variantes: {$totalVariantes}\n";
        } else {
            echo "\n⚠️ No se migraron nuevos registros.\n";
        }
    }
}
