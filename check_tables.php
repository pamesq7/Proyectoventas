<?php
// Script para verificar estructura de tablas y datos
try {
    $host = 'localhost';
    $dbname = 'laravel';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>🔍 Verificación de Tablas y Datos</h2>";
    
    // 1. Verificar si las tablas existen
    $tables = ['opcions', 'caracteristicas', 'migrations'];
    
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>✅ Tabla '$table' existe - Registros: {$result['count']}</p>";
        } catch (PDOException $e) {
            echo "<p>❌ Tabla '$table' NO existe o hay error: " . $e->getMessage() . "</p>";
        }
    }
    
    // 2. Verificar estructura de tabla opcions
    echo "<h3>📋 Estructura tabla 'opcions':</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE opcions");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "<p>❌ Error al obtener estructura: " . $e->getMessage() . "</p>";
    }
    
    // 3. Verificar estructura de tabla caracteristicas
    echo "<h3>📋 Estructura tabla 'caracteristicas':</h3>";
    try {
        $stmt = $pdo->query("DESCRIBE caracteristicas");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td></tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "<p>❌ Error al obtener estructura: " . $e->getMessage() . "</p>";
    }
    
    // 4. Insertar datos de prueba si no existen
    echo "<h3>💾 Insertando datos de prueba...</h3>";
    
    // Verificar si hay opciones
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM opcions");
    $opcionesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($opcionesCount == 0) {
        echo "<p>Insertando opciones...</p>";
        $opciones = [
            ['Fútbol', 'Productos relacionados con fútbol', 1],
            ['Vestir', 'Productos de vestir', 1],
            ['Corto', 'Productos de corte corto', 1],
            ['Chamarra', 'Productos tipo chamarra', 1]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO opcions (nombre, descripcion, estado, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        
        foreach ($opciones as $opcion) {
            $stmt->execute($opcion);
        }
        echo "<p>✅ Opciones insertadas</p>";
    } else {
        echo "<p>✅ Ya existen $opcionesCount opciones</p>";
    }
    
    // Mostrar opciones actuales
    echo "<h3>📊 Opciones disponibles:</h3>";
    $stmt = $pdo->query("SELECT * FROM opcions ORDER BY idOpcion");
    $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($opciones as $opcion) {
        echo "<li>ID: {$opcion['idOpcion']} - {$opcion['nombre']} (Estado: {$opcion['estado']})</li>";
    }
    echo "</ul>";
    
    // Mostrar características actuales
    echo "<h3>📊 Características disponibles:</h3>";
    $stmt = $pdo->query("SELECT c.*, o.nombre as opcion_nombre FROM caracteristicas c LEFT JOIN opcions o ON c.idOpcion = o.idOpcion ORDER BY c.idCaracteristica");
    $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($caracteristicas as $car) {
        echo "<li>ID: {$car['idCaracteristica']} - {$car['nombre']} (Opción: {$car['opcion_nombre']})</li>";
    }
    echo "</ul>";
    
    echo "<h3>🎯 Prueba de inserción directa:</h3>";
    
    // Probar inserción directa
    if (count($opciones) > 0) {
        $primeraOpcion = $opciones[0]['idOpcion'];
        $nombreTest = "Test " . date('H:i:s');
        
        try {
            $stmt = $pdo->prepare("INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$nombreTest, 'Característica de prueba', 1, $primeraOpcion]);
            
            echo "<p>✅ Característica '$nombreTest' insertada correctamente</p>";
        } catch (PDOException $e) {
            echo "<p>❌ Error al insertar: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Error de conexión: " . $e->getMessage() . "</h2>";
    echo "<p>Verifica que XAMPP esté iniciado y MySQL corriendo</p>";
}
?>
