<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    protected $primaryKey = 'id_alumno';

    protected $fillable = [
        'matricula',
        'nombre_completo',
        'correo',
        'password',
        'rol',
        'curp',
        'grupo',
        'status',
        'created_by',
        'modified_by',
    ];
}



