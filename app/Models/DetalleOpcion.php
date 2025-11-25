<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleOpcion extends Model
{
    use HasFactory;

    protected $table = 'detalle_opciones';
    protected $primaryKey = 'idDetalleOpcion';

    protected $fillable = [
        'idDetalleVenta',
        'idOpcion',
        'idCaracteristica',
        'nombre',
        'precioAdicional'
    ];

    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    public function opcion()
    {
        return $this->belongsTo(Opcion::class, 'idOpcion', 'idOpcion');
    }

    public function caracteristica()
    {
        return $this->belongsTo(Caracteristica::class, 'idCaracteristica', 'idCaracteristica');
    }

    public function scopeTipoPrenda($query)
    {
        return $query->whereHas('opcion', function($q) {
            $q->where('nombre', 'LIKE', '%tipo%');
        });
    }
}