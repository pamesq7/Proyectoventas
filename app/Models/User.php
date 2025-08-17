<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'idUser';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    
    protected $fillable = [
        'ci',
        'name',
        'primerApellido',
        'segundApellido',
        'email',
        'telefono',
        'password',
        'estado',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'estado' => 'boolean',
    ];

    protected $appends = [
        'nombre_completo',
        'tipo_usuario',
    ];

    // 🔸 Relación: un usuario puede ser un cliente natural
    public function clienteNatural()
    {
        return $this->hasOne(ClienteNatural::class, 'idCliente', 'idUser');
    }

    // 🔸 Relación: un usuario puede representar un establecimiento
    public function clienteEstablecimiento()
    {
        return $this->hasOne(ClienteEstablecimiento::class, 'idRepresentante', 'idUser');
    }

    // 🔸 Relación: un usuario puede ser un empleado
    public function empleado()
    {
        return $this->hasOne(Empleado::class, 'idEmpleado', 'idUser');
    }

    // 🔸 Relación: un usuario puede tener muchas transacciones
    public function transacciones()
    {
        return $this->hasMany(Transaccion::class, 'idUser', 'idUser');
    }

    // 🔸 Método helper: obtener tipo de usuario
    public function getTipoUsuarioAttribute()
    {
        if ($this->clienteNatural) {
            return 'Cliente Natural';
        } elseif ($this->clienteEstablecimiento) {
            return 'Cliente Establecimiento';
        } elseif ($this->empleado) {
            return 'Empleado';
        }
        return 'Usuario Base';
    }

    // 🔸 Método helper: obtener nombre completo
    public function getNombreCompletoAttribute()
    {
        return trim($this->name . ' ' . $this->primerApellido . ' ' . $this->segundApellido);
    }

    // 🔸 Método helper: verificar si está activo
    public function estaActivo()
    {
        return $this->estado == 1;
    }

    // 🔸 Scope: usuarios activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 1);
    }

    // 🔸 Scope: buscar por CI o nombre
    public function scopeBuscar($query, $search)
    {
        return $query->where('ci', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('primerApellido', 'like', "%{$search}%");
    }
}