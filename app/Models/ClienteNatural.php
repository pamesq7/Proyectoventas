<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Venta;

class ClienteNatural extends Model
{
    use HasFactory;

    protected $table = 'cliente_naturals';
    protected $primaryKey = 'idCliente';
    protected $keyType = 'int';
    public $incrementing = false;


    protected $fillable = [
        'idCliente',
        'nit',
        'estado',
    ];

    // 🔸 Relación: pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'idCliente', 'idUser');
    }

    // 🔸 Relación: tiene muchas ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'idCliente', 'idCliente');
    }

    // Accessor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
