<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $table = 'pack';
    protected $primaryKey = 'idPackProducto';

    protected $fillable = [
        'idProducto',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // El producto principal del pack
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }

    // Los productos que pertenecen a este pack

    public function productos()
{
    return $this->belongsToMany(Producto::class, 'pack_productos', 'idPackProducto', 'idProducto')
                ->withPivot('cantidad');
}

}