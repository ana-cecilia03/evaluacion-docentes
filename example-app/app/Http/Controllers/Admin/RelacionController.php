<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Relacion;
use Illuminate\Http\Request;

class RelacionController extends Controller
{
    public function index()
    {
        return Relacion::with(['profesor', 'periodo', 'matCuatriCar', 'grupo'])->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'profesor_id' => 'required|exists:profesores,id_profesor',
            'periodo_id' => 'required|exists:periodos,id_periodo',
            'id_mat_cuatri_car' => 'required|exists:mat_cuatri_carr,id_mat_cuatri_car',
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'modified_by' => 'nullable|string|max:100',
        ]);

        return Relacion::create($data);
    }
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'profesor_id' => 'required|exists:profesores,id_profesor',
            'periodo_id' => 'required|exists:periodos,id_periodo',
            'id_mat_cuatri_car' => 'required|exists:mat_cuatri_carr,id_mat_cuatri_car',
            'grupo_id' => 'required|exists:grupos,id_grupo',
            'modified_by' => 'nullable|string|max:100',
        ]);

        $relacion = Relacion::findOrFail($id);
        $relacion->update($data);

        return response()->json(['message' => 'Relación actualizada correctamente']);
    }
}
