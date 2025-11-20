<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';
    protected $primaryKey = 'idVenta';

    protected $fillable = [
        'subtotal',
        'total',
        'fechaEntrega',
        'estadoPedido',
        'saldo',
        'estado',
        'idEmpleado',
        'idCliente',
        'idEstablecimiento',
        'idDireccion', // <- agregar
    ];



    // Relación: pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación: pertenece a un cliente natural (opcional)
    public function clienteNatural()
    {
        return $this->belongsTo(ClienteNatural::class, 'idCliente', 'idCliente');
    }

    // Relación: pertenece a un establecimiento (opcional)
    public function clienteEstablecimiento()
    {
        return $this->belongsTo(ClienteEstablecimiento::class, 'idEstablecimiento', 'idEstablecimiento');
    }


    // Relación: detalles de venta
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'idVenta', 'idVenta');
    }

    // Relación: transacciones asociadas
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'idVenta', 'idVenta');
    }
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'idDireccion', 'idDireccion');
    }
    // En app/Models/Venta.php

    // Agrega esta relación al final de la clase Venta
    public function disenos()
    {
        return $this->hasManyThrough(
            Diseno::class,        // Modelo destino
            DetalleVenta::class,  // Modelo intermedio
            'idVenta',           // Clave foránea en la tabla detalle_ventas
            'idDetalleVenta',    // Clave foránea en la tabla disenos
            'idVenta',           // Clave local en ventas
            'idDetalleVenta'     // Clave local en detalle_ventas
        )->with('empleado.user'); // Cargar la relación con empleado y usuario
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

    // Método: verificar si puede recibir pagos
    public function puedeRecibirPagos()
    {
        return $this->saldo > 0;
    }
}
