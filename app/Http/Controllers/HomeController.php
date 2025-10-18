<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->empleado)
                switch (strtolower(trim($user->empleado->rol))) {
                    case 'administrador':
                        return redirect()->route('dashboard.admin');
                    case 'diseñador':
                        return redirect()->route('dashboard.disenador');
                    case 'vendedor':
                        return redirect()->route('dashboard.vendedor');
                    case 'operador':
                        return redirect()->route('dashboard.operador');
                    default:
                        return view('home');
                }
        } else {
            // Si no es empleado, es cliente
            return redirect()->route('dashboard.cliente');
        }
    }

    public function adminDashboard()
    {
        return view('dashboard.admin');
    }

    /**
     * Dashboard para vendedores
     */
    public function vendedorDashboard()
    {
        return view('dashboard.vendedor');
    }

    /**
     * Dashboard para diseñadores
     */
    public function disenadorDashboard()
    {
        // Verificar que el usuario tenga rol de diseñador
        if (!auth()->user()->empleado || strtolower(auth()->user()->empleado->rol) !== 'diseñador') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return view('dashboard.disenador');
    }

    /**
     * Dashboard para operadores
     */
    public function operadorDashboard()
    {
        // Verificar que el usuario tenga rol de operador
        if (!auth()->user()->empleado || strtolower(auth()->user()->empleado->rol) !== 'operador') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return view('dashboard.operador');
    }

    /**
     * Dashboard para clientes
     */
    public function clienteDashboard()
    {
        return view('dashboard.cliente');
    }
}
