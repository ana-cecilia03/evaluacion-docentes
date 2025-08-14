<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\EvaluacionAlumno1;

class AlumnoMateriaController extends Controller
{
    /**
     * PROTEGIDA (token Sanctum):
     * Lista materias/profesores para el alumno autenticado.
     * Front debe llamar: GET /api/alumno/materias  (SIN ?id)
     */
    public function listaMateriasEval(Request $request)
    {
        $alumno = $request->user(); // auth()->user() también sirve
        if (!$alumno) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $alumnoId = $alumno->id_alumno ?? $alumno->id ?? null;
        $grupoId  = $alumno->grupo_id ?? $alumno->grupo ?? null;

        if (!$alumnoId || !$grupoId) {
            return response()->json(['message' => 'Alumno sin grupo o ID inválido'], 409);
        }

        $sub = EvaluacionAlumno1::select('relacion_id')
            ->where('id_alumno', $alumnoId);

        $materias = DB::table('relacions as r')
            ->join('profesores as p', 'r.profesor_id', '=', 'p.id_profesor')
            ->join('mat_cuatri_carr as mcc', 'r.id_mat_cuatri_car', '=', 'mcc.id_mat_cuatri_car')
            ->join('periodos as pe', 'r.periodo_id', '=', 'pe.id_periodo')
            ->where('r.grupo_id', $grupoId)
            ->where('pe.estado', 'activo')
            ->leftJoinSub($sub, 'ea', function ($join) {
                $join->on('ea.relacion_id', '=', 'r.id_relacion');
            })
            ->select(
                'r.id_relacion as relacion_id',
                'r.grupo_id',
                'mcc.materia_nombre',
                'p.nombre_completo as profesor_nombre',
                DB::raw('CASE WHEN ea.relacion_id IS NULL THEN FALSE ELSE TRUE END AS evaluado')
            )
            ->orderBy('mcc.materia_nombre')
            ->get();

        return response()->json(['materias' => $materias]);
    }

    /**
     * PÚBLICA (hotfix de emergencia):
     * Lista materias usando ?id=<id_alumno> SIN token.
     * Front temporal: GET /api/alumno/materias-public?id=123
     *
     * TODO: eliminar cuando el front use siempre el endpoint protegido.
     */
    public function listaMateriasEvalPublic(Request $request)
    {
        $idAlumno = $request->query('id');
        if (!$idAlumno) {
            return response()->json(['message' => 'Falta id de alumno'], 422);
        }

        $alumno = Alumno::find($idAlumno);
        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado'], 404);
        }

        $alumnoId = $alumno->id_alumno ?? $alumno->id ?? null;
        $grupoId  = $alumno->grupo_id ?? $alumno->grupo ?? null;

        if (!$alumnoId || !$grupoId) {
            return response()->json(['message' => 'El alumno no tiene grupo asignado'], 409);
        }

        $sub = EvaluacionAlumno1::select('relacion_id')
            ->where('id_alumno', $alumnoId);

        $materias = DB::table('relacions as r')
            ->join('profesores as p', 'r.profesor_id', '=', 'p.id_profesor')
            ->join('mat_cuatri_carr as mcc', 'r.id_mat_cuatri_car', '=', 'mcc.id_mat_cuatri_car')
            ->join('periodos as pe', 'r.periodo_id', '=', 'pe.id_periodo')
            ->where('r.grupo_id', $grupoId)
            ->where('pe.estado', 'activo')
            ->leftJoinSub($sub, 'ea', function ($join) {
                $join->on('ea.relacion_id', '=', 'r.id_relacion');
            })
            ->select(
                'r.id_relacion as relacion_id',
                'r.grupo_id',
                'mcc.materia_nombre',
                'p.nombre_completo as profesor_nombre',
                DB::raw('CASE WHEN ea.relacion_id IS NULL THEN FALSE ELSE TRUE END AS evaluado')
            )
            ->orderBy('mcc.materia_nombre')
            ->get();

        return response()->json(['materias' => $materias]);
    }
}
