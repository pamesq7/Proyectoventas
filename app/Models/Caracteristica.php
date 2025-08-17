<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    use HasFactory;

    protected $table = 'caracteristicas';
    protected $primaryKey = 'idCaracteristica';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'idOpcion'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'idCaracteristica';
    }

    // Relación: cada característica pertenece a una opción
    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'idOpcion', 'idOpcion');
    }

    // Relación: una característica puede tener muchas variantes características
    public function variantesCaracteristicas()
    {
        return $this->hasMany(VarianteCaracteristica::class, 'idCaracteristica', 'idCaracteristica');
    }

    // Relación: muchos productos a través de la tabla pivot producto_caracteristicas
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_caracteristicas', 'idCaracteristica', 'idProducto')
            ->withPivot('nombre', 'descripcion', 'estado', 'precioAdicional')
            ->withTimestamps();
    }

    // Relación: registros directos de la tabla pivot
    public function productoCaracteristicas()
    {
        return $this->hasMany(ProductoCaracteristica::class, 'idCaracteristica', 'idCaracteristica');
    }

    // Scope: características activas
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    // Método helper: contar productos asociados
    public function getProductosCountAttribute()
    {
        return $this->productos()->count();
    }
}
