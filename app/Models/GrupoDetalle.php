<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GrupoDetalle extends Model
{
    use HasFactory;

    protected $table = 'grupo_detalles';
    protected $primaryKey = 'idGrupoDetalle';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    // 🔸 Relación: un grupo puede tener muchos detalles de venta
    public function detalleVentas()
    {
        return $this->hasMany(DetalleVenta::class, 'idGrupoDetalle');
    }

    // 🔸 Relación: un grupo puede tener muchas variantes características asociadas
    public function detalleVentaVarCaracteristicas()
    {
        return $this->hasMany(DetalleVentaVarCaracteristica::class, 'idGrupoDetalle');
    }

    // Accessor para mostrar estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
