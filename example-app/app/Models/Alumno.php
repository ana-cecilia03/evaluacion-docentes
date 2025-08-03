<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Alumno extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'alumnos';
    protected $primaryKey = 'id_alumno';

    protected $fillable = [
        'matricula',
        'nombre_completo',
        'correo',
        'password',
        'rol',
        'curp',
        'grupo', // Este campo contiene el id_grupo
        'status',
        'created_by',
        'modified_by',
    ];

    protected $hidden = [
        'password',
    ];

    // 🔗 Relación con el modelo Grupo
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo', 'id_grupo'); // 'grupo' es la FK en la tabla alumnos
    }
}
