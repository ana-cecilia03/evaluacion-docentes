<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaAlumno extends Model
{
    protected $table = 'preguntas_alumno';
    protected $primaryKey = 'id';

    protected $fillable = [
        'clasificacion',
        'texto',
        'tipo_opciones',
    ];

    public $timestamps = false;
}
