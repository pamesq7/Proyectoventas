<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diseno extends Model
{
    use HasFactory;

    protected $table = 'disenos';
    protected $primaryKey = 'idDiseno';

    protected $fillable = [
        'archivo',
        'comentario',
        'estado',
        'estadoDiseno',
        'idDetalleVenta',
        'idEmpleado'
    ];

    protected $casts = [
        'estado' => 'integer',
        'estadoDiseno' => 'string'
    ];

    // Relación con empleado (diseñador)
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado', 'idEmpleado');
    }

    // Relación con detalle de venta (opcional)
    public function detalleVenta()
    {
        return $this->belongsTo(DetalleVenta::class, 'idDetalleVenta', 'idDetalleVenta');
    }

     public function productoDiseno()
    {
        return $this->hasMany(ProductoDiseno::class, 'idDiseno', 'idDiseno');
    }

    // Scope para diseños activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // Scope para diseños en proceso
    public function scopeEnProceso($query)
    {
        return $query->where('estadoDiseno', 'borrador');
    }

    // Scope para diseños terminados
    public function scopeTerminados($query)
    {
        return $query->where('estadoDiseno', 'terminado');
    }

    // Accessor para URL de archivo
    public function getArchivoUrlAttribute()
    {
        return $this->archivo ? asset('storage/' . $this->archivo) : null;
    }

    // Método para obtener estado como texto
    public function getEstadoTextoAttribute()
    {
        return $this->estado == 1 ? 'Activo' : 'Inactivo';
    }
    // Método para marcar diseño como terminado
    public function marcarComoTerminado($nuevaImagen)
    {
        // Actualiza el archivo del diseño y el estado del diseño
        $this->archivo = $nuevaImagen;
        $this->estadoDiseno = 'terminado'; // Cambiar de 'borrador' a 'terminado'
        $this->save();
        // Actualizar el estado de la venta asociada a este diseño
        $venta = $this->detalleVenta->venta; // Obtenemos la venta asociada al detalle

        // Cambiar el estado de la venta a 'producción'
        if ($this->detalleVenta) {
            $this->detalleVenta->venta->estadoPedido = 2; // '2' es Producción
            $this->detalleVenta->venta->save();
        }
    }
}
