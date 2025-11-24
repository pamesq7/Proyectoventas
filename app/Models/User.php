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
    public $incrementing = true;   // según tu tabla
    protected $keyType = 'int';

    protected $fillable = [
        'ci',
        'name',
        'primerApellido',
        'segundApellido',
        'email',
        'telefono',
        'password',
        'estado',
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

    // 🔹 Relación: un usuario (cliente) puede tener muchas ventas/pedidos
    public function pedidos()
    {
        // Si es cliente natural
        if ($this->clienteNatural) {
            return $this->hasMany(Venta::class, 'idCliente', 'idUser');
        }
        
        // Si es representante de establecimiento
        if ($this->clienteEstablecimiento) {
            return $this->hasMany(Venta::class, 'idEstablecimiento', 'idUser');
        }
        
        // Retornar relación vacía por defecto
        return $this->hasMany(Venta::class, 'idCliente', 'idUser')->whereRaw('1=0');
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

    /**
     * 🔥 NUEVO: DETERMINAR EL ROL DEL USUARIO
     */
    public function getRolAttribute()
    {
        // Si es empleado, retornar su rol
        if ($this->empleado) {
            return $this->empleado->rol;
        }

        // Si es cliente natural, retornar 'cliente'
        if ($this->clienteNatural) {
            return 'cliente';
        }

        // Si no es ni empleado ni cliente, es usuario básico
        return 'usuario';
    }

    /**
     * 🔥 NUEVO: VERIFICAR SI TIENE UN ROL ESPECÍFICO
     */
    public function hasRol($rol)
    {
        return $this->rol === $rol;
    }

    /**
     * 🔥 NUEVO: VERIFICAR SI TIENE ALGUNO DE LOS ROLES
     */
    public function hasAnyRol($roles)
    {
        if (is_array($roles)) {
            return in_array($this->rol, $roles);
        }
        return $this->rol === $roles;
    }

    /**
     * 🔥 NUEVO: VERIFICAR SI ES EMPLEADO
     */
    public function isEmpleado()
    {
        return !is_null($this->empleado);
    }

    /**
     * 🔥 NUEVO: VERIFICAR SI ES CLIENTE
     */
    public function isCliente()
    {
        return !is_null($this->clienteNatural);
    }

    /**
     * 🔥 NUEVO: VERIFICAR SI ES USUARIO BÁSICO
     */
    public function isUsuarioBasico()
    {
        return !$this->isEmpleado() && !$this->isCliente();
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

    /**
     * 🔥 NUEVO: Scope para filtrar por rol
     */
    public function scopePorRol($query, $rol)
    {
        if ($rol === 'empleado') {
            return $query->whereHas('empleado');
        } elseif ($rol === 'cliente') {
            return $query->whereHas('clienteNatural');
        } elseif (in_array($rol, self::getRolesEmpleados())) {
            return $query->whereHas('empleado', function ($q) use ($rol) {
                $q->where('rol', $rol);
            });
        }

        return $query;
    }
}
