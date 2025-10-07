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

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Obtener el empleado asociado al usuario
            $user = Auth::user();
            $empleado = $user->empleado; // Asegúrate de que esta relación exista

            if ($empleado) {
                // Redirigir según el rol del empleado
                switch ($empleado->cargo) {
                    case 'administrador':
                        return redirect()->route('dashboard.admin');
                    case 'diseñador':
                        return redirect()->route('dashboard.disenador');
                    case 'vendedor':
                        return redirect()->route('dashboard.vendedor');
                    case 'operador':
                        return redirect()->route('dashboard.operador');
                    default:
                        return redirect()->route('home'); // Redirigir a la página de inicio si el cargo no coincide
                }
            }

            // Si no tiene empleado asociado, redirigir a la página de inicio
            return redirect()->route('home');
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

        return $this->redirectByRole($user);
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
     * Redirigir según el rol del usuario
     */
    private function redirectByRole($user)
    {
        if (!$user->empleado) {
            return redirect()->route('home');
        }

        switch (strtolower(trim($user->empleado->cargo))) {
            case 'administrador':
                return redirect()->route('dashboard.admin');
            case 'vendedor':
                return redirect()->route('dashboard.vendedor');
            case 'diseñador':
                return redirect()->route('dashboard.disenador');
            case 'operador':
                return redirect()->route('dashboard.operador');
            default:
                return redirect()->route('home');
        }
    }
}
