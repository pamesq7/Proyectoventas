<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
    use HasFactory;

    protected $table = 'opcions';
    protected $primaryKey = 'idOpcion';

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
    public function productoOpcion()
    {
        return $this->hasMany(ProductoOpcion::class, 'idOpcion', 'idOpcion');
    }
    // Relación: una opción puede tener muchas características
    // app/Models/Opcion.php
    public function caracteristicas()
    {
        return $this->hasMany(Caracteristica::class, 'idOpcion', 'idOpcion')
            ->where('estado', 1);
    }
    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
