<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupos';
    protected $primaryKey = 'id_grupo';
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'created_by',
        'modified_by'
    ];
}
