<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Permite generar y validar tokens con Sanctum

class Profesor extends Authenticatable
{
    use HasApiTokens, HasFactory;

    // Tabla asociada a este modelo
    protected $table = 'profesores';

    // Clave primaria personalizada
    protected $primaryKey = 'id_profesor';

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'matricula',
        'nombre_completo',
        'correo',
        'password',
        'rol',          // Enum: 'profesor' o 'administrador'
        'cargo',        // Enum: 'PTC' o 'PA'
        'status',       // Enum: 'activo' o 'inactivo'
        'created_by',   // Usuario que creó el registro
        'modified_by',  // Usuario que modificó por última vez
    ];

    // Ocultar el campo de contraseña cuando se serializa a JSON
    protected $hidden = [
        'password',
    ];
}
