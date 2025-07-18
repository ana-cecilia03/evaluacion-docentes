<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginAlumno;
use App\Http\Controllers\Auth\LoginProfesor;

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

#Api Loginprofesores
Route::post('/admin/login', [LoginProfesor::class, 'login']);
