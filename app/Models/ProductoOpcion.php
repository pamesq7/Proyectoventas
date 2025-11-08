<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoOpcion extends Model
{
    use HasFactory;

    protected $table = 'producto_opcions';
    protected $primaryKey = 'idProductoOpcion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'idProducto',
        'idOpcion',
    ];

    // 🔸 Relación: pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }

    // 🔸 Relación: pertenece a una opción
    public function opciones()
    {
        return $this->belongsToMany(Opcion::class, 'producto_opcions', 'idProducto', 'idOpcion')
            ->withTimestamps();
    }


    // Accessor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
