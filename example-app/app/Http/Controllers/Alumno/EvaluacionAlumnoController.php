<?php

namespace App\Http\Controllers\Alumno;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Modelos
use App\Models\EvaluacionAlumno1;
use App\Models\RespuestaEvaluacionalum;
use App\Models\PreguntaAlumno;
use App\Models\Alumno;

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
     * Registra la evaluación de un alumno, incluyendo respuestas numéricas y comentarios.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_alumno'   => 'required|exists:alumnos,id_alumno',
            'relacion_id' => 'required|exists:relacions,id_relacion',
            'respuestas'  => 'required|array|min:1',
            'respuestas.*.id_pregunta'  => 'required|exists:preguntas_alumno,id',
            'respuestas.*.calificacion' => 'required|numeric|min:0|max:10',
            'comentarios' => 'nullable|array',
            'comentarios.*.id_pregunta' => 'required|exists:preguntas_alumno,id',
            'comentarios.*.texto'       => 'nullable|string|max:1000'
        ]);

        // Obtener el alumno desde la base de datos
        $alumno = Alumno::find($request->input('id_alumno'));

        if (!$alumno) {
            return response()->json(['message' => 'Alumno no encontrado.'], 404);
        }

        // 1) Bloquear duplicados
        $yaEvaluo = EvaluacionAlumno1::where('id_alumno', $alumno->id_alumno)
            ->where('relacion_id', $request->relacion_id)
            ->exists();

        if ($yaEvaluo) {
            return response()->json(['message' => 'Ya evaluaste a este profesor.'], 409);
        }

        // 2) Guardar evaluación y respuestas
        DB::transaction(function () use ($request, $alumno) {
            $evaluacion = EvaluacionAlumno1::create([
                'id_alumno'   => $alumno->id_alumno,
                'relacion_id' => $request->relacion_id,
                'fecha'       => now(),
            ]);

            // Guardar respuestas numéricas
            foreach ($request->respuestas as $r) {
                RespuestaEvaluacionalum::create([
                    'id_evaluacion' => $evaluacion->id_evaluacion,
                    'id_pregunta'   => $r['id_pregunta'],
                    'calificacion'  => $r['calificacion'],
                ]);
            }

            // Guardar comentarios (si tienes columna 'comentario', úsala)
            if (!empty($request->comentarios)) {
                foreach ($request->comentarios as $c) {
                    if (!empty($c['texto'])) {
                        RespuestaEvaluacionalum::create([
                            'id_evaluacion' => $evaluacion->id_evaluacion,
                            'id_pregunta'   => $c['id_pregunta'],
                            'comentario'    => $c['texto'], // ← Usa esta si tu tabla tiene esa columna
                        ]);
                    }
                }
            }
        });

        return response()->json(['message' => 'Evaluación registrada correctamente.']);
    }

    /**
     * Devuelve los datos informativos de una relación para mostrar al alumno.
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
                'g.nombre as grupo',
                DB::raw('DATE_FORMAT(NOW(), "%d/%m/%Y") as fecha')
            )
            ->first();

        if (!$datos) {
            return response()->json(['error' => 'Relación no encontrada.'], 404);
        }

        return response()->json($datos);
    }
}
