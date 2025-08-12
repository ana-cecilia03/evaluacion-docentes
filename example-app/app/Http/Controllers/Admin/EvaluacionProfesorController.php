<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionProfesor;
use App\Models\RespuestaEvaluacion;
use App\Models\PreguntaProfesor;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EvaluacionProfesorController extends Controller
{
    public function preguntasPA()
    {
        return PreguntaProfesor::whereIn('tipo', ['PA', 'ambos'])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    public function preguntasPTC()
    {
        return PreguntaProfesor::whereIn('tipo', ['PTC', 'ambos'])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

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

    public function estado(Request $request)
    {
        $validated = $request->validate([
            'profesor_id' => ['required', 'integer', 'exists:profesores,id_profesor'],
            'tipo'        => ['required', Rule::in(['PA','PTC'])],
            'periodo'     => ['required', 'string', 'max:100'],
        ]);

        $evaluacion = EvaluacionProfesor::where('profesor_id', $validated['profesor_id'])
            ->where('tipo', $validated['tipo'])
            ->where('periodo', $validated['periodo'])
            ->first();

        return response()->json([
            'evaluado'           => (bool) $evaluacion,
            'evaluacion_id'      => $evaluacion->id ?? null,
            'calif_i'            => $evaluacion->calif_i ?? null,
            'calif_ii'           => $evaluacion->calif_ii ?? null,
            'calificacion_final' => $evaluacion->calificacion_final ?? null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'profesor_id'        => 'required|integer|exists:profesores,id_profesor',
            'tipo'               => 'required|in:PA,PTC',
            'periodo'            => 'nullable|string|max:100',
            'calif_i'            => 'nullable|numeric|min:0|max:10',
            'calif_ii'           => 'nullable|numeric|min:0|max:10',
            'calificacion_final' => 'nullable|numeric|min:0|max:10',
            'comentario'         => 'nullable|string',
            'respuestas'                  => 'required|array|min:1',
            'respuestas.*.pregunta_id'    => 'required|integer|exists:preguntas_profesores,id',
            'respuestas.*.calificacion'   => 'required|numeric|min:1|max:5',
        ]);

        $existe = EvaluacionProfesor::where('profesor_id', $validated['profesor_id'])
            ->where('tipo', $validated['tipo'])
            ->where('periodo', $validated['periodo'])
            ->exists();

        if ($existe) {
            return response()->json([
                'message' => 'Ya existe una evaluación para este profesor, tipo y periodo.'
            ], 409);
        }

        return DB::transaction(function () use ($validated) {
            $evaluacion = EvaluacionProfesor::create([
                'profesor_id'        => $validated['profesor_id'],
                'tipo'               => $validated['tipo'],
                'periodo'            => $validated['periodo'] ?? null,
                'calif_i'            => $validated['calif_i'] ?? null,
                'calif_ii'           => $validated['calif_ii'] ?? null,
                'calificacion_final' => $validated['calificacion_final'] ?? null,
                'comentario'         => $validated['comentario'] ?? null,
                'evaluador_id'       => Auth::id(),
            ]);

            foreach ($validated['respuestas'] as $r) {
                RespuestaEvaluacion::create([
                    'evaluacion_id' => $evaluacion->id,
                    'pregunta_id'   => $r['pregunta_id'],
                    'calificacion'  => $r['calificacion'],
                ]);
            }

            return response()->json([
                'message'       => 'Evaluación guardada correctamente',
                'evaluacion_id' => $evaluacion->id,
            ], 201);
        });
    }
}
