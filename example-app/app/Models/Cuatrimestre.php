<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuatrimestre extends Model
{
    protected $table = 'cuatrimestres'; // Ajusta si el nombre de la tabla es diferente
    protected $primaryKey = 'id_cuatrimestre';

    protected $fillable = [
        'num',
        'nombre',
        'created_by',
        'modified_by'
    ];
}
