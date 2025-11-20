<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Talla extends Model
{
    use HasFactory;

    protected $table = 'tallas';
    protected $primaryKey = 'idTallas';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    // 🔸 Relación: una talla puede pertenecer a muchos productos (producto_tallas)
    public function detalleTalla()
    {
        return $this->hasMany(DetalleTalla::class, 'idTallas', 'idTallas');
    }

    // Accesor opcional para mostrar el estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Disponible' : 'No disponible';
    }
    // Alias para usar idTalla en vistas sin cambiar BD
    public function getIdTallaAttribute()
    {
        return $this->attributes['idTallas'] ?? null;
    }
    // En app/Models/Talla.php
    public function detalleVentas()
    {
        return $this->belongsToMany(
            DetalleVenta::class,
            'detalle_tallas',
            'idTallas',           // Ajustado a idTallas
            'idDetalleVenta'
        )->withPivot('cantidad');
    }
}
