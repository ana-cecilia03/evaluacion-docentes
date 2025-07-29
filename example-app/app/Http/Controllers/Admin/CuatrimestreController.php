<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuatrimestre;
use Illuminate\Http\Request;

class CuatrimestreController extends Controller
{
    // Obtener todos los cuatrimestres
    public function index()
    {
        return Cuatrimestre::all();
    }

    // Registrar nuevo cuatrimestre
    public function store(Request $request)
    {
        $validated = $request->validate([
            'num' => 'nullable|integer',
            'nombre' => 'required|string|max:30|unique:cuatrimestres,nombre',
            'created_by' => 'required|string|max:100',
        ]);

        $validated['modified_by'] = $validated['created_by'];

        return Cuatrimestre::create($validated);
    }

    // Actualizar cuatrimestre existente
    public function update(Request $request, $id)
    {
        $cuatrimestre = Cuatrimestre::findOrFail($id);

        $validated = $request->validate([
            'num' => 'nullable|integer',
            'nombre' => 'required|string|max:30|unique:cuatrimestres,nombre,' . $id . ',id_cuatrimestre',
            'modified_by' => 'required|string|max:100',
        ]);

        $cuatrimestre->update($validated);

        return $cuatrimestre;
    }

    // Obtener solo los números de cuatrimestres (para dropdowns)
    public function numeros()
    {
        return Cuatrimestre::select('num')->orderBy('num')->get();
    }
}
