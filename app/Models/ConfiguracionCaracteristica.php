<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfiguracionCaracteristica extends Model
{
    use HasFactory;

    protected $table = 'configuracion_caracteristicas';

    protected $primaryKey = 'id';

    protected $fillable = [
        'valor',
        'idDetalleVenta',
        'idCaracteristica',
        'idOpcion',
        'idComponente'
    ];

    // Relación: pertenece a un detalle de venta
    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    // Relación: pertenece a una característica
    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class, 'idCaracteristica', 'idCaracteristica');
    }

    // (Opcional) Si quieres relación con opcion
    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'idOpcion', 'idOpcion');
    }
}
