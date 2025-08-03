<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionAlumno extends Model {
  protected $table = 'evaluaciones_alumnos';
  protected $fillable = ['id_alumno','id_mat_cuatri_car','id_pregunta','fecha','calificacion'];
}
