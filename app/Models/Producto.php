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
        'stock',
        'estado',
        'idCategoria',
        'idVariante',
        'tipoProducto',
        'idPackProducto'
    ];

    protected $casts = [
        'precioVenta' => 'decimal:2',
        'precioProduccion' => 'decimal:2',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación con categoría
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }
    // En app/Models/Producto.php

// En app/Models/Producto.php
public function disenos()
{
    return $this->belongsToMany(Diseno::class, 'producto_diseno', 'idProducto', 'idDiseno');
}
  // Muchos a muchos: producto tiene muchas opciones (vía producto_opcions)
    public function productoDiseno()
    {
        return $this->hasMany(ProductoDiseno::class, 'idProducto', 'idProducto');
    }

    // Relación con variante
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'idVariante', 'idVariante');
    }

    // Si el producto es parte de un pack
    public function packPertenece()
    {
        return $this->belongsTo(Pack::class, 'idPackProducto', 'idPackProducto');
    }

    // Si el producto ES un pack (tiene productos asociados)
    public function packContenido()
    {
        return $this->hasOne(Pack::class, 'idProducto', 'idProducto');
    }

    // Método para verificar si es un pack
    public function esPack()
    {
        return $this->tipoProducto === 'pack' || $this->packContenido !== null;
    }

    // Método para obtener los productos de un pack
    public function productosDelPack()
    {
        if ($this->esPack()) {
            return $this->hasMany(Producto::class, 'idPackProducto', 'idPackProducto');
        }
        return null;
    }

    // Relación con detalles de venta
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'idProducto', 'idProducto');
    }
}