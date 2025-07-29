<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relacion extends Model
{
    // Especificar nombre de la tabla si no sigue convención Laravel
    protected $table = 'relacions';

    protected $primaryKey = 'id_relacion';

    protected $fillable = [
        'profesor_nombre',
        'periodo_num',
        'carrera_nom',
        'materia_nom',
        'clave',
        'modified_by'
    ];

    // Relaciones con otras tablas
    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_nombre', 'id_profesor');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_num', 'id_periodo'); // corregido si usas id_periodo
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_nom', 'id_mat_cuatri_car');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'clave', 'clave');
    }
}
