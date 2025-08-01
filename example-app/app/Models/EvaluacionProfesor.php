<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RespuestaEvaluacion;
use App\Models\Profesor;

class EvaluacionProfesor extends Model
{
    protected $table = 'evaluaciones_profesores';

    protected $fillable = [
        'profesor_id',
        'evaluador_id',
        'tipo',
        'periodo',
        'calif_i',
        'calif_ii',
        'calificacion_final',
        'comentario'
    ];

    public $timestamps = true;

    public function respuestas()
    {
        return $this->hasMany(RespuestaEvaluacion::class, 'evaluacion_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }
}
