<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Si el usuario está autenticado, redirigir según su rol
        if (auth()->check()) {
            $user = auth()->user();
            
            switch ($user->rol) {
                case 'administrador':
                    return redirect()->route('dashboard');
                case 'vendedor':
                    return redirect()->route('dashboard.vendedor');
                case 'diseñador':
                    return redirect()->route('dashboard.disenador');
                case 'operador':
                    return redirect()->route('dashboard.operador');
                case 'cliente':
                    return redirect()->route('dashboard.cliente');
                default:
                    return view('home'); // Usuario básico
            }
        }
        
        // Usuario no autenticado - página pública
        return view('home');
    }

    /**
     * Dashboard para administradores
     */
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
    public function diseñadorDashboard()
    {
        return view('dashboard.diseñador');
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