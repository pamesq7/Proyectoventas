<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    use HasFactory;


    protected $table = 'detalle_ventas';
    protected $primaryKey = 'idDetalleVenta';

    protected $fillable = [
        'cantidad',
        'precioUnitario',
        'descuento',
        'descripcion',
        'tipo_pack',
        'subtotal',
        'estado',
        'idVenta',
        'idProducto',
        'idPack',
        'idEmpleado'
    ];

    // Relación: pertenece a una talla
    public function detalleTalla()
    {
        return $this->hasMany(DetalleTalla::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class, 'idPack', 'idPack');
    }
    
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación: pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idVenta', 'idVenta');
    }
    // Relación: pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
    }
    public function diseno()
    {
        return $this->hasMany(Diseno::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Anulado';
    }
}
