<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        // Si el usuario está autenticado y no ha cambiado su contraseña
        if ($user && is_null($user->password_changed_at)) {
            // Excluimos las rutas de cambio de contraseña y logout
            if (!$request->is('password/change*') && 
                !$request->is('logout') && 
                !$request->is('password/update')) {
                return redirect()->route('password.change');
            }
        }

        return $next($request);
    }
}