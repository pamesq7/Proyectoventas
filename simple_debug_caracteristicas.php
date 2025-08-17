<?php
// Script simple para diagnosticar el error de características

try {
    // Conectar directamente a la base de datos
    $host = 'localhost';
    $dbname = 'laravel'; // Cambiar a laravel que es la BD que usa el proyecto
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DIAGNÓSTICO SIMPLE DE CARACTERÍSTICAS ===\n\n";
    
    // 1. Verificar opciones
    echo "1. Verificando opciones:\n";
    $stmt = $pdo->query("SELECT * FROM opcions LIMIT 5");
    $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($opciones)) {
        echo "❌ No hay opciones en la BD\n";
        echo "Ejecuta: INSERT INTO opcions (nombre, descripcion, estado) VALUES ('capucha', 'Tipo de capucha', 1);\n";
        exit;
    }
    
    foreach ($opciones as $opcion) {
        echo "   - ID: {$opcion['idOpcion']}, Nombre: {$opcion['nombre']}\n";
    }
    
    // 2. Verificar estructura de tabla características
    echo "\n2. Verificando estructura de tabla caracteristicas:\n";
    $stmt = $pdo->query("DESCRIBE caracteristicas");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        echo "   - {$col['Field']}: {$col['Type']} ({$col['Null']}, {$col['Key']})\n";
    }
    
    // 3. Probar inserción directa
    echo "\n3. Probando inserción directa:\n";
    $primeraOpcion = $opciones[0]['idOpcion'];
    
    $stmt = $pdo->prepare("
        INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion) 
        VALUES (?, ?, ?, ?)
    ");
    
    $resultado = $stmt->execute([
        'Test Debug',
        'Característica de prueba',
        1,
        $primeraOpcion
    ]);
    
    if ($resultado) {
        $id = $pdo->lastInsertId();
        echo "✅ Característica creada con ID: $id\n";
        
        // Limpiar
        $pdo->exec("DELETE FROM caracteristicas WHERE idCaracteristica = $id");
        echo "✅ Característica de prueba eliminada\n";
    } else {
        echo "❌ Error al insertar\n";
    }
    
    // 4. Verificar características existentes
    echo "\n4. Características existentes:\n";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM caracteristicas");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "   Total: $total características\n";
    
    if ($total > 0) {
        $stmt = $pdo->query("SELECT c.*, o.nombre as opcion_nombre FROM caracteristicas c LEFT JOIN opcions o ON c.idOpcion = o.idOpcion LIMIT 3");
        $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($caracteristicas as $car) {
            echo "   - ID: {$car['idCaracteristica']}, Nombre: {$car['nombre']}, Opción: {$car['opcion_nombre']}\n";
        }
    }
    
    echo "\n✅ Diagnóstico completado - La BD funciona correctamente\n";
    echo "El error 500 debe estar en el código PHP de Laravel\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Verifica:\n";
    echo "1. Que XAMPP esté iniciado\n";
    echo "2. Que la BD 'ventas' exista\n";
    echo "3. Las credenciales de conexión\n";
}
?>
