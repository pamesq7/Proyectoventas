<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarianteCaracteristica extends Model
{
    use HasFactory;

    protected $table = 'variante_caracteristicas';
    protected $primaryKey = 'idVarianteCaracteristica';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'precioAdicional',
        'idCaracteristica',
        'idVariante'
    ];

    protected $casts = [
        'estado' => 'integer',
        'precioAdicional' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con variante
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'idVariante', 'idVariante');
    }

    // Relación con característica
    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class, 'idCaracteristica', 'idCaracteristica');
    }

    // Scope para activos
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }

    // Accessor para precio formateado
    public function getPrecioFormateadoAttribute()
    {
        return '$' . number_format($this->precioAdicional, 2);
    }
}

