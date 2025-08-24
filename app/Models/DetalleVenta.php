<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;


    protected $table = 'detalle_ventas';
    protected $primaryKey = 'iddetalleVenta';

    protected $fillable = [
        'cantidad',
        'nombrePersonalizado',
        'numeroPersonalizado',
        'textoAdicional',
        'observacion',
        'precioUnitario',
        'descuento',
        'descripcion',
        'estado',
        'idTalla',
        'idVenta',
        'idEmpleado',
    ];

    // Relación: pertenece a una talla
    public function talla()
    {
        return $this->belongsTo(Talla::class, 'idTalla');
    }

    // Relación: pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idVenta');
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Anulado';
    }
}
