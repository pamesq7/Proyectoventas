<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            
            // REDIRIGIR SEGÚN EL ROL DEL EMPLEADO
            return $this->redirectToDashboard($user);
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    }

    /**
     * Mostrar formulario de registro
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Procesar registro
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        // Usar el mismo método de redirección
        return $this->redirectToDashboard($user);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Redirigir según el rol del empleado
     */
    private function redirectToDashboard($user)
    {
        // Obtener el empleado relacionado con el usuario
        $empleado = $user->empleado;

        if ($empleado) {
            // Verifica el campo 'rol' del empleado y redirige al dashboard correspondiente
            switch ($empleado->rol) {
                case 'administrador':
                    return redirect()->route('dashboard.admin');
                case 'diseñador':
                    return redirect()->route('dashboard.disenador');
                case 'vendedor':
                    return redirect()->route('dashboard.vendedor');
                case 'operador':
                    return redirect()->route('dashboard.operador');
                case 'cliente':
                    return redirect()->route('dashboard.cliente');
                default:
                    // Si no tiene rol definido, redirigir a un dashboard genérico
                    return redirect('/dashboard');
            }
        } else {
            // Si no tiene empleado relacionado, redirigir a la página de inicio
            return redirect()->route('home');
        }
    }
}