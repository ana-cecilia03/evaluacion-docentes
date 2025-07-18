<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Profesor extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'profesores';
    protected $primaryKey = 'id_profesor';
    public $timestamps = true;

    protected $fillable = ['correo', 'password', 'nombre']; // Agrega si es necesario

    protected $hidden = ['password'];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alum_prof', 'id_profesor', 'id_alumno');
    }
}



