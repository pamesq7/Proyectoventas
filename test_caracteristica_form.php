<?php
// Test para simular el formulario de creación de características
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Formulario Características</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>🧪 Test Formulario Características</h2>
        
        <?php
        // Conexión directa para obtener opciones
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Obtener opciones
            $stmt = $pdo->query("SELECT * FROM opcions WHERE estado = 1 ORDER BY nombre");
            $opciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "<div class='alert alert-success'>✅ Conexión exitosa - " . count($opciones) . " opciones disponibles</div>";
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Error de conexión: " . $e->getMessage() . "</div>";
            $opciones = [];
        }
        ?>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📝 Formulario de Prueba</h5>
                    </div>
                    <div class="card-body">
                        <form id="testForm">
                            <div class="mb-3">
                                <label for="idOpcion" class="form-label">Opción *</label>
                                <select class="form-select" id="idOpcion" name="idOpcion" required>
                                    <option value="">Selecciona una opción</option>
                                    <?php foreach ($opciones as $opcion): ?>
                                    <option value="<?= $opcion['idOpcion'] ?>"><?= htmlspecialchars($opcion['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre *</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required maxlength="100" placeholder="Ej: Pequeño, Rojo, Algodón">
                                <div class="form-text">Máximo 100 caracteres</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="255" placeholder="Descripción opcional..."></textarea>
                                <div class="form-text">Máximo 255 caracteres</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Crear Característica
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>📊 Resultado</h5>
                    </div>
                    <div class="card-body">
                        <div id="resultado">
                            <p class="text-muted">Completa el formulario y envía para ver el resultado</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        <h5>📋 Opciones Disponibles</h5>
                    </div>
                    <div class="card-body">
                        <?php if (count($opciones) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($opciones as $opcion): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <span><?= htmlspecialchars($opcion['nombre']) ?></span>
                                <span class="badge bg-primary"><?= $opcion['idOpcion'] ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php else: ?>
                        <div class="alert alert-warning">No hay opciones disponibles</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        $('#testForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('_token', 'test-token'); // Token simulado
            
            // Mostrar datos que se enviarían
            let datos = {};
            for (let [key, value] of formData.entries()) {
                datos[key] = value;
            }
            
            $('#resultado').html(`
                <div class="alert alert-info">
                    <h6>📤 Datos que se enviarían a Laravel:</h6>
                    <pre>${JSON.stringify(datos, null, 2)}</pre>
                </div>
                <div class="alert alert-warning">
                    <strong>Siguiente paso:</strong> Probar desde la interfaz real de Laravel
                    <br><a href="/TESIS/VENTAS1/VENTAS/public/configuracion" target="_blank" class="btn btn-sm btn-primary mt-2">🔗 Ir a Configuración Laravel</a>
                </div>
            `);
            
            // Simular petición AJAX (comentado para evitar errores)
            /*
            $.ajax({
                url: '/TESIS/VENTAS1/VENTAS/public/caracteristicas',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#resultado').html('<div class="alert alert-success">✅ Característica creada correctamente</div>');
                },
                error: function(xhr) {
                    $('#resultado').html('<div class="alert alert-danger">❌ Error: ' + xhr.responseText + '</div>');
                }
            });
            */
        });
    });
    </script>
</body>
</html>
