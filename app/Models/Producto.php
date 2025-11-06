<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'idProducto';

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
    // Relación con variante
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'idVariante', 'idVariante');
    }

    // Muchos a muchos: producto tiene muchas opciones (vía producto_opcions)
    public function productoDiseno()
    {
        return $this->hasMany(ProductoDiseno::class, 'idProducto', 'idProducto');
    }
    
    public function productoOpcion()
    {
        return $this->hasMany(ProductoOpcion::class, 'idProducto', 'idProducto');
    }
    
    public function packProducto()
    {
        return $this->hasMany(PackProducto::class, 'idProducto', 'idProducto');
    }
    
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'idProducto', 'idProducto');
    }

        // Accesor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
