<?php
// Debug simple para características
header('Content-Type: text/html; charset=utf-8');

echo "<h2>🔍 Debug Características - Sistema VENTAS</h2>";

try {
    // Conexión directa
    $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Conexión exitosa a base de datos</p>";
    
    // Verificar tablas principales
    $tablas = ['migrations', 'opcions', 'caracteristicas'];
    
    foreach ($tablas as $tabla) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM $tabla");
            $count = $stmt->fetch()['total'];
            echo "<p>✅ Tabla '$tabla': $count registros</p>";
        } catch (Exception $e) {
            echo "<p>❌ Tabla '$tabla': " . $e->getMessage() . "</p>";
        }
    }
    
    // Verificar migraciones aplicadas
    echo "<h3>📋 Migraciones aplicadas:</h3>";
    try {
        $stmt = $pdo->query("SELECT migration FROM migrations WHERE migration LIKE '%caracteristicas%' OR migration LIKE '%opcions%'");
        $migraciones = $stmt->fetchAll();
        
        if (count($migraciones) > 0) {
            echo "<ul>";
            foreach ($migraciones as $mig) {
                echo "<li>{$mig['migration']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>⚠️ No se encontraron migraciones de opciones/características</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error al verificar migraciones: " . $e->getMessage() . "</p>";
    }
    
    // Insertar datos básicos si no existen
    echo "<h3>💾 Verificando/Insertando datos básicos:</h3>";
    
    // Verificar opciones
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM opcions");
    $opcionesCount = $stmt->fetch()['count'];
    
    if ($opcionesCount == 0) {
        echo "<p>Insertando opciones básicas...</p>";
        
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
        
        echo "<p>✅ 4 opciones insertadas</p>";
    } else {
        echo "<p>✅ Ya existen $opcionesCount opciones</p>";
    }
    
    // Mostrar opciones disponibles
    echo "<h3>📊 Opciones disponibles:</h3>";
    $stmt = $pdo->query("SELECT * FROM opcions ORDER BY idOpcion");
    $opciones = $stmt->fetchAll();
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Estado</th></tr>";
    foreach ($opciones as $opcion) {
        $estadoTexto = $opcion['estado'] ? 'Activo' : 'Inactivo';
        echo "<tr><td>{$opcion['idOpcion']}</td><td>{$opcion['nombre']}</td><td>{$opcion['descripcion']}</td><td>$estadoTexto</td></tr>";
    }
    echo "</table>";
    
    // Mostrar características
    echo "<h3>📊 Características disponibles:</h3>";
    $stmt = $pdo->query("SELECT c.*, o.nombre as opcion_nombre FROM caracteristicas c LEFT JOIN opcions o ON c.idOpcion = o.idOpcion ORDER BY c.idCaracteristica");
    $caracteristicas = $stmt->fetchAll();
    
    if (count($caracteristicas) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Opción</th><th>Estado</th></tr>";
        foreach ($caracteristicas as $car) {
            $estadoTexto = $car['estado'] ? 'Activo' : 'Inactivo';
            echo "<tr><td>{$car['idCaracteristica']}</td><td>{$car['nombre']}</td><td>{$car['descripcion']}</td><td>{$car['opcion_nombre']}</td><td>$estadoTexto</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ No hay características en la base de datos</p>";
    }
    
    // Probar inserción de característica de prueba
    echo "<h3>🧪 Prueba de inserción:</h3>";
    
    if (count($opciones) > 0) {
        $primeraOpcion = $opciones[0]['idOpcion'];
        $nombreTest = "Test Característica " . date('H:i:s');
        
        try {
            $stmt = $pdo->prepare("INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $resultado = $stmt->execute([$nombreTest, 'Prueba automática', 1, $primeraOpcion]);
            
            if ($resultado) {
                $nuevoId = $pdo->lastInsertId();
                echo "<p>✅ Característica '$nombreTest' insertada con ID: $nuevoId</p>";
            } else {
                echo "<p>❌ Error al insertar característica</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error en inserción: " . $e->getMessage() . "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>🎯 Conclusiones:</h3>";
    echo "<ul>";
    echo "<li>Base de datos: " . (count($opciones) > 0 ? "✅ Funcionando" : "❌ Sin datos") . "</li>";
    echo "<li>Opciones: " . count($opciones) . " disponibles</li>";
    echo "<li>Características: " . count($caracteristicas) . " disponibles</li>";
    echo "</ul>";
    
    echo "<p><strong>Siguiente paso:</strong> Probar creación desde la interfaz web Laravel</p>";
    echo "<p><a href='/TESIS/VENTAS1/VENTAS/public/configuracion' target='_blank'>🔗 Ir a Configuración Laravel</a></p>";
    
} catch (Exception $e) {
    echo "<h2>❌ Error: " . $e->getMessage() . "</h2>";
    echo "<p>Verifica que XAMPP esté iniciado y MySQL corriendo</p>";
}
?>
