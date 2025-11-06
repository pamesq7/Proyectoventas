<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'idEmpleado';
    protected $keyType = 'int';
    public $incrementing = false;


    protected $fillable = [
        'idEmpleado',
        'cargo',
        'rol',
        'estado',
    ];


    // Relación: pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class, 'idEmpleado', 'idUser')
            ->withDefault();
    }

    // Relación: puede tener muchas ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación: puede tener muchos diseños
    public function disenos()
    {
        return $this->hasMany(Diseno::class, 'idEmpleado', 'idEmpleado');
    }
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'idEmpleado', 'idEmpleado');
    }

    // Accessor para estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    // Scope para empleados activos
    public function scopeActivos($query)
    {
        return $query->where('estado', true);
    }

    // Scope: buscar por cargo o rol
    public function scopeBuscar($query, $search)
    {
        return $query->where('cargo', 'like', "%{$search}%")
            ->orWhere('rol', 'like', "%{$search}%");
    }
}
