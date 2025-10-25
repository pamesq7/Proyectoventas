<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        /** @var \App\Models\User|null $user */
        $user = auth()->user();
        
        // Verificar si el usuario está autenticado
        if (!$user) {
            return redirect()->route('login');
        }

        // Cargar relaciones necesarias
        $user->load(['empleado', 'clienteNatural', 'clienteEstablecimiento']);
        
        // Verificar si el usuario es un empleado
        if (!$user->empleado) {
            // Si no es empleado, verificar si es cliente
            if ($user->clienteNatural || $user->clienteEstablecimiento) {
                return $next($request);
            }
            abort(403, 'Usuario no tiene un rol asignado.');
        }

        // Verificar si el rol del empleado está en los roles permitidos
        if (!in_array($user->empleado->rol, $roles)) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
        
        return $next($request);
    }
}
