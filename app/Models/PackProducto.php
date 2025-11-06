<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackProducto extends Model
{
    use HasFactory;

    protected $table = 'pack_productos';
    protected $primaryKey = 'idPackProducto';

    protected $fillable = [
        'idPack',
        'idProducto',
        'nombre',
        'cantidad',
        'precio',
        'estado',
    ];

    // Relación con productos
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'idPack', 'idPack');
    }
}
