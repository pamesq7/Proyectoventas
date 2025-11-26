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

    /* ==============================
       CATEGORÍA
    ===============================*/
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'idCategoria', 'idCategoria');
    }

    /* ==============================
       VARIANTE
    ===============================*/
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'idVariante', 'idVariante');
    }

    /* ==============================
       PACK → ESTE PRODUCTO PERTENECE A UN PACK
       productos.idPackProducto -> pack.idPackProducto
    ===============================*/
    public function pack()
    {
        return $this->belongsTo(Pack::class, 'idPackProducto', 'idPackProducto');
    }


    public function packProductos()
    {
        return $this->belongsToMany(
            Producto::class,
            'pack_productos',
            'idPackProducto',
            'idProducto'
        );
    }

    /* ==============================
       PACK → ESTE PRODUCTO ES UN PACK Y TIENE PRODUCTOS HIJOS
       relación real: tabla pack (idPackProducto, idProducto)
    ===============================*/
    public function componentes()
    {
        return $this->belongsToMany(
            Producto::class,
            'pack',
            'idPackProducto',
            'idProducto'
        );
    }

    /* ==============================
       OPCIONES DEL PRODUCTO (opción + características)
    ===============================*/
    public function productoOpcion()
    {
        return $this->hasMany(ProductoOpcion::class, 'idProducto', 'idProducto')
            ->where('estado', 1)
            ->with('opcion.caracteristicas');
    }

    /* ==============================
       DISEÑOS
    ===============================*/
    public function disenos()
    {
        return $this->belongsToMany(Diseno::class, 'producto_diseno', 'idProducto', 'idDiseno');
    }

    // Relación con tabla pivote producto_diseno
    public function productoDiseno()
    {
        return $this->hasMany(ProductoDiseno::class, 'idProducto', 'idProducto');
    }






    /* ==============================
       DETALLES DE VENTA
    ===============================*/
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'idProducto', 'idProducto');
    }

    /* ==============================
       HELPERS
    ===============================*/

    public function esPack(): bool
    {
        return $this->tipoProducto === 'pack';
    }

    public function esProducto(): bool
    {
        return $this->tipoProducto === 'producto';
    }

    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    /* ==============================
       SCOPES
    ===============================*/
    public function scopePacks($q)
    {
        return $q->where('tipoProducto', 'pack');
    }

    public function scopeSimples($q)
    {
        return $q->where('tipoProducto', 'producto');
    }
}
