<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReporteProf;
use Illuminate\Http\Request;

class ReporteProfcontrol extends Controller
{
    public function index()
    {
        return ReporteProf::select('id_profesor', 'matricula', 'nombre_completo', 'cargo')
                          ->where('status', 'activo')
                          ->orderBy('id_profesor', 'desc')
                          ->get();
    }
}
