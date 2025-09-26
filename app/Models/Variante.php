<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;

    protected $table = 'variantes';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'idVariante', 'id');
    }

    // Relación con características a través de la tabla intermedia
    public function caracteristicas()
    {
        return $this->belongsToMany(
            Caracteristica::class,
            'variante_caracteristicas', // nombre de la tabla intermedia
            'idVariante',               // foreign key en la tabla intermedia
            'idCaracteristica'          // related key en la tabla intermedia
        )->withTimestamps();
    }
    
    // Alias para mantener compatibilidad con el código existente
    public function varianteCaracteristicas()
    {
        return $this->caracteristicas();
    }
}
