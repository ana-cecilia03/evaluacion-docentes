<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreguntaProfesor extends Model
{
    protected $table = 'preguntas_profesores';

    protected $fillable = [
        'factor',
        'definicion',
        'tipo',
        'orden',
        'activo'
    ];

    public $timestamps = true;
}
