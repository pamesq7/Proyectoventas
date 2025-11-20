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
        'idEmpleado'
    ];

    // Relación: tiene muchos detalles de talla

    // Relación con la tabla intermedia detalle_tallas
    public function detalleTallas()
    {
        return $this->hasMany(DetalleTalla::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    // Relación many-to-many con Talla a través de detalle_tallas
    // En app/Models/DetalleVenta.php

    // Relación many-to-many con Talla a través de detalle_tallas
    public function tallas()
    {
        return $this->belongsToMany(
            Talla::class,
            'detalle_tallas',     // tabla intermedia
            'idDetalleVenta',     // FK en detalle_tallas
            'idTallas',           // Ajustado a idTallas (en plural)
            'idDetalleVenta',     // Clave local en detalle_ventas
            'idTallas'            // Clave local en tallas
        )->withPivot('cantidad');
    }
    // Relación: pertenece a un empleado
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

    // Relación: tiene muchos diseños
    public function diseno()
    {
        return $this->hasOne(Diseno::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Anulado';
    }
}
