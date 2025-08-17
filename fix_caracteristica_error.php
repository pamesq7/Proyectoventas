<?php
// Script para diagnosticar y solucionar el error específico de creación de características
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix - Error Creación Características</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>🔧 Diagnóstico y Solución - Error Creación Características</h2>
        
        <?php
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="alert alert-success">✅ Conexión exitosa</div>';
            
            // 1. Verificar si existe la opción "capucha"
            echo '<h3>🔍 Paso 1: Verificar opciones disponibles</h3>';
            $stmt = $pdo->query("SELECT * FROM opcions ORDER BY idOpcion");
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table class="table table-sm">';
            echo '<tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Descripción</th></tr>';
            
            $capuchaOpcion = null;
            foreach ($opciones as $opcion) {
                $estadoTexto = $opcion['estado'] ? 'Activo' : 'Inactivo';
                $badgeClass = $opcion['estado'] ? 'bg-success' : 'bg-secondary';
                
                echo "<tr>";
                echo "<td>{$opcion['idOpcion']}</td>";
                echo "<td><strong>{$opcion['nombre']}</strong></td>";
                echo "<td><span class='badge $badgeClass'>$estadoTexto</span></td>";
                echo "<td>{$opcion['descripcion']}</td>";
                echo "</tr>";
                
                if (strtolower($opcion['nombre']) === 'capucha') {
                    $capuchaOpcion = $opcion;
                }
            }
            echo '</table>';
            
            // 2. Verificar si existe la opción "capucha" específicamente
            if (!$capuchaOpcion) {
                echo '<div class="alert alert-warning">';
                echo '<h5>⚠️ Problema identificado: No existe la opción "capucha"</h5>';
                echo '<p>Necesitamos crear la opción "capucha" primero.</p>';
                
                // Crear la opción capucha
                try {
                    $stmt = $pdo->prepare("INSERT INTO opcions (nombre, descripcion, estado, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                    $stmt->execute(['capucha', 'Productos con capucha', 1]);
                    
                    $capuchaId = $pdo->lastInsertId();
                    echo "<div class='alert alert-success'>✅ Opción 'capucha' creada con ID: $capuchaId</div>";
                    
                    $capuchaOpcion = [
                        'idOpcion' => $capuchaId,
                        'nombre' => 'capucha',
                        'descripcion' => 'Productos con capucha',
                        'estado' => 1
                    ];
                    
                } catch (Exception $e) {
                    echo "<div class='alert alert-danger'>❌ Error al crear opción: {$e->getMessage()}</div>";
                }
                
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">✅ La opción "capucha" existe con ID: ' . $capuchaOpcion['idOpcion'] . '</div>';
            }
            
            // 3. Intentar crear la característica "sin capucha"
            if ($capuchaOpcion) {
                echo '<h3>🧪 Paso 2: Probar creación de característica "sin capucha"</h3>';
                
                $caracteristicaData = [
                    'nombre' => 'sin capucha',
                    'descripcion' => 'Producto sin capucha',
                    'estado' => 1,
                    'idOpcion' => $capuchaOpcion['idOpcion']
                ];
                
                echo '<div class="card">';
                echo '<div class="card-header">Datos a insertar:</div>';
                echo '<div class="card-body">';
                echo '<pre>' . json_encode($caracteristicaData, JSON_PRETTY_PRINT) . '</pre>';
                
                try {
                    // Verificar si ya existe
                    $stmt = $pdo->prepare("SELECT * FROM caracteristicas WHERE nombre = ? AND idOpcion = ?");
                    $stmt->execute([$caracteristicaData['nombre'], $caracteristicaData['idOpcion']]);
                    $existe = $stmt->fetch();
                    
                    if ($existe) {
                        echo '<div class="alert alert-warning">⚠️ La característica "sin capucha" ya existe</div>';
                        echo '<pre>' . json_encode($existe, JSON_PRETTY_PRINT) . '</pre>';
                    } else {
                        // Insertar nueva característica
                        $stmt = $pdo->prepare("INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                        $resultado = $stmt->execute([
                            $caracteristicaData['nombre'],
                            $caracteristicaData['descripcion'],
                            $caracteristicaData['estado'],
                            $caracteristicaData['idOpcion']
                        ]);
                        
                        if ($resultado) {
                            $nuevoId = $pdo->lastInsertId();
                            echo "<div class='alert alert-success'>✅ Característica creada exitosamente con ID: $nuevoId</div>";
                            
                            // Verificar inserción
                            $stmt = $pdo->prepare("SELECT c.*, o.nombre as opcion_nombre FROM caracteristicas c LEFT JOIN opcions o ON c.idOpcion = o.idOpcion WHERE c.idCaracteristica = ?");
                            $stmt->execute([$nuevoId]);
                            $verificacion = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            echo '<div class="alert alert-info">Característica creada:</div>';
                            echo '<pre>' . json_encode($verificacion, JSON_PRETTY_PRINT) . '</pre>';
                        }
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">❌ Error al crear característica: ' . $e->getMessage() . '</div>';
                    echo '<p><strong>Detalles del error:</strong></p>';
                    echo '<ul>';
                    echo '<li>Archivo: ' . $e->getFile() . '</li>';
                    echo '<li>Línea: ' . $e->getLine() . '</li>';
                    echo '</ul>';
                }
                
                echo '</div></div>';
            }
            
            // 4. Mostrar todas las características actuales
            echo '<h3>📊 Paso 3: Características actuales en la BD</h3>';
            $stmt = $pdo->query("
                SELECT c.*, o.nombre as opcion_nombre 
                FROM caracteristicas c 
                LEFT JOIN opcions o ON c.idOpcion = o.idOpcion 
                ORDER BY o.nombre, c.nombre
            ");
            $caracteristicas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($caracteristicas) > 0) {
                echo '<table class="table table-sm">';
                echo '<tr><th>ID</th><th>Nombre</th><th>Descripción</th><th>Opción</th><th>Estado</th></tr>';
                foreach ($caracteristicas as $car) {
                    $estadoTexto = $car['estado'] ? 'Activo' : 'Inactivo';
                    $badgeClass = $car['estado'] ? 'bg-success' : 'bg-secondary';
                    echo "<tr>";
                    echo "<td>{$car['idCaracteristica']}</td>";
                    echo "<td><strong>{$car['nombre']}</strong></td>";
                    echo "<td>{$car['descripcion']}</td>";
                    echo "<td>{$car['opcion_nombre']}</td>";
                    echo "<td><span class='badge $badgeClass'>$estadoTexto</span></td>";
                    echo "</tr>";
                }
                echo '</table>';
            } else {
                echo '<div class="alert alert-info">No hay características en la base de datos</div>';
            }
            
            // 5. Diagnóstico del problema en Laravel
            echo '<h3>🔍 Paso 4: Posibles causas del error en Laravel</h3>';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5>Posibles causas del error:</h5>';
            echo '<ol>';
            echo '<li><strong>Token CSRF:</strong> El formulario puede no estar enviando el token CSRF correctamente</li>';
            echo '<li><strong>Validación:</strong> Las validaciones de Laravel pueden estar fallando</li>';
            echo '<li><strong>Modelo:</strong> El modelo Caracteristica puede tener problemas con los campos fillable</li>';
            echo '<li><strong>Base de datos:</strong> Problemas de conexión o estructura</li>';
            echo '</ol>';
            
            echo '<h5>Soluciones recomendadas:</h5>';
            echo '<ul>';
            echo '<li>Verificar que el formulario incluya @csrf</li>';
            echo '<li>Revisar las validaciones en CaracteristicaController</li>';
            echo '<li>Verificar que los campos estén en $fillable del modelo</li>';
            echo '<li>Comprobar los logs de Laravel para errores específicos</li>';
            echo '</ul>';
            echo '</div></div>';
            
            echo '<div class="mt-3">';
            echo '<a href="/TESIS/VENTAS1/VENTAS/public/configuracion" target="_blank" class="btn btn-primary">🔗 Probar en Laravel</a>';
            echo '<a href="debug_caracteristica_creation.php" target="_blank" class="btn btn-info ms-2">🔍 Debug Avanzado</a>';
            echo '</div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>
</body>
</html>
