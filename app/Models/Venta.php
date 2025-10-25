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
        'lugarEntrega',
        'estadoPedido',
        'saldo',
        'estado',
        'idEmpleado',
        'idCliente',
        'idEstablecimiento',
    ];

    protected $casts = [
        'fechaEntrega' => 'date',
    ];

    // Relación: pertenece a un empleado
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación: pertenece a un cliente natural (opcional)
    public function clienteNatural()
    {
        return $this->belongsTo(ClienteNatural::class, 'idCliente');
    }

    // Relación: pertenece a un establecimiento (opcional)
    public function clienteEstablecimiento()
    {
        return $this->belongsTo(ClienteEstablecimiento::class, 'idEstablecimiento');
    }

    public function user()
    {
        // Relación no necesaria para Venta, ya que el usuario se obtiene a través de clienteNatural o empleado
        return null;
    }


    // Relación: detalles de venta
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'idVenta');
    }

    // Relación: diseños asociados a través de los detalles de venta
    public function disenos()
    {
        return $this->hasManyThrough(Diseno::class, DetalleVenta::class, 'idVenta', 'iddetalleVenta');
    }

    // Relación: transacciones asociadas
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'idVenta');
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

    // Accessor: estado de pago
    public function getEstadoPagoAttribute()
    {
        if ($this->saldo <= 0) return 'PAGADO';
        if ($this->saldo < $this->total) return 'PARCIAL';
        return 'PENDIENTE';
    }

    // Accessor: monto pagado
    public function getMontoPagadoAttribute()
    {
        return $this->total - $this->saldo;
    }

    // Accessor: porcentaje pagado
    public function getPorcentajePagadoAttribute()
    {
        if ($this->total <= 0) return 0;
        // Calcular usando solo campos existentes: monto_pagado = total - saldo
        $montoPagado = $this->total - $this->saldo;
        return round(($montoPagado / $this->total) * 100, 2);
    }

    // Accessor: nombre completo del cliente
    public function getNombreClienteAttribute()
    {
        // Cliente natural: tomar desde users (name, primerApellido, segundApellido)
        if ($this->clienteNatural && method_exists($this->clienteNatural, 'user') && $this->clienteNatural->user) {
            $u = $this->clienteNatural->user;
            $nombre = trim(($u->name ?? '') . ' ' . ($u->primerApellido ?? '') . ' ' . ($u->segundApellido ?? ''));
            return $nombre !== '' ? $nombre : 'Cliente natural';
        }
        // Establecimiento: razón social o nombreEstablecimiento
        if ($this->clienteEstablecimiento) {
            return $this->clienteEstablecimiento->razonSocial
                ?? $this->clienteEstablecimiento->razonSocial
                ?? 'Establecimiento';
        }
        return 'Cliente no especificado';
    }

    // Accessor: tipo de cliente
    public function getTipoClienteAttribute()
    {
        if ($this->clienteNatural) return 'Natural';
        if ($this->clienteEstablecimiento) return 'Establecimiento';
        return 'No especificado';
    }

    // Accessor: días de atraso (solo para ventas con saldo pendiente)
    public function getDiasAtrasoAttribute()
    {
        if ($this->saldo <= 0) return 0;
        return now()->diffInDays($this->created_at);
    }

    // Scope: solo ventas saldadas
    public function scopeSaldadas($query)
    {
        return $query->where('saldo', '<=', 0);
    }

    // Scope: solo ventas pendientes
    public function scopePendientes($query)
    {
        return $query->where('saldo', '>', 0);
    }

    // Scope: ventas con pagos parciales
    public function scopeParciales($query)
    {
        return $query->where('saldo', '>', 0)->where('saldo', '<', DB::raw('total'));
    }

    // Método: verificar si puede ser anulada
    public function puedeSerAnulada()
    {
        return $this->estado != '3'; // No entregada
    }

    // Método: verificar si puede recibir pagos
    public function puedeRecibirPagos()
    {
        return $this->saldo > 0;
    }

   
}
