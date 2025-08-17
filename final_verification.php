<?php
// Verificación final del sistema VENTAS - Configuración de características
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verificación Final - Sistema VENTAS</title>
    <meta charset="utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-3">
        <h1 class="text-center mb-4">🎯 Verificación Final - Sistema VENTAS</h1>
        
        <?php
        try {
            // Conexión a base de datos
            $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Conexión exitosa a base de datos</div>';
            
            // 1. Verificar estructura de tablas
            echo '<div class="row">';
            echo '<div class="col-md-6">';
            echo '<div class="card">';
            echo '<div class="card-header bg-primary text-white"><h5><i class="fas fa-database"></i> Estado de Tablas</h5></div>';
            echo '<div class="card-body">';
            
            $tablas = ['migrations', 'opcions', 'caracteristicas', 'categorias', 'productos'];
            $estadoTablas = [];
            
            foreach ($tablas as $tabla) {
                try {
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $tabla");
                    $count = $stmt->fetch()['count'];
                    $estadoTablas[$tabla] = $count;
                    echo "<p><i class='fas fa-check text-success'></i> <strong>$tabla:</strong> $count registros</p>";
                } catch (Exception $e) {
                    echo "<p><i class='fas fa-times text-danger'></i> <strong>$tabla:</strong> Error - " . $e->getMessage() . "</p>";
                }
            }
            
            echo '</div></div></div>';
            
            // 2. Verificar datos de configuración
            echo '<div class="col-md-6">';
            echo '<div class="card">';
            echo '<div class="card-header bg-info text-white"><h5><i class="fas fa-cogs"></i> Datos de Configuración</h5></div>';
            echo '<div class="card-body">';
            
            // Opciones
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM opcions WHERE estado = 1");
            $opcionesActivas = $stmt->fetch()['count'];
            echo "<p><i class='fas fa-tags text-primary'></i> <strong>Opciones activas:</strong> $opcionesActivas</p>";
            
            // Características
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM caracteristicas WHERE estado = 1");
            $caracteristicasActivas = $stmt->fetch()['count'];
            echo "<p><i class='fas fa-list text-success'></i> <strong>Características activas:</strong> $caracteristicasActivas</p>";
            
            // Categorías
            if (isset($estadoTablas['categorias'])) {
                echo "<p><i class='fas fa-folder text-warning'></i> <strong>Categorías:</strong> {$estadoTablas['categorias']}</p>";
            }
            
            // Productos
            if (isset($estadoTablas['productos'])) {
                echo "<p><i class='fas fa-box text-info'></i> <strong>Productos:</strong> {$estadoTablas['productos']}</p>";
            }
            
            echo '</div></div></div>';
            echo '</div>';
            
            // 3. Mostrar opciones disponibles
            echo '<div class="row mt-4">';
            echo '<div class="col-12">';
            echo '<div class="card">';
            echo '<div class="card-header bg-success text-white"><h5><i class="fas fa-list-ul"></i> Opciones Disponibles</h5></div>';
            echo '<div class="card-body">';
            
            $stmt = $pdo->query("SELECT * FROM opcions ORDER BY nombre");
            $opciones = $stmt->fetchAll();
            
            if (count($opciones) > 0) {
                echo '<div class="row">';
                foreach ($opciones as $opcion) {
                    $estadoBadge = $opcion['estado'] ? 'bg-success' : 'bg-secondary';
                    $estadoTexto = $opcion['estado'] ? 'Activo' : 'Inactivo';
                    
                    echo '<div class="col-md-3 mb-3">';
                    echo '<div class="card h-100">';
                    echo '<div class="card-body">';
                    echo "<h6 class='card-title'>{$opcion['nombre']}</h6>";
                    echo "<p class='card-text small'>{$opcion['descripcion']}</p>";
                    echo "<span class='badge $estadoBadge'>$estadoTexto</span>";
                    echo '</div></div></div>';
                }
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning">No hay opciones disponibles</div>';
            }
            
            echo '</div></div></div></div>';
            
            // 4. Mostrar características por opción
            echo '<div class="row mt-4">';
            echo '<div class="col-12">';
            echo '<div class="card">';
            echo '<div class="card-header bg-warning text-dark"><h5><i class="fas fa-tags"></i> Características por Opción</h5></div>';
            echo '<div class="card-body">';
            
            $stmt = $pdo->query("
                SELECT o.nombre as opcion_nombre, o.idOpcion,
                       COUNT(c.idCaracteristica) as total_caracteristicas,
                       GROUP_CONCAT(c.nombre SEPARATOR ', ') as caracteristicas_nombres
                FROM opcions o 
                LEFT JOIN caracteristicas c ON o.idOpcion = c.idOpcion AND c.estado = 1
                WHERE o.estado = 1
                GROUP BY o.idOpcion, o.nombre 
                ORDER BY o.nombre
            ");
            $resumenCaracteristicas = $stmt->fetchAll();
            
            if (count($resumenCaracteristicas) > 0) {
                echo '<div class="accordion" id="caracteristicasAccordion">';
                
                foreach ($resumenCaracteristicas as $index => $item) {
                    $collapseId = "collapse$index";
                    $caracteristicas = $item['caracteristicas_nombres'] ? explode(', ', $item['caracteristicas_nombres']) : [];
                    
                    echo '<div class="accordion-item">';
                    echo '<h2 class="accordion-header">';
                    echo "<button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#$collapseId'>";
                    echo "<strong>{$item['opcion_nombre']}</strong> <span class='badge bg-primary ms-2'>{$item['total_caracteristicas']} características</span>";
                    echo '</button></h2>';
                    echo "<div id='$collapseId' class='accordion-collapse collapse' data-bs-parent='#caracteristicasAccordion'>";
                    echo '<div class="accordion-body">';
                    
                    if (count($caracteristicas) > 0) {
                        echo '<div class="d-flex flex-wrap gap-2">';
                        foreach ($caracteristicas as $caracteristica) {
                            echo "<span class='badge bg-info'>$caracteristica</span>";
                        }
                        echo '</div>';
                    } else {
                        echo '<p class="text-muted">No hay características para esta opción</p>';
                    }
                    
                    echo '</div></div></div>';
                }
                
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">No hay datos de características disponibles</div>';
            }
            
            echo '</div></div></div></div>';
            
            // 5. Acciones disponibles
            echo '<div class="row mt-4">';
            echo '<div class="col-12">';
            echo '<div class="card">';
            echo '<div class="card-header bg-dark text-white"><h5><i class="fas fa-rocket"></i> Acciones Disponibles</h5></div>';
            echo '<div class="card-body">';
            echo '<div class="row">';
            
            // Botón para ir a configuración
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="d-grid">';
            echo '<a href="/TESIS/VENTAS1/VENTAS/public/configuracion" target="_blank" class="btn btn-primary btn-lg">';
            echo '<i class="fas fa-cogs"></i><br>Ir a Configuración<br><small>Interfaz Laravel</small>';
            echo '</a>';
            echo '</div></div>';
            
            // Botón para ejecutar insert data
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="d-grid">';
            echo '<a href="execute_insert_data.php" target="_blank" class="btn btn-success btn-lg">';
            echo '<i class="fas fa-database"></i><br>Ejecutar Insert Data<br><small>Poblar BD con datos</small>';
            echo '</a>';
            echo '</div></div>';
            
            // Botón para test de formulario
            echo '<div class="col-md-4 mb-3">';
            echo '<div class="d-grid">';
            echo '<a href="test_caracteristica_form.php" target="_blank" class="btn btn-info btn-lg">';
            echo '<i class="fas fa-vial"></i><br>Test Formulario<br><small>Probar creación</small>';
            echo '</a>';
            echo '</div></div>';
            
            echo '</div></div></div></div></div>';
            
            // 6. Estado final y recomendaciones
            echo '<div class="row mt-4">';
            echo '<div class="col-12">';
            echo '<div class="card">';
            echo '<div class="card-header bg-secondary text-white"><h5><i class="fas fa-clipboard-check"></i> Estado Final y Recomendaciones</h5></div>';
            echo '<div class="card-body">';
            
            $todoCompleto = true;
            $recomendaciones = [];
            
            // Verificar si hay opciones
            if ($opcionesActivas == 0) {
                $todoCompleto = false;
                $recomendaciones[] = "⚠️ No hay opciones activas. Ejecuta el script insert_data.sql";
            } else {
                echo "<p><i class='fas fa-check text-success'></i> Opciones configuradas correctamente ($opcionesActivas activas)</p>";
            }
            
            // Verificar si hay características
            if ($caracteristicasActivas == 0) {
                $recomendaciones[] = "⚠️ No hay características activas. Crea algunas desde la interfaz";
            } else {
                echo "<p><i class='fas fa-check text-success'></i> Características configuradas correctamente ($caracteristicasActivas activas)</p>";
            }
            
            // Verificar estructura de tablas
            $tablasRequeridas = ['opcions', 'caracteristicas'];
            foreach ($tablasRequeridas as $tabla) {
                if (!isset($estadoTablas[$tabla])) {
                    $todoCompleto = false;
                    $recomendaciones[] = "❌ Tabla '$tabla' no existe. Ejecuta las migraciones";
                }
            }
            
            if ($todoCompleto && count($recomendaciones) == 0) {
                echo '<div class="alert alert-success">';
                echo '<h5><i class="fas fa-trophy"></i> ¡Sistema Listo!</h5>';
                echo '<p>El sistema de configuración de características está funcionando correctamente.</p>';
                echo '<p><strong>Próximos pasos:</strong></p>';
                echo '<ul>';
                echo '<li>Ir a la <a href="/TESIS/VENTAS1/VENTAS/public/configuracion" target="_blank">interfaz de configuración</a></li>';
                echo '<li>Probar crear nuevas características</li>';
                echo '<li>Verificar que los datos se muestren correctamente</li>';
                echo '</ul>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning">';
                echo '<h5><i class="fas fa-exclamation-triangle"></i> Acciones Pendientes</h5>';
                echo '<ul>';
                foreach ($recomendaciones as $rec) {
                    echo "<li>$rec</li>";
                }
                echo '</ul>';
                echo '</div>';
            }
            
            echo '</div></div></div></div>';
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">';
            echo '<h5><i class="fas fa-exclamation-circle"></i> Error de Conexión</h5>';
            echo '<p><strong>Error:</strong> ' . $e->getMessage() . '</p>';
            echo '<p><strong>Verifica que:</strong></p>';
            echo '<ul>';
            echo '<li>XAMPP esté iniciado</li>';
            echo '<li>MySQL esté corriendo</li>';
            echo '<li>La base de datos "laravel" exista</li>';
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
