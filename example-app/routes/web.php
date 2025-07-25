<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
#ruta raiz
Route::get('/', function () {
    return redirect('/bienvenida');
});

#VISTA DE PRUEBA
Route::get('/home', function () {
    return Inertia::render('Home');
})->name('home');

#Bienvenida.vue
Route::get('/bienvenida', function () {
    return Inertia::render('auth/Bienvenida');
});

#inicio
Route::get('/inicio', function () {
    return Inertia::render('auth/Inicio');
});

#Login
Route::get('/login/alumno', fn () => Inertia::render('Alumno/LoginAlumno'))->name('login.alumno');
Route::get('/admin-control', fn () => Inertia::render('Admin/LoginAdmin'))->name('login.admin');
#inicioAlumno
Route::get('/inicio/alumno', fn () => Inertia::render('Alumno/InicioAlumno'))->name('inicio.alumno');

#DashboardAdmin
Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->name('admin.dashboard');

#Lista de materias donde el alumno evalua al profesor
Route::get('/alumno/materias', function () {
    return Inertia::render('Alumno/ListaMaterias');
})->name('alumno.materias');

#Lista donde se gestionan los profes que se van a evaluar
Route::get('/admin/usuarios', function () {
    return Inertia::render('Admin/Usuarios');
})->name('admin.usuarios');

#Configuracion del administrador
Route::get('/admin/configuracion', function () {
    return Inertia::render('Admin/Configuracion');
})->name('admin.configuracion');

// Página de activación de encuesta
Route::get('/admin/periodo', function () {
    return Inertia::render('Admin/Periodo');
})->name('admin.periodo');

#Vista de registros de carrera
Route::get('/registro/carrera', function () {
    return Inertia::render('Admin/Carreras');
})->name('registro.carrera');

#Vista de registros de alumnos
Route::get('/registro/alumnos', function () {
    return Inertia::render('Admin/AlumnoRegistro');
})->name('registro.alumnos');

#Vista para registrar asignaturas
Route::get('/registro/asignaturas', function () {
    return Inertia::render('Admin/AsignaturaRegistro');
})->name('registro.asignaturas');

#Vista para registrar grupos
Route::get('/registro/grupo', function () {
    return Inertia::render('Admin/GrupoRegistro');
})->name('registro.grupos');

#Vista para registrar profesores
Route::get('/registro/profesores', function () {
    return Inertia::render('Admin/ProfesoresRegistro');
})->name('registro.profesores');


###########################################
#
Route::get('/reportes/profesores', function () {
    return Inertia::render('Admin/ReportesProfesores');
})->name('reporte.profesores');

#
Route::get('/encuesta', function () {
    return Inertia::render('Admin/Evaluacion');
})->name('evaluacion.alumnos');

#
Route::get('/reportes/materias', function () {
    return Inertia::render('Admin/ReportesMaterias');
})->name('reporte.materias');

#
Route::get('/reporte/grupos', function () {
    return Inertia::render('Admin/ReportesGrupos');
})->name('reporte.grupos');

#
Route::get('/relacion/materia-periodo-carrera', function () {
    return Inertia::render('Admin/MateriaPeriodoCarrera');
})->name('relacion.materia-periodo-carrera');

#
Route::get('/relacion/profesor-materia', function () {
    return Inertia::render('Admin/ProfeMateria');
})->name('relacion.profesor-materia');

#
Route::get('/estadisticas', function () {
    return Inertia::render('Admin/Estadisticas');
})->name('admin.estadisticas');

################################
#app
Route::get('/apps', function () {
    return Inertia::render('auth/app');
});

#contacto
Route::get('/contacto', function () {
    return Inertia::render('auth/contacto');
});

#evaluacion Alumno
Route::get('/evaluacion', function () {
    return Inertia::render('Alumno/EvaluacionAlumno');
});

#evaluacion profesores
Route::get('/evaluacionProfesorPTC', function () {
    return Inertia::render('Admin/EvaluacionProfesorPTC');
});

#evaluacion profesores
Route::get('/evaluacionProfesorPA', function () {
    return Inertia::render('Admin/EvaluacionProfesorPA');
});

#
Route::get('/relacion/cuatri', function () {
    return Inertia::render('Admin/numCuatri');
})->name('relacion.numCuatri');

