@echo off
echo ========================================
echo  MIGRACION SEGURA DE VARIANTES
echo ========================================
echo.

echo [PASO 1] Verificando estado actual...
php artisan migrate:status
echo.

echo [PASO 2] Creando tabla pivot producto_variantes...
php artisan migrate --path=database/migrations/2025_08_03_000001_create_producto_variantes_table.php
if %errorlevel% neq 0 (
    echo ERROR: No se pudo crear la tabla pivot
    pause
    exit /b 1
)
echo ✅ Tabla pivot creada exitosamente
echo.

echo [PASO 3] Migrando datos existentes...
php artisan db:seed --class=MigrateVariantesToPivotSeeder
if %errorlevel% neq 0 (
    echo ERROR: No se pudieron migrar los datos
    pause
    exit /b 1
)
echo ✅ Datos migrados exitosamente
echo.

echo [PASO 4] ATENCION: Se eliminara la columna idProducto de variantes
echo Esta accion es IRREVERSIBLE. Los datos ya fueron migrados a la tabla pivot.
echo.
set /p confirm="¿Continuar? (S/N): "
if /i "%confirm%" neq "S" (
    echo Operacion cancelada por el usuario
    pause
    exit /b 0
)

echo [PASO 4] Eliminando columna idProducto de variantes...
php artisan migrate --path=database/migrations/2025_08_03_000002_remove_idproducto_from_variantes_table.php
if %errorlevel% neq 0 (
    echo ERROR: No se pudo eliminar la columna idProducto
    pause
    exit /b 1
)
echo ✅ Columna eliminada exitosamente
echo.

echo [PASO 5] Verificando resultado final...
php artisan migrate:status
echo.

echo ========================================
echo  MIGRACION COMPLETADA EXITOSAMENTE
echo ========================================
echo ✅ Estructura Many-to-Many implementada
echo ✅ Datos preservados en tabla pivot
echo ✅ Columna idProducto eliminada
echo.
echo Proximos pasos:
echo 1. Probar la funcionalidad en la interfaz
echo 2. Verificar que las relaciones funcionan
echo 3. Actualizar cualquier codigo que use la relacion antigua
echo.
pause
