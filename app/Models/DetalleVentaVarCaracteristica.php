<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVentaVarCaracteristica extends Model
{
    use HasFactory;

    protected $table = 'detalle_venta_var_caracteristicas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'estado',
        'idVarCaracteristicas',
        'idGrupoDetalle',
    ];

    // 🔸 Relación: pertenece a una variante caracteristica
    public function varianteCaracteristica()
    {
        return $this->belongsTo(VarianteCaracteristica::class, 'idVarCaracteristicas');
    }

    // 🔸 Relación: pertenece a un grupo detalle
    public function grupoDetalle()
    {
        return $this->belongsTo(GrupoDetalle::class, 'idGrupoDetalle');
    }

    // Accessor para estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
}
