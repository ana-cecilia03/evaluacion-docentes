<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Carrera;

class CarreraController extends Controller
{
    // Obtener solo los nombres de carrera (para dropdowns)
    public function nombres()
    {
        return Carrera::select('nombre_carrera')->orderBy('nombre_carrera')->get();
    }

    // Obtener todas las carreras (historial)
    public function index()
    {
        return Carrera::orderBy('id_carrera', 'desc')->get();
    }

    // Registrar nueva carrera
    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:10|unique:carreras,clave',
            'nombre' => 'required|string|max:100',
        ]);

        $carrera = Carrera::create([
            'clave' => $request->clave,
            'nombre_carrera' => $request->nombre,
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Carrera registrada correctamente',
            'data' => $carrera
        ], 201);
    }

    // Actualizar una carrera existente
    public function update(Request $request, $id)
    {
        $carrera = Carrera::find($id);

        if (!$carrera) {
            return response()->json(['message' => 'Carrera no encontrada'], 404);
        }

        $request->validate([
            'clave' => "required|string|max:10|unique:carreras,clave,{$id},id_carrera",
            'nombre_carrera' => 'required|string|max:100',
        ]);

        $carrera->update([
            'clave' => $request->clave,
            'nombre_carrera' => $request->nombre_carrera,
            'modified_by' => $request->modified_by ?? 'frontend',
        ]);

        return response()->json([
            'message' => 'Carrera actualizada correctamente',
            'data' => $carrera
        ]);
    }
}
