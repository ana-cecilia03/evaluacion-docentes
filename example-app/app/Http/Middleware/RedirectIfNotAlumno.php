<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotAlumno
{
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('alumno')->check()) {
            return redirect()->route('bienvenida'); // Redirige si no está logueado como alumno
        }

        return $next($request);
    }
}
