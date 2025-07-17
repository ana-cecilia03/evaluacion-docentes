<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlumProf extends Model
{
    protected $table = 'alum_prof';
    protected $primaryKey = 'id_alum_prof';
    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'id_profesor');
    }
}
