<?php

namespace App\Http\Controllers\Admin;

use App\Models\EvaluacionAlumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PreguntaAlumno;

class EvaluacionAlumnoController extends Controller
{
  public function listaMateriasEval(Request $req) {
    $alumnoId = Auth::id();
    // Consulta tus relaciones grupales y que retorne profesores/materia según logic
    $materias = /* consulta personalizada por grupo, activo */;
    $yaEvaluadas = EvaluacionAlumno::where('id_alumno',$alumnoId)->pluck('id_mat_cuatri_car')->toArray();
    $sinEvaluar = array_filter($materias, fn($m)=>!in_array($m['id_mat_cuatri_car'],$yaEvaluadas));
    return response()->json(['materias'=>$sinEvaluar]);
  }
}
