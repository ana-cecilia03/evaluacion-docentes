<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relacion extends Model
{
    protected $table = 'relacions';
    protected $primaryKey = 'id_relacion';

    protected $fillable = [
        'profesor_id',
        'periodo_id',
        'id_mat_cuatri_car',
        'grupo_id',
        'modified_by'
    ];

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'periodo_id');
    }

    public function matCuatriCar()
    {
        return $this->belongsTo(MatCuatriCar::class, 'id_mat_cuatri_car');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id');
    }
}
