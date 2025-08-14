<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Autenticación
use App\Http\Controllers\Auth\LoginAlumno;
use App\Http\Controllers\Auth\LoginProfesor;

// Admin Controllers
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
use App\Http\Controllers\Admin\PuntajeFinalController;
use App\Http\Controllers\Admin\PerfilController;

// Alumno Controllers
use App\Http\Controllers\Alumno\EvaluacionAlumnoController;
use App\Http\Controllers\Alumno\AlumnoMateriaController;

// Modelos usados en cierres simples
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
| Mantén la baseURL de axios en /api desde el frontend.
| Se agregan whereNumber / where para endurecer parámetros.
*/

// ------- Auth (alumno/admin) -------
Route::post('/login/alumno', [LoginAlumno::class, 'login']);
Route::post('/admin/login',  [LoginProfesor::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // Perfil del admin autenticado
    Route::get('/admin/me', function () {
        $u = Auth::user();
        return response()->json([
            'id'     => $u->id_profesor ?? $u->id ?? null,
            'nombre' => $u->nombre_completo ?? null,
            'correo' => $u->correo ?? null,
            'rol'    => $u->rol ?? null,
        ]);
    });

    // Actualizar perfil del admin
    Route::put('/admin/me', [PerfilController::class, 'update']);
});


// ------- Periodos -------
Route::prefix('periodos')->group(function () {
    Route::post('/',                [PeriodoController::class, 'store']);
    Route::get('/',                 [PeriodoController::class, 'index']);
    Route::put('/{id}/estado',      [PeriodoController::class, 'cambiarEstado'])->whereNumber('id');
    Route::get('/activos',          [PeriodoController::class, 'activos']);
});


// ------- Carreras / Materias por carrera -------
Route::prefix('carreras')->group(function () {
    Route::get('/',     [CarreraController::class, 'index']);
    Route::post('/',    [CarreraController::class, 'store']);
    Route::put('/{id}', [CarreraController::class, 'update'])->whereNumber('id');
    Route::get('/nombres', [CarreraController::class, 'nombres']);

    // Carreras únicas desde mat_cuatri_carr
    Route::get('/unicas', function () {
        return MatCuatriCar::selectRaw('MIN(id_mat_cuatri_car) as id_mat_cuatri_car, carrera_nombre')
            ->groupBy('carrera_nombre')
            ->orderBy('carrera_nombre')
            ->get();
    });

    // Carreras filtradas por periodo
    Route::get('/por-periodo/{num}', [MatCuatriCarController::class, 'carrerasPorPeriodo'])->whereNumber('num');
});

// Materias por carrera (consulta simple)
Route::get('/materias/por-carrera/{nombre}', function ($nombre) {
    return MatCuatriCar::where('carrera_nombre', $nombre)
        ->select('id_mat_cuatri_car', 'materia_nombre', 'cuatri_num')
        ->orderBy('cuatri_num')
        ->get();
});


// ------- Alumnos (CRUD) -------
Route::prefix('alumnos')->group(function () {
    Route::get('/',           [AlumnoController::class, 'index']);
    Route::post('/',          [AlumnoController::class, 'store']);
    Route::post('/csv',       [AlumnoController::class, 'importarDesdeCSV']);
    Route::put('/{id}',       [AlumnoController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}',    [AlumnoController::class, 'destroy'])->whereNumber('id');
    Route::post('/desactivar',[AlumnoController::class, 'desactivarVarios']);
});


// ------- Evaluación de profesores por alumnos (restringido) -------
// ================== PROTEGIDAS (token Sanctum) ==================
Route::middleware('auth:sanctum')->prefix('alumno')->group(function () {
    Route::get('/materias', [AlumnoMateriaController::class, 'listaMateriasEval']);
    Route::get('/preguntas', [EvaluacionAlumnoController::class, 'preguntas']);
    Route::post('/evaluar',  [EvaluacionAlumnoController::class, 'store']);
    Route::get('/evaluacion-datos/{id_relacion}', [EvaluacionAlumnoController::class, 'datosRelacion'])
        ->whereNumber('id_relacion');
});

// ================== PÚBLICAS TEMPORALES (HOTFIX) ==================
// ⚠️ Quitar cuando estabilices el flujo con token.
Route::prefix('alumno')->group(function () {
    // Materias con fallback ?id=
    Route::get('/materias-public', [AlumnoMateriaController::class, 'listaMateriasEvalPublic']);

    // Si tus métodos NO dependen de Auth::user(), puedes reutilizarlos así:
    Route::get('/preguntas-public', [EvaluacionAlumnoController::class, 'preguntas']);
    Route::get('/evaluacion-datos-public/{id_relacion}', [EvaluacionAlumnoController::class, 'datosRelacion'])
        ->whereNumber('id_relacion');

    // Si alguno usa Auth::user(), crea versiones ...Public en su controlador y apunta aquí.
});

// ------- Profesores (CRUD) -------
Route::prefix('profesores')->group(function () {
    Route::get('/',           [ProfesorController::class, 'index']);
    Route::post('/',          [ProfesorController::class, 'store']);
    Route::put('/{id}',       [ProfesorController::class, 'update'])->whereNumber('id');
    Route::post('/csv',       [ProfesorController::class, 'importarDesdeCSV']);

    // Público porque tu listado lo llama sin auth
    Route::get('/activos',    [ProfesorController::class, 'activos']);

    Route::get('/{id}',       [ProfesorController::class, 'show'])->whereNumber('id');
});


// ------- Grupos (CRUD) -------
Route::prefix('grupos')->group(function () {
    Route::get('/',       [GrupoController::class, 'index']);
    Route::post('/',      [GrupoController::class, 'store']);
    Route::put('/{id}',   [GrupoController::class, 'update'])->whereNumber('id');
    Route::post('/csv',   [GrupoController::class, 'importarDesdeCSV']);
});


// ------- Materias (CRUD) -------
Route::prefix('materias')->group(function () {
    Route::get('/',       [MateriaController::class, 'index']);
    Route::post('/',      [MateriaController::class, 'store']);
    Route::put('/{id}',   [MateriaController::class, 'update'])->whereNumber('id');
    Route::post('/csv',   [MateriaController::class, 'importarDesdeCSV']);
    Route::get('/nombres', [MateriaController::class, 'nombres']);
    Route::get('/por-periodo/{num}', [MatCuatriCarController::class, 'materiasPorPeriodo'])->whereNumber('num');
});


// ------- Cuatrimestres -------
Route::prefix('cuatrimestres')->group(function () {
    Route::get('/',     [CuatrimestreController::class, 'index']);
    Route::post('/',    [CuatrimestreController::class, 'store']);
    Route::put('/{id}', [CuatrimestreController::class, 'update'])->whereNumber('id');
    Route::get('/numeros', [CuatrimestreController::class, 'numeros']);
});


// ------- MatCuatriCar -------
Route::prefix('mat-cuatri-car')->group(function () {
    Route::get('/',   [MatCuatriCarController::class, 'index']);
    Route::post('/',  [MatCuatriCarController::class, 'store']);
    Route::put('/{id}', [MatCuatriCarController::class, 'update'])->whereNumber('id');
});


// ------- Relaciones (profesor-materia-grupo) -------
Route::prefix('relaciones')->group(function () {
    Route::get('/',     [RelacionController::class, 'index']);
    Route::post('/',    [RelacionController::class, 'store']);
    Route::put('/{id}', [RelacionController::class, 'update'])->whereNumber('id');
});


// ------- Selects reutilizables -------
Route::prefix('selects')->group(function () {
    Route::get('/profesores', fn() => Profesor::all());
    Route::get('/periodos',  fn() => Periodo::all());
    Route::get('/materias',  fn() => Materia::all());
    Route::get('/grupos',    fn() => Grupo::all());
});


// ------- Evaluación por administrador (PA/PTC) - protegido -------
Route::middleware('auth:sanctum')->prefix('evaluaciones')->group(function () {
    // Preguntas por tipo de profesor
    Route::get('/preguntas-pa',  [EvaluacionProfesorController::class, 'preguntasPA']);
    Route::get('/preguntas-ptc', [EvaluacionProfesorController::class, 'preguntasPTC']);

    // Datos de profesor para cabecera
    Route::get('/profesor/{id}', [EvaluacionProfesorController::class, 'getProfesor'])->whereNumber('id');

    // Guardar evaluación de admin
    Route::post('/',             [EvaluacionProfesorController::class, 'store']);

    // Estado "ya evaluado" en periodo/tipo (usado por vistas y listado)
    Route::get('/estado',        [EvaluacionProfesorController::class, 'estado']);

    // Opcional: ver detalle por tipo/id
    Route::get('/{tipo}/{profesor_id}', [EvaluacionProfesorController::class, 'show'])
        ->where(['tipo' => 'PA|PTC', 'profesor_id' => '[0-9]+']);
});


// ------- Verificar evaluador (admin) - protegido -------
Route::middleware('auth:sanctum')->get('/evaluador', function () {
    $user = Auth::user();
    if (!$user || $user->rol !== 'administrador') {
        return response()->json(['evaluador' => null]);
    }
    return response()->json(['evaluador' => $user->nombre_completo]);
});


// ------- Preguntas para evaluaciones de alumnos (ABM) -------
Route::prefix('preguntas-alumno')->group(function () {
    Route::get('/',     [PreguntasController::class, 'index']);
    Route::post('/',    [PreguntasController::class, 'store']);
    Route::put('/{id}', [PreguntasController::class, 'update'])->whereNumber('id');
    Route::delete('/{id}', [PreguntasController::class, 'destroy'])->whereNumber('id');

    // Guardar evaluación de alumnos (si lo usas)
    Route::post('/enviar-evaluacion', [PreguntasController::class, 'guardarEvaluacion']);
});


// ------- Reporte: Puntaje Final (promedio alumnos + calif admin) -------
// MODO PRUEBA (público) — lo usa tu frontend para rellenar "Calificación II Estudiantes".
Route::prefix('admin')->group(function () {
    Route::get('/reportes/puntaje-final',              [PuntajeFinalController::class, 'index']); // opcional
    Route::get('/reportes/puntaje-final/{profesorId}', [PuntajeFinalController::class, 'show'])
        ->whereNumber('profesorId');
});

// MODO PRODUCCIÓN (protegido) — cuando quieras cerrar acceso, usa este bloque y comenta el de arriba.
/*
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/reportes/puntaje-final',              [PuntajeFinalController::class, 'index']);
    Route::get('/reportes/puntaje-final/{profesorId}', [PuntajeFinalController::class, 'show'])
        ->whereNumber('profesorId');
});
*/
