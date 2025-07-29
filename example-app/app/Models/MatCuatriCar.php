<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatCuatriCar extends Model
{
    protected $table = 'mat_cuatri_carr';

    protected $primaryKey = 'id_mat_cuatri_car';

    public $timestamps = false; // <--- esto es lo importante

    protected $fillable = [
        'carrera_nombre',
        'cuatri_num',
        'materia_nombre',
    ];
}
