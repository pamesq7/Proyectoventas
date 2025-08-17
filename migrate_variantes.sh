#!/bin/bash

echo "========================================"
echo "  MIGRACIÓN SEGURA DE VARIANTES"
echo "========================================"
echo

echo "[PASO 1] Verificando estado actual..."
php artisan migrate:status
echo

echo "[PASO 2] Creando tabla pivot producto_variantes..."
php artisan migrate --path=database/migrations/2025_08_03_000001_create_producto_variantes_table.php
if [ $? -ne 0 ]; then
    echo "❌ ERROR: No se pudo crear la tabla pivot"
    exit 1
fi
echo "✅ Tabla pivot creada exitosamente"
echo

echo "[PASO 3] Migrando datos existentes..."
php artisan db:seed --class=MigrateVariantesToPivotSeeder
if [ $? -ne 0 ]; then
    echo "❌ ERROR: No se pudieron migrar los datos"
    exit 1
fi
echo "✅ Datos migrados exitosamente"
echo

echo "[PASO 4] ⚠️ ATENCIÓN: Se eliminará la columna idProducto de variantes"
echo "Esta acción es IRREVERSIBLE. Los datos ya fueron migrados a la tabla pivot."
echo
read -p "¿Continuar? (S/N): " confirm
if [[ $confirm != [Ss] ]]; then
    echo "Operación cancelada por el usuario"
    exit 0
fi

echo "[PASO 4] Eliminando columna idProducto de variantes..."
php artisan migrate --path=database/migrations/2025_08_03_000002_remove_idproducto_from_variantes_table.php
if [ $? -ne 0 ]; then
    echo "❌ ERROR: No se pudo eliminar la columna idProducto"
    exit 1
fi
echo "✅ Columna eliminada exitosamente"
echo

echo "[PASO 5] Verificando resultado final..."
php artisan migrate:status
echo

echo "========================================"
echo "  MIGRACIÓN COMPLETADA EXITOSAMENTE"
echo "========================================"
echo "✅ Estructura Many-to-Many implementada"
echo "✅ Datos preservados en tabla pivot"
echo "✅ Columna idProducto eliminada"
echo
echo "Próximos pasos:"
echo "1. Probar la funcionalidad en la interfaz"
echo "2. Verificar que las relaciones funcionan"
echo "3. Actualizar cualquier código que use la relación antigua"
echo
