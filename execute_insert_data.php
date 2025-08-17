<?php
// Script para ejecutar insert_data.sql y verificar resultados
header('Content-Type: text/html; charset=utf-8');

echo "<h2>🚀 Ejecutando Script SQL insert_data.sql</h2>";

try {
    // Conexión a base de datos
    $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Conexión exitosa a base de datos</p>";
    
    // Leer el archivo SQL
    $sqlFile = 'insert_data.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("Archivo $sqlFile no encontrado");
    }
    
    $sqlContent = file_get_contents($sqlFile);
    echo "<p>✅ Archivo SQL leído correctamente</p>";
    
    // Dividir en consultas individuales
    $queries = explode(';', $sqlContent);
    $queries = array_filter($queries, function($query) {
        return trim($query) !== '' && !preg_match('/^\s*--/', trim($query));
    });
    
    echo "<p>📊 Total de consultas a ejecutar: " . count($queries) . "</p>";
    
    // Verificar estado actual antes de insertar
    echo "<h3>📋 Estado ANTES de insertar:</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM opcions");
    $opcionesAntes = $stmt->fetch()['count'];
    echo "<p>Opciones: $opcionesAntes</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM caracteristicas");
    $caracteristicasAntes = $stmt->fetch()['count'];
    echo "<p>Características: $caracteristicasAntes</p>";
    
    // Ejecutar consultas
    echo "<h3>⚡ Ejecutando consultas:</h3>";
    
    $pdo->beginTransaction();
    
    $ejecutadas = 0;
    foreach ($queries as $query) {
        $query = trim($query);
        if ($query) {
            try {
                $pdo->exec($query);
                $ejecutadas++;
                echo "<p>✅ Consulta $ejecutadas ejecutada</p>";
            } catch (PDOException $e) {
                // Si es error de duplicado, continuar
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    echo "<p>⚠️ Consulta $ejecutadas: Datos ya existen (ignorado)</p>";
                } else {
                    throw $e;
                }
            }
        }
    }
    
    $pdo->commit();
    echo "<p>✅ Todas las consultas ejecutadas correctamente</p>";
    
    // Verificar estado después de insertar
    echo "<h3>📊 Estado DESPUÉS de insertar:</h3>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM opcions");
    $opcionesDespues = $stmt->fetch()['count'];
    echo "<p>Opciones: $opcionesDespues (+". ($opcionesDespues - $opcionesAntes) .")</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM caracteristicas");
    $caracteristicasDespues = $stmt->fetch()['count'];
    echo "<p>Características: $caracteristicasDespues (+". ($caracteristicasDespues - $caracteristicasAntes) .")</p>";
    
    // Mostrar opciones insertadas
    echo "<h3>📋 Opciones disponibles:</h3>";
    $stmt = $pdo->query("SELECT * FROM opcions ORDER BY idOpcion");
    $opciones = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th></tr>";
    foreach ($opciones as $opcion) {
        $estadoTexto = $opcion['estado'] ? 'Activo' : 'Inactivo';
        echo "<tr><td>{$opcion['idOpcion']}</td><td>{$opcion['nombre']}</td><td>{$opcion['descripcion']}</td><td>$estadoTexto</td></tr>";
    }
    echo "</table>";
    
    // Mostrar características por opción
    echo "<h3>📋 Características por opción:</h3>";
    $stmt = $pdo->query("
        SELECT o.nombre as opcion_nombre, COUNT(c.idCaracteristica) as total_caracteristicas
        FROM opcions o 
        LEFT JOIN caracteristicas c ON o.idOpcion = c.idOpcion 
        GROUP BY o.idOpcion, o.nombre 
        ORDER BY o.nombre
    ");
    $resumen = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Opción</th><th>Total Características</th></tr>";
    foreach ($resumen as $item) {
        echo "<tr><td>{$item['opcion_nombre']}</td><td>{$item['total_caracteristicas']}</td></tr>";
    }
    echo "</table>";
    
    // Mostrar todas las características
    echo "<h3>📋 Todas las características:</h3>";
    $stmt = $pdo->query("
        SELECT c.*, o.nombre as opcion_nombre 
        FROM caracteristicas c 
        LEFT JOIN opcions o ON c.idOpcion = o.idOpcion 
        ORDER BY o.nombre, c.nombre
    ");
    $caracteristicas = $stmt->fetchAll();
    
    if (count($caracteristicas) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Opción</th><th>Estado</th></tr>";
        foreach ($caracteristicas as $car) {
            $estadoTexto = $car['estado'] ? 'Activo' : 'Inactivo';
            $descripcion = $car['descripcion'] ?: '-';
            echo "<tr><td>{$car['idCaracteristica']}</td><td>{$car['nombre']}</td><td>$descripcion</td><td>{$car['opcion_nombre']}</td><td>$estadoTexto</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ No se encontraron características</p>";
    }
    
    echo "<hr>";
    echo "<h3>🎯 Próximos pasos:</h3>";
    echo "<ol>";
    echo "<li>✅ Datos insertados correctamente</li>";
    echo "<li>🔗 <a href='/TESIS/VENTAS1/VENTAS/public/configuracion' target='_blank'>Ir a Configuración Laravel</a></li>";
    echo "<li>🧪 Probar creación de nuevas características</li>";
    echo "<li>👀 Verificar que los datos se muestren en la interfaz</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p>Línea: " . $e->getLine() . "</p>";
    echo "<p>Archivo: " . $e->getFile() . "</p>";
}
?>
