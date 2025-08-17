<?php
// Script para depurar el problema de creación de características
require_once 'vendor/autoload.php';

// Cargar configuración de Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Opcion;
use App\Models\Caracteristica;

echo "=== DEBUG: Creación de Características ===\n\n";

try {
    // 1. Verificar conexión a base de datos
    echo "1. Verificando conexión a base de datos...\n";
    DB::connection()->getPdo();
    echo "✅ Conexión exitosa\n\n";
    
    // 2. Verificar si existen opciones
    echo "2. Verificando opciones existentes...\n";
    $opciones = Opcion::all();
    echo "📊 Total opciones: " . $opciones->count() . "\n";
    
    if ($opciones->count() == 0) {
        echo "⚠️  No hay opciones en la base de datos\n";
        echo "Insertando opciones de prueba...\n";
        
        // Insertar opciones básicas
        $opcionesData = [
            ['nombre' => 'Fútbol', 'descripcion' => 'Productos relacionados con fútbol', 'estado' => 1],
            ['nombre' => 'Vestir', 'descripcion' => 'Productos de vestir', 'estado' => 1],
            ['nombre' => 'Corto', 'descripcion' => 'Productos de corte corto', 'estado' => 1],
        ];
        
        foreach ($opcionesData as $opcionData) {
            Opcion::create($opcionData);
        }
        
        echo "✅ Opciones insertadas\n";
        $opciones = Opcion::all();
    }
    
    foreach ($opciones as $opcion) {
        echo "   - {$opcion->nombre} (ID: {$opcion->idOpcion})\n";
    }
    echo "\n";
    
    // 3. Verificar características existentes
    echo "3. Verificando características existentes...\n";
    $caracteristicas = Caracteristica::with('opcion')->get();
    echo "📊 Total características: " . $caracteristicas->count() . "\n";
    
    foreach ($caracteristicas as $caracteristica) {
        echo "   - {$caracteristica->nombre} (Opción: {$caracteristica->opcion->nombre})\n";
    }
    echo "\n";
    
    // 4. Probar creación de nueva característica
    echo "4. Probando creación de nueva característica...\n";
    
    $primeraOpcion = $opciones->first();
    if ($primeraOpcion) {
        $nuevaCaracteristica = [
            'nombre' => 'Test Característica ' . date('H:i:s'),
            'descripcion' => 'Característica de prueba creada automáticamente',
            'estado' => 1,
            'idOpcion' => $primeraOpcion->idOpcion
        ];
        
        echo "Datos a insertar:\n";
        print_r($nuevaCaracteristica);
        
        $caracteristicaCreada = Caracteristica::create($nuevaCaracteristica);
        echo "✅ Característica creada exitosamente con ID: {$caracteristicaCreada->idCaracteristica}\n\n";
        
        // Verificar que se guardó correctamente
        $verificacion = Caracteristica::find($caracteristicaCreada->idCaracteristica);
        if ($verificacion) {
            echo "✅ Verificación exitosa - Característica encontrada en BD\n";
        } else {
            echo "❌ Error - Característica no encontrada en BD\n";
        }
    }
    
    // 5. Verificar estructura de tabla características
    echo "\n5. Verificando estructura de tabla características...\n";
    $columns = DB::select("DESCRIBE caracteristicas");
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) - {$column->Null} - {$column->Key}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DEBUG ===\n";
?>
