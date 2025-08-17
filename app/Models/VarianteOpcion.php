<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VarianteOpcion extends Model
{
    use HasFactory;

    protected $table = 'variante_opcion';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'idOpcion',
        'idCaracteristica'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación: pertenece a una opción
    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'idOpcion', 'idOpcion');
    }

    // Relación: pertenece a una característica
    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class, 'idCaracteristica', 'idCaracteristica');
    }

    // Scope: activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }
}
