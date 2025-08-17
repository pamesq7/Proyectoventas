@echo off
echo ========================================
echo  MIGRACION CORRECTA: VARIANTE → PRODUCTOS
echo  (Una variante pertenece a muchos productos)
echo ========================================
echo.

echo [PASO 1] Verificando estado actual...
php artisan migrate:status
echo.

echo [PASO 2] Agregando columna idVariante a productos...
php artisan migrate --path=database/migrations/2025_08_03_000003_add_idvariante_to_productos_table.php
if %errorlevel% neq 0 (
    echo ERROR: No se pudo agregar la columna idVariante a productos
    pause
    exit /b 1
)
echo ✅ Columna idVariante agregada a productos
echo.

echo [PASO 3] Migrando relaciones existentes...
echo Moviendo datos de variantes.idProducto → productos.idVariante
php artisan db:seed --class=MigrateToCorrectRelationSeeder
if %errorlevel% neq 0 (
    echo ERROR: No se pudieron migrar las relaciones
    pause
    exit /b 1
)
echo ✅ Relaciones migradas exitosamente
echo.

echo [PASO 4] ATENCION: Se eliminara la columna idProducto de variantes
echo Esta accion es IRREVERSIBLE. Los datos ya fueron migrados a productos.idVariante
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
echo ✅ Relación One-to-Many implementada
echo ✅ Una variante → muchos productos
echo ✅ Datos preservados
echo.
echo ESTRUCTURA FINAL:
echo   variantes (1) ←→ (N) productos
echo   - productos.idVariante → variantes.idVariante
echo.
echo Proximos pasos:
echo 1. Probar la funcionalidad en la interfaz
echo 2. Verificar que las relaciones funcionan
echo 3. Actualizar formularios para asignar variantes
echo.
pause
