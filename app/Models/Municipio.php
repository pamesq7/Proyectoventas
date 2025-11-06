<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';
    protected $primaryKey = 'idMunicipio';
    public $timestamps = false;

    protected $fillable = ['nombreMunicipio', 'estado', 'idProvincia'];

    // belongsTo Provincia
    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'idProvincia', 'idProvincia');
    }

    // hasMany Direccion
    public function direccion()
    {
        return $this->hasMany(Direccion::class, 'idMunicipio', 'idMunicipio');
    }

    public function scopeActivos($q)
    {
        return $q->where('estado', '1');
    }
}
