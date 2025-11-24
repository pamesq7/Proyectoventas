<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;



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
        // VALIDACIÓN (ci es opcional)
        $request->validate([
            'ci' => 'nullable|string|max:20|unique:users', // ✅ nullable
            'name' => 'required|string|max:255',
            'primerApellido' => 'required|string|max:255',
            'segundoApellido' => 'nullable|string|max:255', // ✅ nullable
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // CREACIÓN DEL USUARIO
        $user = User::create([
            'ci' => $request->ci, // ✅ Puede ser NULL
            'name' => $request->name,
            'primerApellido' => $request->primerApellido,
            'segundoApellido' => $request->segundoApellido, // ✅ Puede ser NULL
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);
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
                    return redirect('/home');
            }
        } else {
            // Si no tiene empleado relacionado, redirigir a la página de inicio
            return redirect()->route('dashboard.cliente');
        }
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $response = Password::sendResetLink(
        $request->only('email')
    );

    return $response === Password::RESET_LINK_SENT
        ? back()->with('status', __($response))
        : back()->withErrors(['email' => __($response)]);
}
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    return $status === Password::PASSWORD_RESET
        ? redirect()->route('login')->with('status', __($status))
        : back()->withErrors(['email' => [__($status)]]);
}

    public function showChangePasswordForm()
    {
        return view('auth.passwords.change');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        return redirect()->route('home')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }
}

