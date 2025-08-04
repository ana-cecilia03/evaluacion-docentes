<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EvaluacionAlumno;
use App\Models\PreguntaAlumno;
use Illuminate\Support\Facades\DB;

class EvaluacionAlumnoController extends Controller
{
    /**
     * Devuelve todas las preguntas de evaluación disponibles para los alumnos.
     */
    public function preguntas()
    {
        return response()->json([
            'preguntas' => PreguntaAlumno::all()
        ]);
    }

    /**
     * Registra las respuestas del alumno a las preguntas de evaluación.
     */
    public function store(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'relacion_id' => 'required|exists:relacions,id_relacion',
            'respuestas' => 'required|array|min:1',
            'respuestas.*.id_pregunta' => 'required|exists:preguntas_alumno,id',
            'respuestas.*.calificacion' => 'required|numeric|min:0|max:10',
        ]);

        $alumnoId = Auth::id();     // ID del alumno autenticado
        $fecha = now();             // Fecha actual

        // Guarda cada respuesta en la base de datos
        foreach ($request->respuestas as $respuesta) {
            EvaluacionAlumno::create([
                'id_alumno' => $alumnoId,
                'relacion_id' => $request->relacion_id,
                'id_pregunta' => $respuesta['id_pregunta'],
                'fecha' => $fecha,
                'calificacion' => $respuesta['calificacion'],
            ]);
        }

        return response()->json(['message' => 'Evaluación registrada correctamente.']);
    }

    /**
     * Devuelve los datos informativos de una relación:
     * profesor, materia, grupo y la fecha actual formateada.
     */
    public function datosRelacion($id_relacion)
    {
        $datos = DB::table('relacions as r')
            ->join('profesores as p', 'r.profesor_id', '=', 'p.id_profesor')
            ->join('mat_cuatri_carr as mcc', 'r.id_mat_cuatri_car', '=', 'mcc.id_mat_cuatri_car')
            ->join('grupos as g', 'r.grupo_id', '=', 'g.id_grupo')
            ->join('periodos as pe', 'r.periodo_id', '=', 'pe.id_periodo')
            ->where('r.id_relacion', $id_relacion)
            ->select(
                'p.nombre_completo as profesor',
                'mcc.materia_nombre as materia',
                'g.nombre as grupo', // Corrige campo del grupo mostrado
                DB::raw('DATE_FORMAT(NOW(), "%d/%m/%Y") as fecha')
            )
            ->first();

        if (!$datos) {
            return response()->json(['error' => 'Relación no encontrada'], 404);
        }

        return response()->json($datos);
    }
}
