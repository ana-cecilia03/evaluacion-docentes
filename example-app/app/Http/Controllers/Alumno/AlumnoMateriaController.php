<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;

class AlumnoMateriaController extends Controller
{
    /**
     * Devuelve la lista de materias activas y sus profesores
     * asignadas al grupo del alumno para que pueda evaluarlos.
     */
    public function listaMateriasEval()
    {
        // Simula un alumno fijo mientras no se usa autenticación
        $alumno = Alumno::find(16); // Cambiar por Auth::id() cuando haya login

        if (!$alumno) {
            return response()->json(['error' => 'Alumno no encontrado'], 404);
        }

        $grupoId = $alumno->grupo; // ID del grupo al que pertenece el alumno

        // Consulta relaciones activas de profesor-materia-grupo en el periodo activo
        $materias = DB::table('relacions as r')
            ->join('profesores as p', 'r.profesor_id', '=', 'p.id_profesor')
            ->join('mat_cuatri_carr as mcc', 'r.id_mat_cuatri_car', '=', 'mcc.id_mat_cuatri_car')
            ->join('periodos as pe', 'r.periodo_id', '=', 'pe.id_periodo')
            ->where('r.grupo_id', $grupoId)
            ->where('pe.estado', 'activo')
            ->select(
                'r.id_relacion as relacion_id',
                'mcc.materia_nombre',
                'p.nombre_completo as profesor_nombre'
            )
            ->get();

        return response()->json(['materias' => $materias]);
    }
}
