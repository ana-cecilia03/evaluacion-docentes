<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    /**
     * Nombre de la tabla en la base de datos.
     */
    protected $table = 'periodos';

    /**
     * Nombre de la clave primaria.
     */
    protected $primaryKey = 'id_periodo';

    /**
     * Los campos que se pueden asignar masivamente (mass assignment).
     * 
     * Si agregas o quitas columnas en la tabla `periodos`, recuerda actualizarlos aquí.
     */
    protected $fillable = [
        'num_periodo',       // Número lógico del periodo (ej. 1, 2, 3...)
        'nombre_periodo',    // Ejemplo: "Primer Cuatrimestre"
        'fecha_inicio',      // Fecha en formato YYYY-MM-DD
        'fecha_fin',         // Fecha en formato YYYY-MM-DD
        'estado',            // 'activo' o 'inactivo'
        'created_by',        // Usuario o sistema que creó el registro
        'modified_by',       // Usuario o sistema que modificó por última vez
    ];
}
