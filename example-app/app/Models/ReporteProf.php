<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class ReporteProf extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'profesores'; // ← Esto es importante si el modelo no se llama "Profesor"
    protected $primaryKey = 'id_profesor';

    protected $fillable = [
        'matricula',
        'nombre_completo',
        'correo',
        'password',
        'rol',
        'cargo',
        'status',
        'created_by',
        'modified_by',
    ];

    protected $hidden = ['password'];
}
