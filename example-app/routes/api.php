<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginAlumno;
use App\Http\Controllers\Auth\LoginProfesor;
use App\Http\Controllers\Admin\PeriodoController;
use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\AlumnoController;
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

#Registar Peridos
Route::post('/periodos', [PeriodoController::class, 'store']);
Route::get('/periodos', [PeriodoController::class, 'index']);

#REGISTROS vista de carreras
Route::get('/carreras', [CarreraController::class, 'index']);
Route::post('/carreras', [CarreraController::class, 'store']);
Route::put('/carreras/{id}', [CarreraController::class, 'update']);

#REGISTROS vista de alumnos
Route::get('/alumnos', [AlumnoController::class, 'index']);
Route::post('/alumnos', [AlumnoController::class, 'store']);
Route::post('/alumnos/csv', [AlumnoController::class, 'importarDesdeCSV']);
Route::put('/alumnos/{id}', [AlumnoController::class, 'update']);
Route::delete('/alumnos/{id}', [AlumnoController::class, 'destroy']);

#REGISTROS vista de profesores
