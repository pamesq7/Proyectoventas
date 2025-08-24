<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    use HasFactory;

    protected $table = 'empleados';
    protected $primaryKey = 'idEmpleado';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'idEmpleado',
        'cargo',
        'rol',
        'estado',
    ];
    
    protected $casts = [
        'estado' => 'boolean',
    ];
    
    protected $appends = [
        'estado_texto',
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
        return $this->hasMany(Venta::class, 'idEmpleado');
    }

    // Relación: puede tener muchos diseños
    public function disenos()
    {
        return $this->hasMany(Diseno::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación: puede aparecer en venta_disenos (como diseñador)
    public function ventaDisenos()
    {
        return $this->hasMany(VentaDiseno::class, 'idDiseñador');
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
