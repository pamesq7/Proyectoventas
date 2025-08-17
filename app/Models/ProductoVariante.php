<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoVariante extends Model
{
    use HasFactory;

    protected $table = 'producto_variantes';
    
    protected $fillable = [
        'idProducto',
        'idVariante',
        'precioAdicional',
        'stockVariante',
        'estado'
    ];

    protected $casts = [
        'precioAdicional' => 'decimal:2',
        'stockVariante' => 'integer',
        'estado' => 'boolean'
    ];

    // 🔸 Relación con Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto');
    }

    // 🔸 Relación con Variante
    public function variante()
    {
        return $this->belongsTo(Variante::class, 'idVariante');
    }

    // 🔸 Scope para registros activos
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }

    // 🔸 Método para calcular precio total
    public function getPrecioTotalAttribute()
    {
        $precioBase = $this->producto->precioVenta ?? 0;
        return $precioBase + $this->precioAdicional;
    }

    // 🔸 Método para verificar disponibilidad
    public function getDisponibleAttribute()
    {
        return $this->stockVariante > 0 && $this->estado == 1;
    }
}
