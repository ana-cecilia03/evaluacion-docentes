<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureRoleCookie
{
    /**
     * Uso:
     * - 'front.auth'         -> requiere cookie logged_in=1 (cualquier rol)
     * - 'front.auth:admin'   -> requiere logged_in=1 y role=admin
     * - 'front.auth:alumno'  -> requiere logged_in=1 y role=alumno
     */
    public function handle(Request $request, Closure $next, $role = null)
    {
        $isLogged = $request->cookie('logged_in') === '1';
        if (!$isLogged) return redirect('/bienvenida');

        if ($role && $request->cookie('role') !== $role) {
            return redirect('/bienvenida');
        }
        return $next($request);
    }
}
