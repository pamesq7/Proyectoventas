<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    use HasFactory;

    protected $table = 'tallas';
    protected $primaryKey = 'idTalla';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    // 🔸 Relación: una talla puede pertenecer a muchos productos (producto_tallas)
    public function productoTallas()
    {
        return $this->hasMany(ProductoTalla::class, 'idTalla');
    }

    // Accesor opcional para mostrar el estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Disponible' : 'No disponible';
    }
}
