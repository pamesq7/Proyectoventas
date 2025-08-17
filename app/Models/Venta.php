<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'idVenta';

    protected $fillable = [
        'subtotal',
        'total',
        'fechaEntrega',
        'lugarEntrega',
        'estadoPedido',
        'saldo',
        'estado',
        'idEmpleado',
        'idCliente',
        'idEstablecimiento',
        'idUser',
    ];

    // 🔸 Relación: pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado');
    }

    // 🔸 Relación: pertenece a un cliente natural (opcional)
    public function clienteNatural()
    {
        return $this->belongsTo(ClienteNatural::class, 'idCliente');
    }

    // 🔸 Relación: pertenece a un establecimiento (opcional)
    public function clienteEstablecimiento()
    {
        return $this->belongsTo(ClienteEstablecimiento::class, 'idEstablecimiento');
    }

    // 🔸 Relación: detalles de venta
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'idVenta');
    }

    // 🔸 Relación: transacciones asociadas
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'idVenta');
    }

    // 🔸 Relación: diseños vinculados (muchos a muchos con tabla intermedia)
    public function disenos()
    {
        return $this->belongsToMany(Diseno::class, 'venta_disenos', 'idventa', 'idDiseno');
    }

    // 🔸 Relación: intermedia con datos adicionales
    public function ventaDisenos()
    {
        return $this->hasMany(VentaDiseno::class, 'idventa');
    }

    // Accessor: estado textual
    public function getEstadoTextoAttribute()
    {
        return match ($this->estado) {
            '0' => 'Solicitado',
            '1' => 'Diseño',
            '2' => 'Confección',
            '3' => 'Entregado',
            default => 'Desconocido',
        };
    }
}
