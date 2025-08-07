<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Importación de controladores

// Autenticación
use App\Http\Controllers\Auth\LoginAlumno;
use App\Http\Controllers\Auth\LoginProfesor;

// Controladores del administrador
use App\Http\Controllers\Admin\AlumnoController;
use App\Http\Controllers\Admin\ProfesorController;
use App\Http\Controllers\Admin\CarreraController;
use App\Http\Controllers\Admin\GrupoController;
use App\Http\Controllers\Admin\MateriaController;
use App\Http\Controllers\Admin\PeriodoController;
use App\Http\Controllers\Admin\CuatrimestreController;
use App\Http\Controllers\Admin\MatCuatriCarController;
use App\Http\Controllers\Admin\RelacionController;
use App\Http\Controllers\Admin\EvaluacionProfesorController;
use App\Http\Controllers\Admin\PreguntasController;

// Controladores del alumno
use App\Http\Controllers\Alumno\EvaluacionAlumnoController;
use App\Http\Controllers\Alumno\AlumnoMateriaController;

// Modelos utilizados en rutas anónimas
use App\Models\MatCuatriCar;
use App\Models\Profesor;
use App\Models\Periodo;
use App\Models\Materia;
use App\Models\Grupo;
use App\Models\Carrera;
use App\Models\EvaluacionAlumno;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Definición de rutas para el consumo vía HTTP desde frontend o apps.
| Muchas rutas están agrupadas por prefijos para mantener organización.
*/


// Autenticación de alumno y administrador
Route::post('/login/alumno', [LoginAlumno::class, 'login']);
Route::post('/admin/login', [LoginProfesor::class, 'login']);


// Gestión de periodos académicos
Route::prefix('periodos')->group(function () {
    Route::post('/', [PeriodoController::class, 'store']);
    Route::get('/', [PeriodoController::class, 'index']);
    Route::put('/{id}/estado', [PeriodoController::class, 'cambiarEstado']);
    Route::get('/activos', [PeriodoController::class, 'activos']);
});


// Gestión de carreras
Route::prefix('carreras')->group(function () {
    Route::get('/', [CarreraController::class, 'index']);
    Route::post('/', [CarreraController::class, 'store']);
    Route::put('/{id}', [CarreraController::class, 'update']);
    Route::get('/nombres', [CarreraController::class, 'nombres']);

    // Carreras únicas desde mat_cuatri_carr
    Route::get('/unicas', function () {
        return MatCuatriCar::selectRaw('MIN(id_mat_cuatri_car) as id_mat_cuatri_car, carrera_nombre')
            ->groupBy('carrera_nombre')
            ->orderBy('carrera_nombre')
            ->get();
    });

    // Carreras filtradas por periodo
    Route::get('/por-periodo/{num}', [MatCuatriCarController::class, 'carrerasPorPeriodo']);
});


// Obtener materias según carrera
Route::get('/materias/por-carrera/{nombre}', function ($nombre) {
    return MatCuatriCar::where('carrera_nombre', $nombre)
        ->select('id_mat_cuatri_car', 'materia_nombre', 'cuatri_num')
        ->orderBy('cuatri_num')
        ->get();
});


// CRUD de alumnos (administrador)
Route::prefix('alumnos')->group(function () {
    Route::get('/', [AlumnoController::class, 'index']);
    Route::post('/', [AlumnoController::class, 'store']);
    Route::post('/csv', [AlumnoController::class, 'importarDesdeCSV']);
    Route::put('/{id}', [AlumnoController::class, 'update']);
    Route::delete('/{id}', [AlumnoController::class, 'destroy']);
    Route::post('/desactivar', [AlumnoController::class, 'desactivarVarios']);
});


// Evaluación de profesores por alumnos
Route::prefix('alumno')->group(function () {
    // ✅ Ruta actualizada: lista de materias con ID dinámico
    Route::get('/materias', [AlumnoMateriaController::class, 'listaMateriasEval']);

    // Preguntas de evaluación (por tipo de profesor, por ejemplo)
    Route::get('/preguntas', [EvaluacionAlumnoController::class, 'preguntas']);

    // Guardar evaluación enviada por el alumno
    Route::post('/evaluar', [EvaluacionAlumnoController::class, 'store']);

    // Datos de relación para vista de evaluación individual (por profesor)
    Route::get('/evaluacion-datos/{id_relacion}', [EvaluacionAlumnoController::class, 'datosRelacion']);
});

// CRUD de profesores
Route::prefix('profesores')->group(function () {
    Route::get('/', [ProfesorController::class, 'index']);
    Route::post('/', [ProfesorController::class, 'store']);
    Route::put('/{id}', [ProfesorController::class, 'update']);
    Route::post('/csv', [ProfesorController::class, 'importarDesdeCSV']);
    Route::get('/activos', fn() => Profesor::where('status', 'activo')->get());
    Route::get('/{id}', [ProfesorController::class, 'show']);
});


// CRUD de grupos
Route::prefix('grupos')->group(function () {
    Route::get('/', [GrupoController::class, 'index']);
    Route::post('/', [GrupoController::class, 'store']);
    Route::put('/{id}', [GrupoController::class, 'update']);
    Route::post('/csv', [GrupoController::class, 'importarDesdeCSV']);
});


// CRUD de materias
Route::prefix('materias')->group(function () {
    Route::get('/', [MateriaController::class, 'index']);
    Route::post('/', [MateriaController::class, 'store']);
    Route::put('/{id}', [MateriaController::class, 'update']);
    Route::post('/csv', [MateriaController::class, 'importarDesdeCSV']);
    Route::get('/nombres', [MateriaController::class, 'nombres']);
    Route::get('/por-periodo/{num}', [MatCuatriCarController::class, 'materiasPorPeriodo']);
});


// Gestión de cuatrimestres
Route::prefix('cuatrimestres')->group(function () {
    Route::get('/', [CuatrimestreController::class, 'index']);
    Route::post('/', [CuatrimestreController::class, 'store']);
    Route::put('/{id}', [CuatrimestreController::class, 'update']);
    Route::get('/numeros', [CuatrimestreController::class, 'numeros']);
});


// CRUD de MatCuatriCar (materia-cuatrimestre-carrera)
Route::prefix('mat-cuatri-car')->group(function () {
    Route::get('/', [MatCuatriCarController::class, 'index']);
    Route::post('/', [MatCuatriCarController::class, 'store']);
    Route::put('/{id}', [MatCuatriCarController::class, 'update']);
});


// Relaciones entre profesor, materia y grupo
Route::prefix('relaciones')->group(function () {
    Route::get('/', [RelacionController::class, 'index']);
    Route::post('/', [RelacionController::class, 'store']);
    Route::put('/{id}', [RelacionController::class, 'update']);
});


// Selects reutilizables para formularios
Route::prefix('selects')->group(function () {
    Route::get('/profesores', fn() => Profesor::all());
    Route::get('/periodos', fn() => Periodo::all());
    Route::get('/materias', fn() => Materia::all());
    Route::get('/grupos', fn() => Grupo::all());
});


// Evaluación de profesores por administrador
Route::prefix('evaluaciones')->group(function () {
    Route::get('/preguntas-pa', [EvaluacionProfesorController::class, 'preguntasPA']);
    Route::get('/preguntas-ptc', [EvaluacionProfesorController::class, 'preguntasPTC']);
    Route::get('/profesor/{id}', [EvaluacionProfesorController::class, 'getProfesor']);
    Route::post('/', [EvaluacionProfesorController::class, 'store']);
});


// Verificar autenticación del evaluador (protegido con Sanctum)
Route::middleware('auth:sanctum')->get('/evaluador', function () {
    $user = Auth::user();

    if (!$user || $user->rol !== 'administrador') {
        return response()->json(['evaluador' => null]);
    }

    return response()->json([
        'evaluador' => $user->nombre_completo
    ]);
});


// Gestión de preguntas para evaluaciones de alumnos
Route::prefix('preguntas-alumno')->group(function () {
    Route::get('/', [PreguntasController::class, 'index']);
    Route::post('/', [PreguntasController::class, 'store']);
    Route::put('/{id}', [PreguntasController::class, 'update']);
    Route::delete('/{id}', [PreguntasController::class, 'destroy']);
    Route::post('/enviar-evaluacion', [PreguntasController::class, 'guardarEvaluacion']);
});
