<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTalla extends Model
{
    use HasFactory;

    protected $table = 'detalle_tallas';
    protected $primaryKey = 'idDetalleTalla';

    protected $fillable = [
        'idDetalleVenta',   // <-- agregar
        'idTallas',         // <-- agregar
        'nombre',
        'numero',
        'adicional',
        'estado',
    ];
    protected $casts = [
        'numero' => 'integer',
        'estado' => 'boolean', // CHAR(1) -> true/false
    ];


    // 🔸 Relación: una talla puede pertenecer a muchos productos (producto_tallas)
    public function talla()
    {
        return $this->belongsTo(Talla::class, 'idTallas', 'idTallas');
    }

    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'idDetalleVenta', 'idDetalleVenta');
    }

    // Accesor opcional para mostrar el estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Disponible' : 'No disponible';
    }
}
