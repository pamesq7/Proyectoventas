<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->empleado) {
                switch (strtolower(trim($user->empleado->cargo))) {
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
            }

            return view('home');
        }

        return view('home');
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
