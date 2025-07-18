<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;  // <-- agregado

class Profesor extends Authenticatable
{
    use HasApiTokens, Notifiable; // <-- agregado Notifiable

    protected $table = 'profesores';
    protected $primaryKey = 'id_profesor';
    public $timestamps = true;

    protected $fillable = ['correo', 'password', 'nombre_completo']; // Cambié 'nombre' por 'nombre_completo' para que coincida con la BD

    protected $hidden = ['password', 'remember_token'];

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alum_prof', 'id_profesor', 'id_alumno');
    }
}
