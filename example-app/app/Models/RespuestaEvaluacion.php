<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RespuestaEvaluacion extends Model
{
    protected $table = 'respuestas_evaluacion';

    protected $fillable = [
        'evaluacion_id',
        'pregunta_id',
        'calificacion'
    ];

    public $timestamps = true;

    public function evaluacion()
    {
        return $this->belongsTo(EvaluacionProfesor::class, 'evaluacion_id');
    }

    public function pregunta()
    {
        return $this->belongsTo(PreguntaProfesor::class, 'pregunta_id');
    }
}
