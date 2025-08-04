<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Alumno;
use App\Models\Relacion;
use App\Models\PreguntaAlumno;

class EvaluacionAlumno extends Model
{
    // Nombre explícito de la tabla en la base de datos
    protected $table = 'evaluaciones_alumnos';

    // Campos que se pueden asignar de forma masiva
    protected $fillable = [
        'id_alumno',
        'relacion_id',
        'id_pregunta',
        'fecha',
        'calificacion',
    ];

    // Desactiva timestamps automáticos (created_at, updated_at)
    public $timestamps = false;

    // Relación: una evaluación pertenece a un alumno
    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    // Relación: una evaluación pertenece a una relación prof-mat-grupo
    public function relacion()
    {
        return $this->belongsTo(Relacion::class, 'relacion_id');
    }

    // Relación: una evaluación pertenece a una pregunta específica
    public function pregunta()
    {
        return $this->belongsTo(PreguntaAlumno::class, 'id_pregunta');
    }
}
