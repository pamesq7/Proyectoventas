<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante extends Model
{
    use HasFactory;

    protected $table = 'variantes';
    protected $primaryKey = 'idVariante';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class, 'idVariante', 'idVariante');
    }

    // Alias para mantener compatibilidad con el código existente
    public function varianteCaracteristicas()
    {
        return $this->hasMany(VarianteCaracteristica::class, 'idVariante', 'idVariante');
    }
}
