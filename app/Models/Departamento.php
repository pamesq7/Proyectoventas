<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamentos';
    protected $primaryKey = 'idDepartamento';
    public $timestamps = false;

    protected $fillable = ['nombreDepartamento', 'estado'];

    // Relación: un departamento tiene muchas provincias
    public function provincia()
    {
        return $this->hasMany(Provincia::class, 'idDepartamento', 'idDepartamento');
    }

    // Scope
    public function scopeActivos($q)
    {
        return $q->where('estado', '1');
    }
}
