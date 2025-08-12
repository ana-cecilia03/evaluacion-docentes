<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Models\EvaluacionAlumno1;
use App\Models\EvaluacionProfesor;

class PuntajeFinalController extends Controller
{
    public function show(int $profesorId): JsonResponse
    {
        $promedioAlumnos   = EvaluacionAlumno1::promedioPorProfesor($profesorId);
        $calificacionAdmin = EvaluacionProfesor::calificacionAdminPorProfesor($profesorId);

        $total = (is_null($promedioAlumnos) || is_null($calificacionAdmin))
            ? null
            : round((($promedioAlumnos + $calificacionAdmin) / 2), 2);

        return response()->json([
            'profesor_id'        => $profesorId,
            'promedio_alumnos'   => $promedioAlumnos,
            'calificacion_admin' => $calificacionAdmin,
            'total'              => $total,
        ]);
    }
}
