<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Redirige a la ruta de login si el usuario no está autenticado.
     */
    protected function redirectTo($request)
    {    
    if (! $request->expectsJson()) {
        return route('bienvenida'); // Redirige a tu vista personalizada
    }
    }
}
