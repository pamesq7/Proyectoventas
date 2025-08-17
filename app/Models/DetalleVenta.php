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
        'corte',
        'nombrePersonalizado',
        'numeroPersonalizado',
        'textoAdicional',
        'observacion',
        'precioUnitario',
        'subtotal',
        'descuento',
        'descripcion',
        'estado',
        'idTalla',
        'idVenta',
        'idProducto',
        'idGrupoDetalle',
        'idUser',
        'idDiseñador',
    ];

    // 🔸 Relación: pertenece a una talla
    public function talla()
    {
        return $this->belongsTo(Talla::class, 'idTalla');
    }

    // 🔸 Relación: pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idVenta');
    }

    // 🔸 Relación: pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    // 🔸 Relación: pertenece a un grupo de detalle
    public function grupoDetalle()
    {
        return $this->belongsTo(GrupoDetalle::class, 'idGrupoDetalle');
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Anulado';
    }
}
