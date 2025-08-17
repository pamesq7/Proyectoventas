<?php
// Test simple de conexión y datos
try {
    // Configuración de base de datos (ajustar según tu configuración)
    $host = 'localhost';
    $dbname = 'laravel'; // Nombre por defecto de Laravel
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>✅ Conexión a base de datos exitosa</h2>";
    
    // Verificar opciones
    $stmt = $pdo->query("SELECT * FROM opcions ORDER BY idOpcion");
    $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📊 Opciones en BD (" . count($opciones) . "):</h3>";
    echo "<ul>";
    foreach ($opciones as $opcion) {
        echo "<li>ID: {$opcion['idOpcion']} - {$opcion['nombre']} (Estado: {$opcion['estado']})</li>";
    }
    echo "</ul>";
    
    // Verificar características
    $stmt = $pdo->query("SELECT c.*, o.nombre as opcion_nombre FROM caracteristicas c LEFT JOIN opcions o ON c.idOpcion = o.idOpcion ORDER BY c.idCaracteristica");
    $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>📊 Características en BD (" . count($caracteristicas) . "):</h3>";
    echo "<ul>";
    foreach ($caracteristicas as $car) {
        echo "<li>ID: {$car['idCaracteristica']} - {$car['nombre']} (Opción: {$car['opcion_nombre']})</li>";
    }
    echo "</ul>";
    
    // Si no hay opciones, insertar algunas básicas
    if (count($opciones) == 0) {
        echo "<h3>⚠️ Insertando opciones básicas...</h3>";
        $insertOpciones = [
            ['Fútbol', 'Productos relacionados con fútbol', 1],
            ['Vestir', 'Productos de vestir', 1],
            ['Corto', 'Productos de corte corto', 1]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO opcions (nombre, descripcion, estado, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        
        foreach ($insertOpciones as $opcion) {
            $stmt->execute($opcion);
        }
        
        echo "<p>✅ Opciones insertadas correctamente</p>";
    }
    
} catch (PDOException $e) {
    echo "<h2>❌ Error de conexión: " . $e->getMessage() . "</h2>";
    echo "<p>Verifica que:</p>";
    echo "<ul>";
    echo "<li>XAMPP esté iniciado</li>";
    echo "<li>MySQL esté corriendo</li>";
    echo "<li>La base de datos 'ventas_db' exista</li>";
    echo "<li>Las credenciales sean correctas</li>";
    echo "</ul>";
}
?>
