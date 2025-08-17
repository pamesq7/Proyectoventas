<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCaracteristica extends Model
{
    use HasFactory;

    protected $table = 'producto_caracteristicas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'precioAdicional',
        'idProducto',
        'idCaracteristica'
    ];

    protected $casts = [
        'estado' => 'boolean',
        'precioAdicional' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relación: pertenece a un producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'idProducto', 'idProducto');
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

    // Método helper: precio total (precio base + adicional)
    public function getPrecioTotalAttribute()
    {
        return $this->producto->precioVenta + ($this->precioAdicional ?? 0);
    }

    // Accesor: estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }
}
