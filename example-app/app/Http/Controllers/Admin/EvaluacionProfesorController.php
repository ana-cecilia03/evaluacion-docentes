<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluacionProfesor;       // Modelo de la tabla evaluaciones_profesores
use App\Models\RespuestaEvaluacion;      // Modelo de la tabla respuestas_evaluacion
use App\Models\PreguntaProfesor;         // Modelo de la tabla preguntas_profesores
use App\Models\Profesor;                 // Modelo de la tabla profesores
use Illuminate\Http\Request;

class EvaluacionProfesorController extends Controller
{
    /**
     * ✅ Obtener preguntas tipo PA o generales ("ambos").
     * Se usa para cargar la tabla de evaluación desde el frontend (Vue).
     */
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

    /**
     * ✅ Obtener datos del profesor a evaluar (por su ID).
     * Este método se llama desde Vue cuando se entra a la evaluación.
     */
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

    /**
     * ✅ Guardar evaluación completa en la base de datos.
     * Esta función guarda tanto la evaluación general como las respuestas por pregunta.
     */
    public function store(Request $request)
    {
        // ✳️ Validación de datos de entrada
        $request->validate([
            'profesor_id' => 'required|integer|exists:profesores,id_profesor',
            'tipo' => 'required|in:PA,PTC',
            'periodo' => 'nullable|string|max:100',
            'calif_i' => 'nullable|numeric|min:0|max:10',
            'calif_ii' => 'nullable|numeric|min:0|max:10',
            'calificacion_final' => 'nullable|numeric|min:0|max:10',
            'comentario' => 'nullable|string',

            // ✅ Validación de las respuestas: 
            //    Cada respuesta debe incluir:
            //    - ID de la pregunta
            //    - Calificación entre 1 y 5 (máximo)
            'respuestas' => 'required|array',
            'respuestas.*.pregunta_id' => 'required|integer|exists:preguntas_profesores,id',
            'respuestas.*.calificacion' => 'required|integer|min:1|max:5'
        ]);

        // ✅ Crear una nueva evaluación general
        $evaluacion = EvaluacionProfesor::create([
            'profesor_id' => $request->profesor_id,
            'evaluador_id' => null, // Si tienes autenticación, puedes capturar aquí el evaluador
            'tipo' => $request->tipo,
            'periodo' => $request->periodo,
            'calif_i' => $request->calif_i,
            'calif_ii' => $request->calif_ii,
            'calificacion_final' => $request->calificacion_final,
            'comentario' => $request->comentario,
        ]);

        // ✅ Recorrer todas las respuestas para guardarlas una por una
        foreach ($request->respuestas as $respuesta) {
            RespuestaEvaluacion::create([
                'evaluacion_id' => $evaluacion->id,
                'pregunta_id' => $respuesta['pregunta_id'],
                'calificacion' => $respuesta['calificacion']  // 👈 Aquí se valida que sea de 1 a 5
            ]);
        }

        // ✅ Devolver mensaje de éxito al frontend
        return response()->json(['message' => 'Evaluación guardada correctamente'], 201);
    }

}
