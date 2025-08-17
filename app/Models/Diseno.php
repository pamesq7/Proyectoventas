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
        'nombre',
        'descripcion',
        'imagen_preview',
        'archivo_diseno',
        'categoria_diseno',
        'precio_adicional',
        'especificaciones',
        'colores_disponibles',
        'tags',
        'es_personalizable',
        'estado'
    ];

    protected $casts = [
        'colores_disponibles' => 'array',
        'tags' => 'array',
        'es_personalizable' => 'boolean',
        'estado' => 'boolean',
        'precio_adicional' => 'decimal:2'
    ];

    // Relación: muchos a muchos con productos
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'producto_disenos',
            'idDiseno',
            'idProducto'
        )->withPivot([
            'es_principal',
            'precio_personalizado',
            'personalizaciones',
            'estado'
        ])->withTimestamps();
    }

    // Scope para diseños activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Scope para diseños personalizables
    public function scopePersonalizables($query)
    {
        return $query->where('es_personalizable', 1);
    }

    // Scope por categoría
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria_diseno', $categoria);
    }

    // Accessor para URL de imagen preview
    public function getImagenPreviewUrlAttribute()
    {
        return $this->imagen_preview ? asset('storage/' . $this->imagen_preview) : null;
    }

    // Accessor para URL de archivo de diseño
    public function getArchivoDisenoUrlAttribute()
    {
        return $this->archivo_diseno ? asset('storage/' . $this->archivo_diseno) : null;
    }

    // Método para obtener el precio total (base + adicional)
    public function getPrecioTotal($precioBase = 0)
    {
        return $precioBase + $this->precio_adicional;
    }

    // Método para verificar si tiene una etiqueta específica
    public function tieneTag($tag)
    {
        return in_array($tag, $this->tags ?? []);
    }

    // Método para obtener colores como string
    public function getColoresString()
    {
        return $this->colores_disponibles ? implode(', ', $this->colores_disponibles) : 'N/A';
    }
}
