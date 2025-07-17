<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatPerCarr extends Model
{
    protected $table = 'mat_per_carr';
    protected $primaryKey = 'id_mat_per_car';
    public $timestamps = false;

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera');
    }
}

