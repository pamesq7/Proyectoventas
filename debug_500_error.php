<?php
// Debug específico para error 500 en creación de características
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Error 500 - Características</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>🔍 Debug Error 500 - Creación Características</h2>
        
        <?php
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="alert alert-success">✅ Conexión BD exitosa</div>';
            
            // 1. Verificar estructura tabla características
            echo '<h3>📋 Estructura tabla características:</h3>';
            $stmt = $pdo->query("DESCRIBE caracteristicas");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table class="table table-sm">';
            echo '<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>';
            foreach ($columns as $col) {
                echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
            }
            echo '</table>';
            
            // 2. Verificar si las opciones existen
            echo '<h3>📊 Opciones disponibles:</h3>';
            $stmt = $pdo->query("SELECT * FROM opcions WHERE estado = 1 ORDER BY idOpcion");
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($opciones) > 0) {
                echo '<div class="row">';
                foreach ($opciones as $opcion) {
                    echo '<div class="col-md-4 mb-2">';
                    echo '<div class="card">';
                    echo '<div class="card-body p-2">';
                    echo "<strong>ID: {$opcion['idOpcion']}</strong> - {$opcion['nombre']}";
                    echo '</div></div></div>';
                }
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning">⚠️ No hay opciones activas</div>';
                
                // Insertar opciones básicas
                echo '<p>Insertando opciones básicas...</p>';
                $opcionesBasicas = [
                    ['Fútbol', 'Productos relacionados con fútbol', 1],
                    ['Vestir', 'Productos de vestir', 1],
                    ['Corto', 'Productos de corte corto', 1],
                    ['Chamarra', 'Productos tipo chamarra', 1],
                    ['capucha', 'Productos con capucha', 1]
                ];
                
                $stmt = $pdo->prepare("INSERT INTO opcions (nombre, descripcion, estado, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
                
                foreach ($opcionesBasicas as $opcion) {
                    try {
                        $stmt->execute($opcion);
                        echo "<p>✅ Opción '{$opcion[0]}' insertada</p>";
                    } catch (Exception $e) {
                        echo "<p>⚠️ Opción '{$opcion[0]}': {$e->getMessage()}</p>";
                    }
                }
                
                // Recargar opciones
                $stmt = $pdo->query("SELECT * FROM opcions WHERE estado = 1 ORDER BY idOpcion");
                $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // 3. Simular exactamente lo que hace Laravel
            echo '<h3>🧪 Simulación Laravel - Creación Característica:</h3>';
            
            if (count($opciones) > 0) {
                $primeraOpcion = $opciones[0];
                
                // Datos exactos como los envía el formulario
                $requestData = [
                    'idOpcion' => $primeraOpcion['idOpcion'],
                    'nombre' => 'Test Debug ' . date('H:i:s'),
                    'descripcion' => 'Característica de prueba para debug'
                ];
                
                echo '<div class="card">';
                echo '<div class="card-header">Datos del request simulado:</div>';
                echo '<div class="card-body">';
                echo '<pre>' . json_encode($requestData, JSON_PRETTY_PRINT) . '</pre>';
                
                // Paso 1: Validaciones como Laravel
                echo '<h5>1. Validaciones:</h5>';
                $errores = [];
                
                // Validar idOpcion
                if (empty($requestData['idOpcion'])) {
                    $errores[] = 'idOpcion es requerido';
                } else {
                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM opcions WHERE idOpcion = ?");
                    $stmt->execute([$requestData['idOpcion']]);
                    if ($stmt->fetch()['count'] == 0) {
                        $errores[] = 'idOpcion no existe en tabla opcions';
                    }
                }
                
                // Validar nombre
                if (empty($requestData['nombre'])) {
                    $errores[] = 'nombre es requerido';
                } elseif (strlen($requestData['nombre']) > 100) {
                    $errores[] = 'nombre debe tener máximo 100 caracteres';
                }
                
                // Validar descripcion
                if (!empty($requestData['descripcion']) && strlen($requestData['descripcion']) > 255) {
                    $errores[] = 'descripcion debe tener máximo 255 caracteres';
                }
                
                if (count($errores) > 0) {
                    echo '<div class="alert alert-danger">';
                    echo '<strong>Errores de validación:</strong><ul>';
                    foreach ($errores as $error) {
                        echo "<li>$error</li>";
                    }
                    echo '</ul></div>';
                } else {
                    echo '<div class="alert alert-success">✅ Validaciones pasaron</div>';
                    
                    // Paso 2: Inserción como Eloquent
                    echo '<h5>2. Inserción en BD:</h5>';
                    
                    try {
                        $stmt = $pdo->prepare("
                            INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion, created_at, updated_at) 
                            VALUES (?, ?, ?, ?, NOW(), NOW())
                        ");
                        
                        $resultado = $stmt->execute([
                            $requestData['nombre'],
                            $requestData['descripcion'],
                            1, // estado siempre 1
                            $requestData['idOpcion']
                        ]);
                        
                        if ($resultado) {
                            $nuevoId = $pdo->lastInsertId();
                            echo "<div class='alert alert-success'>✅ Inserción exitosa - ID: $nuevoId</div>";
                            
                            // Verificar que se guardó
                            $stmt = $pdo->prepare("SELECT * FROM caracteristicas WHERE idCaracteristica = ?");
                            $stmt->execute([$nuevoId]);
                            $verificacion = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            echo '<h5>3. Verificación:</h5>';
                            echo '<pre>' . json_encode($verificacion, JSON_PRETTY_PRINT) . '</pre>';
                            
                        } else {
                            echo '<div class="alert alert-danger">❌ Error: La inserción falló</div>';
                        }
                        
                    } catch (PDOException $e) {
                        echo '<div class="alert alert-danger">';
                        echo '<strong>❌ Error SQL:</strong> ' . $e->getMessage() . '<br>';
                        echo '<strong>Código:</strong> ' . $e->getCode() . '<br>';
                        echo '<strong>Archivo:</strong> ' . $e->getFile() . '<br>';
                        echo '<strong>Línea:</strong> ' . $e->getLine();
                        echo '</div>';
                    }
                }
                
                echo '</div></div>';
            }
            
            // 4. Diagnóstico específico del error 500
            echo '<h3>🔍 Diagnóstico Error 500:</h3>';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5>Posibles causas del error 500:</h5>';
            echo '<ol>';
            echo '<li><strong>Modelo Caracteristica:</strong> Campos no están en $fillable</li>';
            echo '<li><strong>Migraciones:</strong> Tabla no existe o estructura incorrecta</li>';
            echo '<li><strong>Conexión BD:</strong> Laravel no puede conectar a la BD</li>';
            echo '<li><strong>Validaciones:</strong> Reglas de validación incorrectas</li>';
            echo '<li><strong>Middleware:</strong> Problemas con CSRF o autenticación</li>';
            echo '</ol>';
            
            echo '<h5>Verificaciones realizadas:</h5>';
            echo '<ul>';
            echo '<li>✅ Conexión directa a BD funciona</li>';
            echo '<li>✅ Tabla características existe</li>';
            echo '<li>✅ Opciones disponibles</li>';
            echo '<li>✅ Inserción directa funciona</li>';
            echo '<li>✅ Token CSRF agregado</li>';
            echo '</ul>';
            
            echo '<h5>Siguiente paso:</h5>';
            echo '<p>Revisar el modelo Caracteristica y verificar que los campos estén en $fillable</p>';
            echo '</div></div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
        }
        ?>
        
        <div class="mt-3">
            <a href="/TESIS/VENTAS1/VENTAS/public/configuracion" target="_blank" class="btn btn-primary">🔗 Probar en Laravel</a>
        </div>
    </div>
</body>
</html>
