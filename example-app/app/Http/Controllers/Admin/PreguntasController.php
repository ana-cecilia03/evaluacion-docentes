<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\PreguntaAlumno;

class PreguntasController extends Controller
{
    // Obtener todas las preguntas (para frontend)
    public function index()
    {
        $preguntas = PreguntaAlumno::all();
   
        return response()->json([
            'preguntas' => $preguntas
        ]);
    }

    // Guardar respuestas y comentarios (evaluación)
    public function guardarEvaluacion(Request $request)
    {
        $respuestas = $request->input('respuestas');
        $comentarios = $request->input('comentarios');

        Log::info('Respuestas: ' . json_encode($respuestas));
        Log::info('Comentarios: ' . json_encode($comentarios));

        return response()->json(['message' => 'Evaluación guardada']);
    }

    // Agregar nueva pregunta
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clasificacion' => 'required|string',
            'texto' => 'required|string',
            'tipo_opciones' => 'required|string'
        ]);

        $pregunta = PreguntaAlumno::create($validated);

        return response()->json($pregunta);
    }

    // Actualizar pregunta
    public function update(Request $request, $id)
    {
        $pregunta = PreguntaAlumno::findOrFail($id);

        $pregunta->update($request->only(['clasificacion', 'texto', 'tipo_opciones']));

        return response()->json(['message' => 'Pregunta actualizada']);
    }

    // Eliminar pregunta
    public function destroy($id)
    {
        $pregunta = PreguntaAlumno::findOrFail($id);
        $pregunta->delete();

        return response()->json(['message' => 'Pregunta eliminada']);
    }
}
