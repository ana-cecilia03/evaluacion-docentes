<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginAlumno;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí registras las rutas de la API de tu aplicación. Estas rutas son
| cargadas por RouteServiceProvider dentro del grupo "api".
| Puedes acceder a ellas desde /api/...
|
*/

// ✅ Ruta funcional para probar Login Alumno
Route::post('/login/alumno', [LoginAlumno::class, 'login']);

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
