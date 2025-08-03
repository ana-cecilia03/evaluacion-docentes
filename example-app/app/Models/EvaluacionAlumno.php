<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alumno;
use App\Models\Relacion;
use App\Models\PreguntaAlumno;

class EvaluacionAlumno extends Model
{
    protected $table = 'evaluaciones_alumnos';

    protected $fillable = [
        'id_alumno',
        'relacion_id',
        'id_pregunta',
        'fecha',
        'calificacion',
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

    public function pregunta()
    {
        return $this->belongsTo(PreguntaAlumno::class, 'id_pregunta');
    }
}
