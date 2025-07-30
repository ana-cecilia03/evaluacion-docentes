<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relacion extends Model
{
    protected $table = 'relacions';
    protected $primaryKey = 'id_relacion';

    protected $fillable = [
        'profesor_nombre',
        'periodo_num',
        'carrera_nom',
        'materia_nom',
        'clave',
        'modified_by',
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_nombre', 'id_profesor');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_num', 'id_periodo');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'carrera_nom', 'id_carrera');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'clave', 'nombre');
    }
}
