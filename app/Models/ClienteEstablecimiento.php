<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteEstablecimiento extends Model
{
    use HasFactory;

    protected $table = 'cliente_establecimientos';
    protected $primaryKey = 'idEstablecimiento';
    protected $keyType = 'int';

    protected $fillable = [
        'nit',
        'razonSocial',
        'tipoEstablecimiento',
        'domicilioFiscal',
        'idRepresentante',
        'estado',
    ];

    // 🔸 Relación: pertenece a un representante (usuario)
    public function user()
    {
        return $this->belongsTo(User::class, 'idRepresentante', 'idUser');
    }

    // 🔸 Relación: puede tener muchas ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class, 'idEstablecimiento', 'idEstablecimiento');
    }

    // Accessor para estado
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }

    // Mutador para asegurar que el NIT siempre se guarde en mayúsculas
    public function setNitAttribute($value)
    {
        $this->attributes['nit'] = strtoupper($value);
    }

    // Scope para clientes activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Verificar si el establecimiento tiene ventas
    public function tieneVentas()
    {
        return $this->ventas()->exists();
    }
    // 👇 Alias para que funcione ->with('representante') y ->clienteEstablecimiento->representante
    public function representante()
    {
        return $this->belongsTo(User::class, 'idRepresentante', 'idUser');
    }
}
