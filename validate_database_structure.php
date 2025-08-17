<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Configurar conexión a la base de datos
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'laravel',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$db = $capsule->getConnection();
$schema = $capsule->schema();

echo "🔍 VALIDANDO ESTRUCTURA DE BASE DE DATOS\n";
echo "========================================\n\n";

$errors = [];
$warnings = [];
$success = [];

// Función para validar tabla
function validateTable($tableName, $expectedColumns, $schema, &$errors, &$success) {
    if (!$schema->hasTable($tableName)) {
        $errors[] = "❌ Tabla '$tableName' no existe";
        return false;
    }
    
    $success[] = "✅ Tabla '$tableName' existe";
    
    foreach ($expectedColumns as $column => $type) {
        if (!$schema->hasColumn($tableName, $column)) {
            $errors[] = "❌ Columna '$column' no existe en tabla '$tableName'";
        } else {
            $success[] = "✅ Columna '$tableName.$column' existe";
        }
    }
    
    return true;
}

// Función para validar clave foránea
function validateForeignKey($tableName, $column, $referencedTable, $referencedColumn, $db, &$errors, &$success) {
    try {
        $foreignKeys = $db->select("
            SELECT 
                COLUMN_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE 
                TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = '$tableName' 
                AND COLUMN_NAME = '$column'
                AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        if (empty($foreignKeys)) {
            $errors[] = "❌ Clave foránea '$tableName.$column' -> '$referencedTable.$referencedColumn' no existe";
        } else {
            $fk = $foreignKeys[0];
            if ($fk->REFERENCED_TABLE_NAME === $referencedTable && $fk->REFERENCED_COLUMN_NAME === $referencedColumn) {
                $success[] = "✅ Clave foránea '$tableName.$column' -> '$referencedTable.$referencedColumn' correcta";
            } else {
                $errors[] = "❌ Clave foránea '$tableName.$column' apunta a tabla/columna incorrecta";
            }
        }
    } catch (Exception $e) {
        $errors[] = "❌ Error validando FK '$tableName.$column': " . $e->getMessage();
    }
}

echo "1. VALIDANDO TABLAS PRINCIPALES\n";
echo "-------------------------------\n";

// Validar tabla users
validateTable('users', [
    'idUser' => 'int',
    'name' => 'varchar',
    'primerApellido' => 'varchar',
    'segundApellido' => 'varchar',
    'usuario' => 'varchar',
    'email' => 'varchar',
    'password' => 'varchar',
    'tipo_usuario' => 'enum',
    'ci' => 'varchar',
    'telefono' => 'varchar',
    'fechaNacimiento' => 'date',
    'genero' => 'varchar',
    'direccion' => 'text',
    'estado' => 'tinyint',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

// Validar tabla categorias
validateTable('categorias', [
    'idCategoria' => 'int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'estado' => 'tinyint',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

// Validar tabla productos
validateTable('productos', [
    'idProducto' => 'int',
    'SKU' => 'varchar',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'foto' => 'varchar',
    'cantidad' => 'int',
    'precioVenta' => 'int',
    'precioProduccion' => 'int',
    'pedidoMinimo' => 'int',
    'estado' => 'tinyint',
    'idCategoria' => 'int',
    'idDiseno' => 'int',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

// Validar tabla disenos
validateTable('disenos', [
    'idDiseno' => 'int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'archivo' => 'varchar',
    'estado' => 'tinyint',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

echo "\n2. VALIDANDO TABLAS DE RELACIONES\n";
echo "---------------------------------\n";

// Validar tabla opcions
validateTable('opcions', [
    'idOpcion' => 'int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'estado' => 'tinyint',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

// Validar tabla caracteristicas
validateTable('caracteristicas', [
    'idCaracteristica' => 'int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'estado' => 'tinyint',
    'idOpcion' => 'int',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

// Validar tabla producto_caracteristicas
validateTable('producto_caracteristicas', [
    'id' => 'int',
    'nombre' => 'varchar',
    'descripcion' => 'text',
    'estado' => 'tinyint',
    'precioAdicional' => 'decimal',
    'idProducto' => 'int',
    'idCaracteristica' => 'int',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

// Validar tabla variante_opcion
validateTable('variante_opcion', [
    'id' => 'int',
    'idVariante' => 'int',
    'idOpcion' => 'int',
    'valor' => 'varchar',
    'precioAdicional' => 'decimal',
    'estado' => 'tinyint',
    'created_at' => 'timestamp',
    'updated_at' => 'timestamp'
], $schema, $errors, $success);

echo "\n3. VALIDANDO CLAVES FORÁNEAS\n";
echo "----------------------------\n";

// Validar claves foráneas principales
validateForeignKey('productos', 'idCategoria', 'categorias', 'idCategoria', $db, $errors, $success);
validateForeignKey('caracteristicas', 'idOpcion', 'opcions', 'idOpcion', $db, $errors, $success);
validateForeignKey('producto_caracteristicas', 'idProducto', 'productos', 'idProducto', $db, $errors, $success);
validateForeignKey('producto_caracteristicas', 'idCaracteristica', 'caracteristicas', 'idCaracteristica', $db, $errors, $success);

echo "\n4. VALIDANDO TIPOS DE DATOS\n";
echo "---------------------------\n";

// Verificar tipos de datos críticos
try {
    $productosColumns = $db->select("
        SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'productos'
        AND COLUMN_NAME IN ('idProducto', 'idCategoria', 'idDiseno')
    ");
    
    foreach ($productosColumns as $col) {
        if ($col->COLUMN_NAME === 'idProducto' && $col->DATA_TYPE === 'int') {
            $success[] = "✅ productos.idProducto es tipo INT";
        } elseif ($col->COLUMN_NAME === 'idCategoria' && $col->DATA_TYPE === 'int') {
            $success[] = "✅ productos.idCategoria es tipo INT";
        } elseif ($col->COLUMN_NAME === 'idDiseno' && $col->DATA_TYPE === 'int' && $col->IS_NULLABLE === 'YES') {
            $success[] = "✅ productos.idDiseno es tipo INT y NULLABLE";
        }
    }
} catch (Exception $e) {
    $errors[] = "❌ Error validando tipos de datos: " . $e->getMessage();
}

echo "\n5. RESUMEN DE VALIDACIÓN\n";
echo "========================\n";

echo "✅ ÉXITOS (" . count($success) . "):\n";
foreach ($success as $msg) {
    echo "   $msg\n";
}

if (!empty($warnings)) {
    echo "\n⚠️  ADVERTENCIAS (" . count($warnings) . "):\n";
    foreach ($warnings as $msg) {
        echo "   $msg\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ERRORES (" . count($errors) . "):\n";
    foreach ($errors as $msg) {
        echo "   $msg\n";
    }
    echo "\n🚨 ESTADO: FALLÓ LA VALIDACIÓN\n";
    exit(1);
} else {
    echo "\n🎉 ESTADO: VALIDACIÓN EXITOSA\n";
    echo "La estructura de la base de datos está correcta.\n";
}

echo "\n📊 ESTADÍSTICAS:\n";
echo "- Éxitos: " . count($success) . "\n";
echo "- Advertencias: " . count($warnings) . "\n";
echo "- Errores: " . count($errors) . "\n";
echo "\nValidación completada.\n";
