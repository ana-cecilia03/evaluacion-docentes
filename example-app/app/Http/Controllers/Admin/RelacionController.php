<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relacion;

class RelacionController extends Controller
{
    public function index()
    {
        return Relacion::with(['profesor', 'periodo', 'carrera', 'grupo'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'profesor_nombre' => 'required|exists:profesores,id_profesor',
            'periodo_num' => 'required|exists:periodos,id_periodo',
            'carrera_nom' => 'required|exists:carreras,id_carrera',
            'materia_nom' => 'required|string|max:100',
            'clave' => 'required|exists:grupos,nombre',
            'modified_by' => 'nullable|string|max:100',
        ]);

        return Relacion::create($data);
    }
}
