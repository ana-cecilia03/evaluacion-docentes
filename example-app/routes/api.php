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
use App\Http\Controllers\Admin\CuatrimestreController;
use App\Http\Controllers\Admin\MatCuatriCarController;
use App\Http\Controllers\Admin\RelacionController;

use App\Models\MatCuatriCar;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Login
Route::post('/login/alumno', [LoginAlumno::class, 'login']);
Route::post('/admin/login', [LoginProfesor::class, 'login']);

// Periodos
Route::post('/periodos', [PeriodoController::class, 'store']);
Route::get('/periodos', [PeriodoController::class, 'index']);
Route::put('/periodos/{id}/estado', [PeriodoController::class, 'cambiarEstado']);
Route::get('/periodos/activos', [PeriodoController::class, 'activos']);


// Carreras
Route::get('/carreras', [CarreraController::class, 'index']);
Route::post('/carreras', [CarreraController::class, 'store']);
Route::put('/carreras/{id}', [CarreraController::class, 'update']);
Route::get('/carreras/nombres', [CarreraController::class, 'nombres']);

// Alumnos
Route::get('/alumnos', [AlumnoController::class, 'index']);
Route::post('/alumnos', [AlumnoController::class, 'store']);
Route::post('/alumnos/csv', [AlumnoController::class, 'importarDesdeCSV']);
Route::put('/alumnos/{id}', [AlumnoController::class, 'update']);
Route::delete('/alumnos/{id}', [AlumnoController::class, 'destroy']);
Route::post('/alumnos/desactivar', [AlumnoController::class, 'desactivarVarios']);

// Profesores
Route::get('/profesores', [ProfesorController::class, 'index']);
Route::post('/profesores', [ProfesorController::class, 'store']);
Route::put('/profesores/{id}', [ProfesorController::class, 'update']);
Route::post('/profesores/csv', [ProfesorController::class, 'importarDesdeCSV']);

// Grupos
Route::get('/grupos', [GrupoController::class, 'index']);
Route::post('/grupos', [GrupoController::class, 'store']);
Route::put('/grupos/{id}', [GrupoController::class, 'update']);
Route::post('/grupos/csv', [GrupoController::class, 'importarDesdeCSV']);

// Materias
Route::prefix('materias')->group(function () {
    Route::get('/', [MateriaController::class, 'index']);
    Route::post('/', [MateriaController::class, 'store']);
    Route::put('/{id}', [MateriaController::class, 'update']);
    Route::post('/csv', [MateriaController::class, 'importarDesdeCSV']);
});
Route::get('/materias/nombres', [MateriaController::class, 'nombres']);

// Cuatrimestres
Route::get('/cuatrimestres', [CuatrimestreController::class, 'index']);
Route::post('/cuatrimestres', [CuatrimestreController::class, 'store']);
Route::put('/cuatrimestres/{id}', [CuatrimestreController::class, 'update']);
Route::get('/cuatrimestres/numeros', [CuatrimestreController::class, 'numeros']);

// MatCuatriCar (Relaciones entre carrera, cuatrimestre y materia)
Route::get('/mat-cuatri-car', [MatCuatriCarController::class, 'index']);
Route::post('/mat-cuatri-car', [MatCuatriCarController::class, 'store']);
Route::get('/carreras/por-periodo/{num}', [MatCuatriCarController::class, 'carrerasPorPeriodo']);
Route::get('/materias/por-periodo/{num}', [MatCuatriCarController::class, 'materiasPorPeriodo']);

//prof_materia
Route::get('/relaciones', [RelacionController::class, 'index']);
Route::post('/relaciones', [RelacionController::class, 'store']);

// Para llenar selects
Route::get('/profesores', fn() => \App\Models\Profesor::all());
Route::get('/periodos', fn() => \App\Models\Periodo::all());
Route::get('/carreras', function () {
    return MatCuatriCar::selectRaw('MIN(id_mat_cuatri_car) as id_mat_cuatri_car, carrera_nombre')
        ->groupBy('carrera_nombre')
        ->get();
});

Route::get('/materias', fn() => \App\Models\Materia::all());
Route::get('/grupos', fn() => \App\Models\Grupo::all());

