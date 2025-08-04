<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Ruta raíz redirige a la pantalla de bienvenida
Route::get('/', fn () => redirect('/bienvenida'));

// Vista de prueba (pantalla genérica)
Route::get('/home', fn () => Inertia::render('Home'))->name('home');

// Pantalla de bienvenida para todos los usuarios
Route::get('/bienvenida', fn () => Inertia::render('auth/Bienvenida'));

// Vista de inicio común (posiblemente selector de rol)
Route::get('/inicio', fn () => Inertia::render('auth/Inicio'));

// Login para alumnos y administradores
Route::get('/login/alumno', fn () => Inertia::render('Alumno/LoginAlumno'))->name('login.alumno');
Route::get('/admin-control', fn () => Inertia::render('Admin/LoginAdmin'))->name('login.admin');

// Vista inicial tras login de alumno
Route::get('/inicio/alumno', fn () => Inertia::render('Alumno/InicioAlumno'))->name('inicio.alumno');

// Dashboard principal del administrador
Route::get('/admin/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('admin.dashboard');

// Lista de materias disponibles para evaluación (alumno)
Route::get('/alumno/materias', fn () => Inertia::render('Alumno/ListaMaterias'))->name('alumno.materias');

// Gestión de profesores evaluables (admin)
Route::get('/admin/usuarios', fn () => Inertia::render('Admin/Usuarios'))->name('admin.usuarios');

// Configuración de perfil u opciones generales (admin)
Route::get('/admin/configuracion', fn () => Inertia::render('Admin/Configuracion'))->name('admin.configuracion');

// Activación o gestión de encuestas por periodo (admin)
Route::get('/admin/periodo', fn () => Inertia::render('Admin/Periodo'))->name('admin.periodo');

// Registro y gestión de carreras
Route::get('/registro/carrera', fn () => Inertia::render('Admin/Carreras'))->name('registro.carrera');

// Registro de alumnos
Route::get('/registro/alumnos', fn () => Inertia::render('Admin/AlumnoRegistro'))->name('registro.alumnos');

// Registro de asignaturas
Route::get('/registro/asignaturas', fn () => Inertia::render('Admin/AsignaturaRegistro'))->name('registro.asignaturas');

// Registro de grupos
Route::get('/registro/grupo', fn () => Inertia::render('Admin/GrupoRegistro'))->name('registro.grupos');

// Registro de profesores
Route::get('/registro/profesores', fn () => Inertia::render('Admin/ProfesoresRegistro'))->name('registro.profesores');

// Reporte de evaluación de profesores
Route::get('/reportes/profesores', fn () => Inertia::render('Admin/ReportesProfesores'))->name('reporte.profesores');

// Encuesta general (vista admin)
Route::get('/encuesta', fn () => Inertia::render('Admin/Evaluacion'))->name('evaluacion.alumnos');

// Reporte de evaluación por materias
Route::get('/reportes/materias', fn () => Inertia::render('Admin/ReportesMaterias'))->name('reporte.materias');

// Reporte por grupos
Route::get('/reporte/grupos', fn () => Inertia::render('Admin/ReportesGrupos'))->name('reporte.grupos');

// Gestión de relaciones entre materia, periodo y carrera
Route::get('/relacion/materia-periodo-carrera', fn () => Inertia::render('Admin/MateriaPeriodoCarrera'))->name('relacion.materia-periodo-carrera');

// Asignación profesor-materia
Route::get('/relacion/profesor-materia', fn () => Inertia::render('Admin/ProfeMateria'))->name('relacion.profesor-materia');

// Estadísticas generales de evaluación (admin)
Route::get('/estadisticas', fn () => Inertia::render('Admin/Estadisticas'))->name('admin.estadisticas');

// Aplicaciones (posiblemente de prueba)
Route::get('/apps', fn () => Inertia::render('auth/app'));

// Vista de contacto
Route::get('/contacto', fn () => Inertia::render('auth/contacto'));

// Vista de evaluación del alumno (acceso directo)
Route::get('/evaluacion', fn () => Inertia::render('Alumno/EvaluacionAlumno'));

// Evaluación de profesor con cargo PTC (con ID)
Route::get('/evaluacionProfesorPTC/{id}', fn ($id) => Inertia::render('Admin/EvaluacionProfesorPTC', ['id' => $id]));

// Evaluación de profesor con cargo PA (con ID)
Route::get('/evaluacionProfesorPA/{id}', fn ($id) => Inertia::render('Admin/EvaluacionProfesorPA', ['id' => $id]));

// Evaluación de profesor por parte del alumno (usando id_relacion)
Route::get('/alumno/evaluar/{id_relacion}', fn ($id_relacion) => Inertia::render('Alumno/EvaluacionAlumno', ['id_relacion' => $id_relacion]))
    ->name('evaluacion.alumno');

// Asignación del número de cuatrimestre a relaciones
Route::get('/relacion/cuatri', fn () => Inertia::render('Admin/numCuatri'))->name('relacion.numCuatri');

// Gestión de preguntas de evaluación (admin)
Route::get('/preguntas', fn () => Inertia::render('Admin/Preguntas'))->name('admin.preguntas');
