<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'idDireccion';
    public $timestamps = false;

    protected $fillable = ['nombreDireccion', 'estado', 'idMunicipio'];

    // belongsTo Municipio
    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'idMunicipio', 'idMunicipio');
    }
}
