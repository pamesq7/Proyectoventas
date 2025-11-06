<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';
    protected $primaryKey = 'idProvincia';
    public $timestamps = false;

    protected $fillable = ['nombreProvincia', 'estado', 'idDepartamento'];

    // belongsTo Departamento
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'idDepartamento', 'idDepartamento');
    }

    // hasMany Municipio
    public function municipios()
    {
        return $this->hasMany(Municipio::class, 'idProvincia', 'idProvincia');
    }

    public function scopeActivos($q)
    {
        return $q->where('estado', '1');
    }
}
