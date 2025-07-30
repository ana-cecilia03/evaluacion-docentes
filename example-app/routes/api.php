<?php

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
use App\Models\Profesor;
use App\Models\Periodo;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Carrera;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// =======================
// 🔐 Autenticación
// =======================
Route::post('/login/alumno', [LoginAlumno::class, 'login']);
Route::post('/admin/login', [LoginProfesor::class, 'login']);

// =======================
// 📆 Periodos
// =======================
Route::prefix('periodos')->group(function () {
    Route::post('/', [PeriodoController::class, 'store']);
    Route::get('/', [PeriodoController::class, 'index']);
    Route::put('/{id}/estado', [PeriodoController::class, 'cambiarEstado']);
    Route::get('/activos', [PeriodoController::class, 'activos']);
});

// =======================
// 🎓 Carreras
// =======================
Route::prefix('carreras')->group(function () {
    Route::get('/', [CarreraController::class, 'index']);
    Route::post('/', [CarreraController::class, 'store']);
    Route::put('/{id}', [CarreraController::class, 'update']);
    Route::get('/nombres', [CarreraController::class, 'nombres']);
    Route::get('/unicas', function () {
        return MatCuatriCar::selectRaw('MIN(id_mat_cuatri_car) as id_mat_cuatri_car, carrera_nombre')
            ->groupBy('carrera_nombre')
            ->orderBy('carrera_nombre')
            ->get();
    });
    Route::get('/por-periodo/{num}', [MatCuatriCarController::class, 'carrerasPorPeriodo']);
});

// =======================
// 👨‍🎓 Alumnos
// =======================
Route::prefix('alumnos')->group(function () {
    Route::get('/', [AlumnoController::class, 'index']);
    Route::post('/', [AlumnoController::class, 'store']);
    Route::post('/csv', [AlumnoController::class, 'importarDesdeCSV']);
    Route::put('/{id}', [AlumnoController::class, 'update']);
    Route::delete('/{id}', [AlumnoController::class, 'destroy']);
    Route::post('/desactivar', [AlumnoController::class, 'desactivarVarios']);
});

// =======================
// 👨‍🏫 Profesores
// =======================
Route::prefix('profesores')->group(function () {
    Route::get('/', [ProfesorController::class, 'index']);
    Route::post('/', [ProfesorController::class, 'store']);
    Route::put('/{id}', [ProfesorController::class, 'update']);
    Route::post('/csv', [ProfesorController::class, 'importarDesdeCSV']);
    Route::get('/activos', fn() => Profesor::where('status', 'activo')->get());
});

// =======================
// 🏫 Grupos
// =======================
Route::prefix('grupos')->group(function () {
    Route::get('/', [GrupoController::class, 'index']);
    Route::post('/', [GrupoController::class, 'store']);
    Route::put('/{id}', [GrupoController::class, 'update']);
    Route::post('/csv', [GrupoController::class, 'importarDesdeCSV']);
    Route::get('/nombres', fn() => Grupo::select('nombre')->get());
});

// =======================
// 📚 Materias
// =======================
Route::prefix('materias')->group(function () {
    Route::get('/', [MateriaController::class, 'index']);
    Route::post('/', [MateriaController::class, 'store']);
    Route::put('/{id}', [MateriaController::class, 'update']);
    Route::post('/csv', [MateriaController::class, 'importarDesdeCSV']);
    Route::get('/nombres', [MateriaController::class, 'nombres']);
    Route::get('/por-periodo/{num}', [MatCuatriCarController::class, 'materiasPorPeriodo']);
    Route::get('/por-carrera/{nombre}', function ($nombre) {
        return MatCuatriCar::where('carrera_nombre', $nombre)
            ->select('materia_nombre')
            ->distinct()
            ->get();
    });
});

// =======================
// 📘 Cuatrimestres
// =======================
Route::prefix('cuatrimestres')->group(function () {
    Route::get('/', [CuatrimestreController::class, 'index']);
    Route::post('/', [CuatrimestreController::class, 'store']);
    Route::put('/{id}', [CuatrimestreController::class, 'update']);
    Route::get('/numeros', [CuatrimestreController::class, 'numeros']);
});

// =======================
// 🔁 MatCuatriCar
// =======================
Route::prefix('mat-cuatri-car')->group(function () {
    Route::get('/', [MatCuatriCarController::class, 'index']);
    Route::post('/', [MatCuatriCarController::class, 'store']);
});

// =======================
// 📋 Relaciones prof-materia-grupo
// =======================
Route::prefix('relaciones')->group(function () {
    Route::get('/', [RelacionController::class, 'index']);
    Route::post('/', [RelacionController::class, 'store']);
});

// =======================
// 📥 Selects genéricos para formularios
// =======================
Route::prefix('selects')->group(function () {
    Route::get('/profesores', fn() => Profesor::all());
    Route::get('/periodos', fn() => Periodo::all());
    Route::get('/materias', fn() => Materia::all());
    Route::get('/grupos', fn() => Grupo::all());
});
