<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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
}