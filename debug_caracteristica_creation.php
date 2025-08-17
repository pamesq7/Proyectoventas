<?php
// Script para diagnosticar error al crear características
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug - Error Creación Características</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>🔍 Diagnóstico - Error al Crear Características</h2>
        
        <?php
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="alert alert-success">✅ Conexión a BD exitosa</div>';
            
            // 1. Verificar estructura de tabla características
            echo '<h3>📋 Estructura tabla características:</h3>';
            $stmt = $pdo->query("DESCRIBE caracteristicas");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<table class="table table-sm">';
            echo '<tr><th>Campo</th><th>Tipo</th><th>Null</th><th>Key</th><th>Default</th></tr>';
            foreach ($columns as $col) {
                echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Key']}</td><td>{$col['Default']}</td></tr>";
            }
            echo '</table>';
            
            // 2. Verificar opciones disponibles
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
            }
            
            // 3. Simular inserción paso a paso
            echo '<h3>🧪 Simulación de Inserción:</h3>';
            
            if (count($opciones) > 0) {
                $primeraOpcion = $opciones[0];
                $datosTest = [
                    'nombre' => 'Test Debug ' . date('H:i:s'),
                    'descripcion' => 'Característica de prueba para debug',
                    'estado' => 1,
                    'idOpcion' => $primeraOpcion['idOpcion']
                ];
                
                echo '<div class="card">';
                echo '<div class="card-header">Datos a insertar:</div>';
                echo '<div class="card-body">';
                echo '<pre>' . json_encode($datosTest, JSON_PRETTY_PRINT) . '</pre>';
                
                // Intentar inserción
                try {
                    $stmt = $pdo->prepare("INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
                    $resultado = $stmt->execute([
                        $datosTest['nombre'],
                        $datosTest['descripcion'], 
                        $datosTest['estado'],
                        $datosTest['idOpcion']
                    ]);
                    
                    if ($resultado) {
                        $nuevoId = $pdo->lastInsertId();
                        echo '<div class="alert alert-success">✅ Inserción exitosa - ID: ' . $nuevoId . '</div>';
                        
                        // Verificar que se guardó
                        $stmt = $pdo->prepare("SELECT * FROM caracteristicas WHERE idCaracteristica = ?");
                        $stmt->execute([$nuevoId]);
                        $verificacion = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($verificacion) {
                            echo '<div class="alert alert-info">✅ Verificación: Registro encontrado en BD</div>';
                            echo '<pre>' . json_encode($verificacion, JSON_PRETTY_PRINT) . '</pre>';
                        }
                    } else {
                        echo '<div class="alert alert-danger">❌ Error: La inserción falló</div>';
                    }
                    
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">❌ Error SQL: ' . $e->getMessage() . '</div>';
                }
                
                echo '</div></div>';
            }
            
            // 4. Probar validaciones de Laravel
            echo '<h3>🔍 Prueba de Validaciones Laravel:</h3>';
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<form id="testLaravelForm">';
            echo '<div class="mb-3">';
            echo '<label class="form-label">Opción:</label>';
            echo '<select class="form-select" name="idOpcion" required>';
            echo '<option value="">Selecciona opción</option>';
            foreach ($opciones as $opcion) {
                echo "<option value='{$opcion['idOpcion']}'>{$opcion['nombre']}</option>";
            }
            echo '</select>';
            echo '</div>';
            
            echo '<div class="mb-3">';
            echo '<label class="form-label">Nombre:</label>';
            echo '<input type="text" class="form-control" name="nombre" value="Test Laravel ' . date('H:i:s') . '" required>';
            echo '</div>';
            
            echo '<div class="mb-3">';
            echo '<label class="form-label">Descripción:</label>';
            echo '<textarea class="form-control" name="descripcion">Prueba desde debug</textarea>';
            echo '</div>';
            
            echo '<button type="submit" class="btn btn-primary">Probar Envío a Laravel</button>';
            echo '</form>';
            echo '</div></div>';
            
            echo '<div id="resultadoLaravel" class="mt-3"></div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">❌ Error: ' . $e->getMessage() . '</div>';
        }
        ?>
    </div>

    <script>
    $('#testLaravelForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.append('_token', 'debug-token'); // Token simulado
        
        // Mostrar datos que se enviarían
        let datos = {};
        for (let [key, value] of formData.entries()) {
            datos[key] = value;
        }
        
        $('#resultadoLaravel').html(`
            <div class="card">
                <div class="card-header">📤 Datos que se enviarían a Laravel:</div>
                <div class="card-body">
                    <pre>${JSON.stringify(datos, null, 2)}</pre>
                    <div class="alert alert-info">
                        <strong>Siguiente paso:</strong> Verificar estos datos en el controlador CaracteristicaController::store()
                    </div>
                </div>
            </div>
        `);
        
        // Intentar envío real (comentado para evitar errores de token)
        /*
        $.ajax({
            url: '/TESIS/VENTAS1/VENTAS/public/caracteristicas',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#resultadoLaravel').html('<div class="alert alert-success">✅ Éxito: ' + JSON.stringify(response) + '</div>');
            },
            error: function(xhr) {
                $('#resultadoLaravel').html('<div class="alert alert-danger">❌ Error: ' + xhr.status + ' - ' + xhr.responseText + '</div>');
            }
        });
        */
    });
    </script>
</body>
</html>
