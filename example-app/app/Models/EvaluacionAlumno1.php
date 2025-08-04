<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alumno;
use App\Models\Relacion;
use App\Models\RespuestaEvaluacionalum;

class EvaluacionAlumno1 extends Model
{
    protected $table = 'evaluaciones_alumnos1';

    protected $fillable = [
        'id_alumno',
        'relacion_id',
        'fecha',
    ];

    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function relacion()
    {
        return $this->belongsTo(Relacion::class, 'relacion_id');
    }

    public function respuestas()
    {
        return $this->hasMany(RespuestaEvaluacionalum::class, 'id_evaluacion');
    }
}
