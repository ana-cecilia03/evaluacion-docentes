<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionProfesor;   // tabla: evaluaciones_profesores (ajusta si tu nombre difiere)
use App\Models\RespuestaEvaluacion;  // tabla: respuestas_evaluacion
use App\Models\PreguntaProfesor;     // tabla: preguntas_profesores
use App\Models\Profesor;             // tabla: profesores
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EvaluacionProfesorController extends Controller
{
    /**
     * Obtener preguntas tipo PA (y las marcadas como "ambos").
     */
    public function preguntasPA()
    {
        return PreguntaProfesor::whereIn('tipo', ['PA', 'ambos'])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    /**
     * Obtener preguntas tipo PTC (y las marcadas como "ambos").
     */
    public function preguntasPTC()
    {
        return PreguntaProfesor::whereIn('tipo', ['PTC', 'ambos'])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    /**
     * Obtener datos del profesor a evaluar por ID.
     */
    public function getProfesor($id)
    {
        $profesor = Profesor::find($id);

        if (!$profesor) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        return response()->json([
            'id'              => $profesor->id_profesor,
            'nombre_completo' => $profesor->nombre_completo,
            'cargo'           => $profesor->cargo,
            'correo'          => $profesor->correo,
        ]);
    }

    /**
     * Guardar evaluación y sus respuestas.
     * Ruta recomendada (protegida): POST /api/evaluaciones  (auth:sanctum)
     */
    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'profesor_id'         => 'required|integer|exists:profesores,id_profesor',
            'tipo'                => 'required|in:PA,PTC',
            'periodo'             => 'nullable|string|max:100',
            'calif_i'             => 'nullable|numeric|min:0|max:10',
            'calif_ii'            => 'nullable|numeric|min:0|max:10',
            'calificacion_final'  => 'nullable|numeric|min:0|max:10',
            'comentario'          => 'nullable|string',
            'respuestas'                  => 'required|array|min:1',
            'respuestas.*.pregunta_id'    => 'required|integer|exists:preguntas_profesores,id',
            'respuestas.*.calificacion'   => 'required|numeric|min:1|max:5',
        ]);

        // Crear evaluación (el evaluador se toma del usuario autenticado por token)
        $evaluacion = EvaluacionProfesor::create([
            'profesor_id'        => $validated['profesor_id'],
            'tipo'               => $validated['tipo'],
            'periodo'            => $validated['periodo'] ?? null,
            'calif_i'            => $validated['calif_i'] ?? null,
            'calif_ii'           => $validated['calif_ii'] ?? null,
            'calificacion_final' => $validated['calificacion_final'] ?? null,
            'comentario'         => $validated['comentario'] ?? null,
            'evaluador_id'       => Auth::id(), // <-- clave: evaluador autenticado
        ]);

        // Guardar respuestas
        foreach ($validated['respuestas'] as $r) {
            RespuestaEvaluacion::create([
                'evaluacion_id' => $evaluacion->id,       // ajusta si tu PK no es "id"
                'pregunta_id'   => $r['pregunta_id'],
                'calificacion'  => $r['calificacion'],
            ]);
        }

        return response()->json([
            'message'       => 'Evaluación guardada correctamente',
            'evaluacion_id' => $evaluacion->id,
        ], 201);
    }
}
