<?php
// Test directo para crear características - simulando Laravel
header('Content-Type: application/json; charset=utf-8');

// Simular datos del formulario como los envía la interfaz
$testData = [
    'idOpcion' => 1, // Cambiar según opciones disponibles
    'nombre' => 'Test Directo ' . date('H:i:s'),
    'descripcion' => 'Característica creada desde test directo'
];

try {
    // Conexión directa
    $pdo = new PDO("mysql:host=localhost;dbname=laravel;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Verificar que la opción existe
    $stmt = $pdo->prepare("SELECT * FROM opcions WHERE idOpcion = ? AND estado = 1");
    $stmt->execute([$testData['idOpcion']]);
    $opcion = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$opcion) {
        throw new Exception("La opción ID {$testData['idOpcion']} no existe o no está activa");
    }
    
    // 2. Validar datos como lo haría Laravel
    if (empty($testData['nombre']) || strlen($testData['nombre']) > 100) {
        throw new Exception("El nombre es requerido y debe tener máximo 100 caracteres");
    }
    
    if (!empty($testData['descripcion']) && strlen($testData['descripcion']) > 255) {
        throw new Exception("La descripción debe tener máximo 255 caracteres");
    }
    
    // 3. Insertar como lo hace el modelo Caracteristica
    $stmt = $pdo->prepare("
        INSERT INTO caracteristicas (nombre, descripcion, estado, idOpcion, created_at, updated_at) 
        VALUES (?, ?, ?, ?, NOW(), NOW())
    ");
    
    $resultado = $stmt->execute([
        $testData['nombre'],
        $testData['descripcion'],
        1, // estado siempre activo
        $testData['idOpcion']
    ]);
    
    if ($resultado) {
        $nuevoId = $pdo->lastInsertId();
        
        // Verificar que se guardó correctamente
        $stmt = $pdo->prepare("
            SELECT c.*, o.nombre as opcion_nombre 
            FROM caracteristicas c 
            LEFT JOIN opcions o ON c.idOpcion = o.idOpcion 
            WHERE c.idCaracteristica = ?
        ");
        $stmt->execute([$nuevoId]);
        $caracteristicaCreada = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'message' => 'Característica creada correctamente',
            'data' => [
                'id' => $nuevoId,
                'caracteristica' => $caracteristicaCreada,
                'datos_enviados' => $testData
            ]
        ], JSON_PRETTY_PRINT);
        
    } else {
        throw new Exception("Error al ejecutar la inserción en base de datos");
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'datos_enviados' => $testData,
        'debug_info' => [
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ], JSON_PRETTY_PRINT);
}
?>
