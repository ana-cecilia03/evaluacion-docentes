<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Profesor;

class Alumno extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'alumnos';
    protected $primaryKey = 'id_alumno';
    public $timestamps = true;

    // Oculta el password (y remember_token si lo usas) en las respuestas JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relación con profesores (muchos a muchos)
    public function profesores()
    {
        return $this->belongsToMany(Profesor::class, 'alum_prof', 'id_alumno', 'id_profesor');
    }
}



