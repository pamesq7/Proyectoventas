<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diseno extends Model
{
    use HasFactory;

    protected $table = 'disenos';
    protected $primaryKey = 'idDiseno';
    
    protected $fillable = [
        'archivo',
        'comentario',
        'estado',
        'estadoDiseño',
        'iddetalleVenta',
        'idEmpleado'
    ];

    protected $casts = [
        'estado' => 'integer',
        'estadoDiseño' => 'string'
    ];

    // Relación con empleado (diseñador)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación con detalle de venta (opcional)
    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'iddetalleVenta', 'iddetalleVenta');
    }

    // Scope para diseños activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Scope para diseños en proceso
    public function scopeEnProceso($query)
    {
        return $query->where('estadoDiseño', 'en proceso');
    }

    // Scope para diseños terminados
    public function scopeTerminados($query)
    {
        return $query->where('estadoDiseño', 'terminado');
    }

    // Accessor para URL de archivo
    public function getArchivoUrlAttribute()
    {
        return $this->archivo ? asset('storage/' . $this->archivo) : null;
    }

    // Método para obtener estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
