<?php
// Script para probar la creación de características y capturar errores específicos

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

require_once 'vendor/autoload.php';

// Configurar Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Configurar el contexto de Laravel
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Importar facades necesarios - mover al inicio del archivo

// Crear una request simulada
$request = Illuminate\Http\Request::create('/caracteristicas', 'POST', [
    'idOpcion' => 1, // Asumiendo que existe opción con ID 1
    'nombre' => 'Test Característica',
    'descripcion' => 'Descripción de prueba'
]);

// Agregar headers necesarios
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('Content-Type', 'application/x-www-form-urlencoded');

try {
    echo "=== PRUEBA DE CREACIÓN DE CARACTERÍSTICA ===\n\n";
    
    // 1. Verificar que existe la opción
    echo "1. Verificando opciones disponibles:\n";
    $opciones = DB::table('opcions')->get();
    foreach ($opciones as $opcion) {
        echo "   - ID: {$opcion->idOpcion}, Nombre: {$opcion->nombre}\n";
    }
    
    if ($opciones->isEmpty()) {
        echo "❌ ERROR: No hay opciones en la base de datos\n";
        exit;
    }
    
    echo "\n2. Probando validaciones del controlador:\n";
    
    // Crear instancia del controlador
    $controller = new App\Http\Controllers\CaracteristicaController();
    
    // Simular la validación
    $validator = Validator::make($request->all(), [
        'idOpcion' => 'required|integer',
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string|max:255'
    ]);
    
    if ($validator->fails()) {
        echo "❌ Errores de validación:\n";
        foreach ($validator->errors()->all() as $error) {
            echo "   - $error\n";
        }
    } else {
        echo "✅ Validaciones pasaron correctamente\n";
    }
    
    echo "\n3. Verificando existencia de opción:\n";
    $opcionExists = DB::table('opcions')->where('idOpcion', $request->idOpcion)->exists();
    if ($opcionExists) {
        echo "✅ La opción ID {$request->idOpcion} existe\n";
    } else {
        echo "❌ La opción ID {$request->idOpcion} NO existe\n";
    }
    
    echo "\n4. Intentando crear característica directamente:\n";
    
    $caracteristica = new App\Models\Caracteristica();
    $caracteristica->nombre = $request->nombre;
    $caracteristica->descripcion = $request->descripcion;
    $caracteristica->estado = 1;
    $caracteristica->idOpcion = $request->idOpcion;
    
    if ($caracteristica->save()) {
        echo "✅ Característica creada exitosamente con ID: {$caracteristica->idCaracteristica}\n";
        
        // Limpiar después de la prueba
        $caracteristica->delete();
        echo "✅ Característica de prueba eliminada\n";
    } else {
        echo "❌ Error al guardar la característica\n";
    }
    
    echo "\n5. Probando el método store del controlador:\n";
    
    // Simular el request con CSRF token
    $request->merge(['_token' => csrf_token()]);
    
    try {
        $response = $controller->store($request);
        echo "✅ Método store ejecutado sin errores\n";
        echo "Tipo de respuesta: " . get_class($response) . "\n";
        
        if (method_exists($response, 'getContent')) {
            echo "Contenido: " . $response->getContent() . "\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Error en método store:\n";
        echo "   Mensaje: " . $e->getMessage() . "\n";
        echo "   Archivo: " . $e->getFile() . "\n";
        echo "   Línea: " . $e->getLine() . "\n";
        echo "   Trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR GENERAL:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE LA PRUEBA ===\n";
?>
