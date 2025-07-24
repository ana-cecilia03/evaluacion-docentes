<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginAlumno;
use App\Http\Controllers\Auth\LoginProfesor;
use App\Http\Controllers\Admin\PeriodoController;
use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\AlumnoController;
use App\Http\Controllers\Admin\ProfesorController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\MateriaController;


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
Route::put('/periodos/{id}/estado', [PeriodoController::class, 'cambiarEstado']);


#REGISTROS vista de carreras
Route::get('/carreras', [CarreraController::class, 'index']);
Route::post('/carreras', [CarreraController::class, 'store']);
Route::put('/carreras/{id}', [CarreraController::class, 'update']);



# REGISTROS vista de alumnos
Route::get('/alumnos', [AlumnoController::class, 'index']);
Route::post('/alumnos', [AlumnoController::class, 'store']);
Route::post('/alumnos/csv', [AlumnoController::class, 'importarDesdeCSV']);
Route::put('/alumnos/{id}', [AlumnoController::class, 'update']);
Route::delete('/alumnos/{id}', [AlumnoController::class, 'destroy']);

# NUEVA ruta para desactivar varios alumnos
Route::post('/alumnos/desactivar', [AlumnoController::class, 'desactivarVarios']);

#----------
#REGISTROS vista de profesores

Route::get('/profesores', [ProfesorController::class, 'index']);
Route::post('/profesores', [ProfesorController::class, 'store']);
Route::put('/profesores/{id}', [ProfesorController::class, 'update']);
Route::post('/profesores/csv', [ProfesorController::class, 'importarDesdeCSV']);

#REGISTROS vista Grupos
Route::get('/grupos', [GrupoController::class, 'index']);
Route::post('/grupos', [GrupoController::class, 'store']);
Route::put('/grupos/{id}', [GrupoController::class, 'update']);
Route::post('/grupos/csv', [GrupoController::class, 'importarDesdeCSV']);

#REGISTROS de vista Materias

Route::prefix('materias')->group(function () {
    Route::get('/', [MateriaController::class, 'index']);
    Route::post('/', [MateriaController::class, 'store']);
    Route::put('/{id}', [MateriaController::class, 'update']);
    Route::post('/csv', [MateriaController::class, 'importarDesdeCSV']);
});
