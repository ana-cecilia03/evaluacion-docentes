<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profesor extends Model
{
    protected $table = 'profesores';
    protected $primaryKey = 'id_profesor';
    public $timestamps = true;

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alum_prof', 'id_profesor', 'id_alumno');
    }
}

