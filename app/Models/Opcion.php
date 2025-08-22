<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    use HasFactory;

    protected $table = 'opcions';
    protected $primaryKey = 'idOpcion';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'idOpcion';
    }

    /**
     * Scope para obtener solo opciones activas
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }

    // Relación: una opción puede tener muchas características
    public function caracteristicas()
    {
        return $this->hasMany(Caracteristica::class, 'idOpcion', 'idOpcion');
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
