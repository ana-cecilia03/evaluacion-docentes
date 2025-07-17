<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Profesor; // ✅ ESTA LÍNEA ES NECESARIA

class Alumno extends Model
{
    protected $table = 'alumnos';
    protected $primaryKey = 'id_alumno';
    public $timestamps = true;

    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'alum_prof', 'id_alumno', 'id_profesor');
    }
}

