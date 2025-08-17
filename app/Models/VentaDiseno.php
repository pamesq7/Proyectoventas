<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaDiseno extends Model
{
    use HasFactory;

    protected $table = 'venta_disenos';
    protected $primaryKey = 'idventaDiseno';

    protected $fillable = [
        'observacion',
        'estado',
        'idDiseñador',
        'idventa',
        'idDiseno',
    ];

    // 🔸 Relación: pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'idventa');
    }

    // 🔸 Relación: pertenece a un diseño
    public function diseno()
    {
        return $this->belongsTo(Diseno::class, 'idDiseno');
    }

    // 🔸 Relación opcional: diseñador (empleado)
    public function disenador()
    {
        return $this->belongsTo(Empleado::class, 'idDiseñador');
    }

    // Accessor: mostrar estado en texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Anulado';
    }
}
