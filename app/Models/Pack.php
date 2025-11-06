<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;

    protected $table = 'pack';
    protected $primaryKey = 'idPack';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // Relación con productos
    public function detalleVenta()
    {
        return $this->hasMany(DetalleVenta::class, 'idPack', 'idPack');
    }

    public function packProducto()
    {
        return $this->hasMany(PackProducto::class, 'idPack', 'idPack');
    }

}
