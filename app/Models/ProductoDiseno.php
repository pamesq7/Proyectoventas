<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoDiseno extends Model
{
    use HasFactory;

    protected $table = 'producto_diseno';
    protected $primaryKey = 'idProductoDiseno';

    protected $fillable = [

        'idProducto',
        'idDiseno',
        'nombre',
        'precio',
        'estado',
    ];

    public function diseno()
    {
        return $this->belongsTo(Diseno::class, 'idDiseno', 'idDiseno');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }
}
