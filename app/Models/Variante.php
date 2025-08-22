<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;
    protected $table = 'variantes';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // 🔸 Relación: una variante tiene muchos productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'idVariante');
    }

    // 🔸 Relación: una variante puede tener muchas características
    public function varianteCaracteristicas()
    {
        return $this->hasMany(VarianteCaracteristica::class, 'idVariante');
    }

    // Relación con características a través de variante_caracteristicas
    public function caracteristicas()
    {
        return $this->hasManyThrough(
            Caracteristica::class,
            VarianteCaracteristica::class,
            'idVariante', // FK en variante_caracteristicas que referencia variantes.id
            'idCaracteristica', // FK en caracteristicas (usamos PK para enlazar)
            'id', // clave local en variantes
            'idCaracteristica' // clave local en pivote hacia caracteristicas
        );
    }

    // Scope para variantes activas
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }

    // Accesor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }

    // Método para obtener el precio total de la variante
    public function getPrecioTotalAttribute()
    {
        $precioBase = $this->productos->sum('precioVenta') ?? 0;
        $precioAdicional = $this->varianteCaracteristicas->sum('precioAdicional');
        return $precioBase + $precioAdicional;
    }
}
