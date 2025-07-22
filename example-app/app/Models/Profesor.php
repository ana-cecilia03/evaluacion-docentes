<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Permite generar y validar tokens de acceso (Laravel Sanctum)

class Profesor extends Authenticatable
{
    // Traits utilizados:
    use HasApiTokens, HasFactory;

    // Nombre de la tabla asociada en la base de datos
    protected $table = 'profesores';

    // Clave primaria personalizada (por defecto sería "id")
    protected $primaryKey = 'id_profesor';

    // Campos que se pueden asignar masivamente (por ejemplo al crear o actualizar registros)
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

    // Campos que se ocultarán al convertir el modelo a JSON (por seguridad)
    protected $hidden = [
        'password',
    ];
}
