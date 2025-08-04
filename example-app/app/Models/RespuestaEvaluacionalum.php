<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EvaluacionAlumno1;
use App\Models\PreguntaAlumno;

class RespuestaEvaluacionalum extends Model
{
    // Nombre explícito de la tabla de respuestas
    protected $table = 'respuestas_evaluacionalum';

    protected $fillable = [
        'id_evaluacion',
        'id_pregunta',
        'calificacion',
    ];

    public $timestamps = false;

    // Relación: una respuesta pertenece a una evaluación
    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionAlumno1::class, 'id_evaluacion');
    }

    // Relación: una respuesta pertenece a una pregunta
    public function pregunta()
    {
        return $this->belongsTo(PreguntaAlumno::class, 'id_pregunta');
    }
}
