<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'idProducto';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    
    protected $fillable = [
        'SKU',
        'nombre',
        'descripcion',
        'foto',
        'cantidad',
        'precioVenta',
        'precioProduccion',
        'pedidoMinimo',
        'estado',
        'idCategoria',
        'idDiseno',
        'idVariante'
    ];

    protected $casts = [
        'precioVenta' => 'decimal:2',
        'precioProduccion' => 'decimal:2',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // Relación inversa: un producto pertenece a una categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }

    // Relación: un producto puede tener un diseño
    public function diseno()
    {
        return $this->belongsTo(Diseno::class, 'idDiseno', 'idDiseno');
    }

    // Relación: un producto pertenece a una variante
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'idVariante', 'idVariante');
    }

    // Relación: un producto puede tener muchas variantes (para casos especiales)
    public function variantes()
    {
        return $this->hasMany(Variante::class, 'idProducto', 'idProducto');
    }

    // Relación de características eliminada (tabla producto_caracteristicas eliminada)

    // Un producto puede tener muchas tallas
    public function productoTallas()
    {
        return $this->hasMany(ProductoTalla::class, 'idProducto');
    }

    // Muchos a muchos: producto tiene muchas opciones (vía producto_opcions)
    public function opciones()
    {
        return $this->belongsToMany(Opcion::class, 'producto_opcions', 'idProducto', 'idOpcion');
    }

    // Relación: muchos a muchos con diseños
    public function disenos()
    {
        return $this->belongsToMany(
            Diseno::class,
            'producto_disenos',
            'idProducto',
            'idDiseno'
        )->withPivot([
            'es_principal',
            'precio_personalizado',
            'personalizaciones',
            'estado'
        ])->withTimestamps();
    }

    // Método para obtener el diseño principal
    public function diseñoPrincipal()
    {
        return $this->disenos()->wherePivot('es_principal', true)->first();
    }

    // Un producto puede aparecer en muchos detalles de venta
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'idProducto');
    }

    // Accesor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
