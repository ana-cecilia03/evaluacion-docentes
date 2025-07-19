<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    protected $table = 'periodos';
    protected $primaryKey = 'id_periodo';
    public $timestamps = true;

    protected $fillable = [
        'nombre_periodo',
        'fecha_inicio',
        'fecha_fin',
        'created_by',
        'modified_by',
    ];
}

