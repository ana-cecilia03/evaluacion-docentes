<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\EvaluacionAlumno1;

class AlumnoMateriaController extends Controller
{
    /**
     * Devuelve la lista de materias activas y sus profesores
     * asignadas al grupo del alumno para que pueda evaluarlos.
     */
    public function listaMateriasEval()
    {
    // Alumno simulado (ajusta cuando tengas login)
    $alumno = \App\Models\Alumno::find(16);
    if (!$alumno) {
        return response()->json(['error' => 'Alumno no encontrado'], 404);
    }

    // Asegúrate de que este campo sea el ID del grupo del alumno
    $grupoId = $alumno->grupo;

    // Subquery: relaciones ya evaluadas por este alumno
    $sub = \App\Models\EvaluacionAlumno1::select('relacion_id')
        ->where('id_alumno', $alumno->id_alumno);

    $materias = \DB::table('relacions as r')
        ->join('profesores as p', 'r.profesor_id', '=', 'p.id_profesor')
        ->join('mat_cuatri_carr as mcc', 'r.id_mat_cuatri_car', '=', 'mcc.id_mat_cuatri_car')
        ->join('periodos as pe', 'r.periodo_id', '=', 'pe.id_periodo')
        // FILTRAR por el grupo del alumno
        ->where('r.grupo_id', $grupoId)
        // (opcional) solo periodo activo
        ->where('pe.estado', 'activo')
        // marcar evaluadas
        ->leftJoinSub($sub, 'ea', function ($join) {
            $join->on('ea.relacion_id', '=', 'r.id_relacion');
        })
        ->select(
            'r.id_relacion as relacion_id',
            'r.grupo_id', // útil para depurar
            'mcc.materia_nombre',
            'p.nombre_completo as profesor_nombre',
            \DB::raw('CASE WHEN ea.relacion_id IS NULL THEN 0 ELSE 1 END AS evaluado')
        )
        ->orderBy('mcc.materia_nombre')
        ->get();

    return response()->json(['materias' => $materias]);
    }
}
