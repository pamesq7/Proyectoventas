# 🔄 MIGRACIÓN SEGURA DE VARIANTES: ONE-TO-MANY → MANY-TO-MANY

## 📋 RESUMEN

Esta migración transforma la relación entre productos y variantes de **One-to-Many** a **Many-to-Many**, permitiendo que una variante pueda pertenecer a múltiples productos.

### ANTES:
```
productos (1) ←→ (N) variantes
```

### DESPUÉS:
```
productos (1) ←→ (N) producto_variantes (N) ←→ (1) variantes
```

## 🚀 MÉTODOS DE MIGRACIÓN

### OPCIÓN 1: Script Automático (Windows)
```bash
# Ejecutar desde el directorio del proyecto
migrate_variantes.bat
```

### OPCIÓN 2: Script Automático (Linux/Mac)
```bash
# Dar permisos de ejecución
chmod +x migrate_variantes.sh
# Ejecutar
./migrate_variantes.sh
```

### OPCIÓN 3: Comandos Manuales Paso a Paso

#### PASO 1: Crear tabla pivot
```bash
php artisan migrate --path=database/migrations/2025_08_03_000001_create_producto_variantes_table.php
```

#### PASO 2: Migrar datos existentes
```bash
php artisan db:seed --class=MigrateVariantesToPivotSeeder
```

#### PASO 3: Eliminar columna idProducto (IRREVERSIBLE)
```bash
php artisan migrate --path=database/migrations/2025_08_03_000002_remove_idproducto_from_variantes_table.php
```

## 📊 ESTRUCTURA DE LA NUEVA TABLA PIVOT

### `producto_variantes`
| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id` | BIGINT | Clave primaria |
| `idProducto` | INT | FK a productos |
| `idVariante` | TINYINT | FK a variantes |
| `precioAdicional` | DECIMAL(8,2) | Precio adicional por variante |
| `stockVariante` | INT | Stock específico de esta combinación |
| `estado` | TINYINT | Estado activo/inactivo |
| `created_at` | TIMESTAMP | Fecha de creación |
| `updated_at` | TIMESTAMP | Fecha de actualización |

### Índices y Restricciones
- **Clave única**: `(idProducto, idVariante)` - Evita duplicados
- **Índices**: En `idProducto` e `idVariante` para optimizar consultas
- **Claves foráneas**: Con eliminación en cascada

## 🔧 MODELOS ELOQUENT ACTUALIZADOS

### Modelo `Producto`
```php
public function variantes()
{
    return $this->belongsToMany(Variante::class, 'producto_variantes', 'idProducto', 'idVariante')
           ->withPivot('precioAdicional', 'stockVariante', 'estado')
           ->withTimestamps();
}
```

### Modelo `Variante`
```php
public function productos()
{
    return $this->belongsToMany(Producto::class, 'producto_variantes', 'idVariante', 'idProducto')
           ->withPivot('precioAdicional', 'stockVariante', 'estado')
           ->withTimestamps();
}
```

### Modelo `ProductoVariante` (Nuevo)
Modelo dedicado para la tabla pivot con métodos helper.

## 🛠️ NUEVAS RUTAS API

```php
POST   /productos/{producto}/variantes/attach           # Asociar variante
DELETE /productos/{producto}/variantes/{id}/detach     # Desasociar variante
PUT    /productos/{producto}/variantes/{id}/update-relation # Actualizar relación
GET    /productos/{producto}/variantes                 # Obtener variantes del producto
```

## 💡 CASOS DE USO

### Antes (Limitado)
- Variante "Talla M" solo podía pertenecer a UN producto
- Duplicación de variantes similares
- Gestión compleja de inventario

### Después (Flexible)
- Variante "Talla M" puede aplicar a múltiples productos
- Precios diferenciados: "Talla XL" +$5 en poleras, +$10 en chaquetas
- Stock independiente por combinación
- Reutilización de variantes

## ⚠️ CONSIDERACIONES IMPORTANTES

### ANTES DE MIGRAR
1. **Backup de la base de datos** - OBLIGATORIO
2. Verificar que no hay procesos usando las tablas
3. Informar a otros desarrolladores del cambio

### DURANTE LA MIGRACIÓN
1. La migración preserva TODOS los datos existentes
2. La eliminación de `idProducto` es IRREVERSIBLE
3. El proceso puede tomar tiempo con muchos registros

### DESPUÉS DE MIGRAR
1. Probar todas las funcionalidades relacionadas con variantes
2. Actualizar cualquier código que use la relación antigua
3. Verificar que las consultas funcionan correctamente

## 🧪 PRUEBAS RECOMENDADAS

### 1. Verificar Migración de Datos
```php
// Verificar que los datos se migraron correctamente
$producto = Producto::find(1);
$variantes = $producto->variantes;
echo "Producto tiene " . $variantes->count() . " variantes";
```

### 2. Probar Nueva Funcionalidad
```php
// Asociar variante existente a producto
$producto = Producto::find(1);
$producto->variantes()->attach(2, [
    'precioAdicional' => 5.00,
    'stockVariante' => 10,
    'estado' => 1
]);
```

### 3. Verificar Datos del Pivot
```php
$variante = $producto->variantes()->first();
echo "Precio adicional: " . $variante->pivot->precioAdicional;
echo "Stock: " . $variante->pivot->stockVariante;
```

## 🔄 ROLLBACK (Si es necesario)

Si necesitas revertir los cambios ANTES de eliminar la columna `idProducto`:

```bash
# Revertir solo la tabla pivot
php artisan migrate:rollback --path=database/migrations/2025_08_03_000001_create_producto_variantes_table.php
```

**NOTA**: Una vez eliminada la columna `idProducto`, el rollback completo requiere restaurar desde backup.

## 📞 SOPORTE

Si encuentras problemas durante la migración:

1. Verifica los logs de Laravel: `storage/logs/laravel.log`
2. Revisa el estado de las migraciones: `php artisan migrate:status`
3. Verifica la integridad de los datos en la base de datos
4. Consulta la documentación de Laravel sobre relaciones Many-to-Many

## ✅ CHECKLIST POST-MIGRACIÓN

- [ ] Tabla `producto_variantes` creada correctamente
- [ ] Datos migrados sin pérdida
- [ ] Columna `idProducto` eliminada de `variantes`
- [ ] Modelos Eloquent funcionando
- [ ] Rutas API respondiendo
- [ ] Interfaz de usuario actualizada
- [ ] Pruebas de funcionalidad completadas
- [ ] Documentación actualizada

---

**¡Migración completada exitosamente! 🎉**
