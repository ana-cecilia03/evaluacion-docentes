<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materias';
    protected $primaryKey = 'id_materia';

    protected $fillable = [
        'nombre_materia',
        'created_by',
        'modified_by',
    ];

    public $timestamps = true; 
}
