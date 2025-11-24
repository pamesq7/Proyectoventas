<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ClienteNatural;
use App\Models\ClienteEstablecimiento;

class ClienteController extends Controller
{
    /**
     * Mostrar formulario de login para clientes
     */
    public function showLogin()
    {
        return view('auth.cliente-login');
    }

    /**
     * Procesar login de cliente
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuario cliente
        $user = User::where('email', $request->email)
                   ->whereHas('clienteNatural')
                   ->first();

        if ($user && Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('cliente.dashboard');
        }

        return back()->withErrors([
            'email' => 'Credenciales incorrectas o no eres un cliente registrado.',
        ]);
    }

    /**
     * Cerrar sesión de cliente
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    // En app/Http/Controllers/ClienteController.php
public function buscarClientes(Request $request)
{
    $search = $request->input('q');
    
    $clientes = ClienteNatural::where('nombre', 'like', "%{$search}%")
        ->orWhere('apellido', 'like', "%{$search}%")
        ->orWhere('documento', 'like', "%{$search}%")
        ->get()
        ->map(function($cliente) {
            return [
                'id' => 'natural:' . $cliente->idClienteNatural,
                'text' => $cliente->nombre . ' ' . $cliente->apellido . ' (' . $cliente->documento . ')'
            ];
        });

    $establecimientos = ClienteEstablecimiento::where('nombre', 'like', "%{$search}%")
        ->orWhere('razon_social', 'like', "%{$search}%")
        ->orWhere('nit', 'like', "%{$search}%")
        ->get()
        ->map(function($establecimiento) {
            return [
                'id' => 'establecimiento:' . $establecimiento->idClienteEstablecimiento,
                'text' => $establecimiento->nombre . ' (' . $establecimiento->nit . ')'
            ];
        });

    return response()->json([
        'results' => $clientes->merge($establecimientos)
    ]);
}
}