<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoTalla extends Model
{
    use HasFactory;

    protected $table = 'producto_tallas';
    protected $primaryKey = 'idProductoTalla';

    protected $fillable = [
        'precioAdicional',
        'stock',
        'estado',
        'idTalla',
        'idProducto'
    ];

    // 🔸 Relación: pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    // 🔸 Relación: pertenece a una talla
    public function talla()
    {
        return $this->belongsTo(Talla::class, 'idTalla');
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
