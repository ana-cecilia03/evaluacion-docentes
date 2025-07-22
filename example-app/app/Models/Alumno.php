<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens; // Permite emitir y verificar tokens de acceso con Sanctum
use Illuminate\Notifications\Notifiable;

class Alumno extends Authenticatable
{
    // Traits necesarios para:
    // - HasApiTokens: autenticar vía API con Laravel Sanctum
    // - Notifiable: enviar notificaciones (no obligatorio, pero útil si usas mails o alerts)
    use HasApiTokens, Notifiable;

    // Define el nombre de la tabla en la base de datos
    protected $table = 'alumnos';

    // Define la clave primaria personalizada (por defecto sería "id")
    protected $primaryKey = 'id_alumno';

    // Campos que se pueden asignar masivamente (ej: Alumno::create([...]))
    protected $fillable = [
        'matricula',
        'nombre_completo',
        'correo',
        'password',
        'rol',
        'curp',
        'grupo',
        'status',
        'created_by',
        'modified_by',
    ];

    // Campos ocultos cuando el modelo se convierte a JSON (para proteger información sensible)
    protected $hidden = [
        'password',
    ];
}
