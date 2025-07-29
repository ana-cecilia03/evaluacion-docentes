<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Relacion;

class RelacionController extends Controller
{
    /**
     * Devuelve todas las relaciones con sus asociaciones.
     */
    public function index()
    {
        return Relacion::with(['profesor', 'periodo', 'carrera', 'grupo'])->get();
    }

    /**
     * Almacena una nueva relación.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'profesor_nombre' => 'required|exists:profesores,id_profesor',
            'periodo_num' => 'required|exists:periodos,id_periodo',
            'carrera_nom' => 'required|exists:mat_cuatri_carr,id_mat_cuatri_car',
            'materia_nom' => 'required|string|max:100',
            'clave' => 'required|exists:grupos,clave',
            'modified_by' => 'nullable|string|max:100',
        ]);

        return Relacion::create($data);
    }
}
