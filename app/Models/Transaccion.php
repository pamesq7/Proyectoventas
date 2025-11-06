<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    use HasFactory;

    protected $table = 'transaccions';
    protected $primaryKey = 'idTransaccion';

    protected $fillable = [
        'tipoTransaccion',
        'monto',
        'metodoPago',
        'observaciones',
        'estado',
        'idVenta',
    ];

    // 🔸 Relación: pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idVenta', 'idVenta');
    }

    // Accessor para estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Confirmada' : 'Anulada';
    }
}
