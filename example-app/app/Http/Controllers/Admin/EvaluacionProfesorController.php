<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionProfesor;
use App\Models\RespuestaEvaluacion;
use App\Models\PreguntaProfesor;
use App\Models\Profesor;
use Illuminate\Http\Request;

class EvaluacionProfesorController extends Controller
{
    // Obtener preguntas para tipo PA
    public function preguntasPA()
    {
        return PreguntaProfesor::whereIn('tipo', ['PA', 'ambos'])
            ->where('activo', true)
            ->orderBy('orden')
            ->get();
    }

    // Obtener los datos de un profesor por ID
    public function getProfesor($id)
    {
        $profesor = Profesor::find($id);

        if (!$profesor) {
            return response()->json(['error' => 'Profesor no encontrado'], 404);
        }

        return response()->json([
            'id' => $profesor->id_profesor,
            'nombre_completo' => $profesor->nombre_completo,
            'cargo' => $profesor->cargo,
            'correo' => $profesor->correo
        ]);
    }

    // Guardar evaluación completa
    public function store(Request $request)
    {
        $request->validate([
            'profesor_id' => 'required|integer|exists:profesores,id_profesor',
            'tipo' => 'required|in:PA,PTC',
            'periodo' => 'nullable|string|max:100',
            'calif_i' => 'nullable|numeric|min:0|max:10',
            'calif_ii' => 'nullable|numeric|min:0|max:10',
            'calificacion_final' => 'nullable|numeric|min:0|max:10',
            'comentario' => 'nullable|string',
            'respuestas' => 'required|array',
            'respuestas.*.pregunta_id' => 'required|integer|exists:preguntas_profesores,id',
            'respuestas.*.calificacion' => 'required|integer|min:1|max:5'
        ]);

        $evaluacion = EvaluacionProfesor::create([
            'profesor_id' => $request->profesor_id,
            'evaluador_id' => null, // puedes capturar el usuario autenticado si aplica
            'tipo' => $request->tipo,
            'periodo' => $request->periodo,
            'calif_i' => $request->calif_i,
            'calif_ii' => $request->calif_ii,
            'calificacion_final' => $request->calificacion_final,
            'comentario' => $request->comentario,
        ]);

        foreach ($request->respuestas as $respuesta) {
            RespuestaEvaluacion::create([
                'evaluacion_id' => $evaluacion->id,
                'pregunta_id' => $respuesta['pregunta_id'],
                'calificacion' => $respuesta['calificacion']
            ]);
        }

        return response()->json(['message' => 'Evaluación guardada correctamente'], 201);
    }
}
