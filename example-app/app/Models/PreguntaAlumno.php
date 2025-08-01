<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaAlumno extends Model
{
    protected $table = 'preguntas_alumno';
    public $timestamps = false;

    protected $fillable = [
        'clasificacion', 
        'texto', 
        'tipo_opciones'
    ];
}
