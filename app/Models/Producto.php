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
        'idVariante',
        'idPackProducto',
        'tipoProducto'    // 👈 
    ];

    protected $casts = [
        'precioVenta' => 'decimal:2',
        'precioProduccion' => 'decimal:2',
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    // En tu modelo Producto.php
    public function opciones()
    {
        return $this->belongsToMany(Opcion::class, 'producto_opcions', 'idProducto', 'idOpcion')
            ->withPivot(['idProductoOpcion', 'estado'])
            ->withTimestamps();
    }
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

    // app/Models/Producto.php
    public function productoOpcion()
    {
        return $this->hasMany(ProductoOpcion::class, 'idProducto', 'idProducto')
            ->where('estado', 1)
            ->with('opcion.caracteristicas');
    }

    // Relación con el pack al que pertenece este producto (si es parte de un pack)
    public function pack()
    {
        return $this->belongsTo(Pack::class, 'idPackProducto', 'idPackProducto');
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


    public function esPack(): bool
    {
        return $this->tipoProducto === 'pack';
    }

    public function esProducto(): bool
    {
        return $this->tipoProducto === 'producto';
    }

    // scope para filtrar solo packs
    public function scopePacks($query)
    {
        return $query->where('tipoProducto', 'pack');
    }

    // scope para filtrar solo productos simples
    public function scopeSimples($query)
    {
        return $query->where('tipoProducto', 'producto');
    }

}
