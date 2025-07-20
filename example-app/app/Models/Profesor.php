<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Profesor extends Authenticatable
{
    use HasFactory;

    protected $table = 'profesores';
    protected $primaryKey = 'id_profesor';

    protected $fillable = [
        'matricula',
        'nombre_completo',
        'correo',
        'password',
        'rol',
        'curp',
        'status',
        'created_by',
        'modified_by',
    ];

    protected $hidden = [
        'password',
    ];
}
