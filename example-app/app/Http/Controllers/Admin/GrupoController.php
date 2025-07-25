<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GrupoController extends Controller
{
    // Listar todos los grupos
    public function index()
    {
        return Grupo::orderBy('id_grupo', 'desc')->get();
    }

    // Registrar grupo manualmente
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|unique:grupos|max:15',
        ]);

        $grupo = Grupo::create([
            'nombre' => $request->nombre,
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Grupo registrado correctamente',
            'data' => $grupo
        ], 201);
    }

    // Actualizar grupo
    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $request->validate([
            'nombre' => 'required|max:15|unique:grupos,nombre,' . $grupo->id_grupo . ',id_grupo',
        ]);

        $grupo->update([
            'nombre' => $request->nombre,
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Grupo actualizado correctamente',
            'data' => $grupo
        ]);
    }

    // Importar grupos desde CSV
    public function importarDesdeCSV(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('archivo');
        $data = array_map('str_getcsv', file($file));
        $header = array_map('trim', $data[0]);
        unset($data[0]);

        $errores = [];
        $insertados = 0;

        foreach ($data as $index => $fila) {
            if (count($fila) !== count($header)) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => ['Cantidad de columnas inválida.']
                ];
                continue;
            }

            $fila = array_combine($header, array_map('trim', $fila));

            $validator = Validator::make($fila, [
                'nombre' => 'required|unique:grupos,nombre|max:15',
            ]);

            if ($validator->fails()) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            Grupo::create([
                'nombre' => $fila['nombre'],
                'created_by' => 'frontend',
                'modified_by' => 'frontend',
            ]);

            $insertados++;
        }

        if (count($errores)) {
            return response()->json([
                'message' => 'Carga completada con errores',
                'insertados' => $insertados,
                'errores' => $errores
            ], 422);
        }

        return response()->json([
            'message' => 'Todos los grupos fueron importados correctamente.',
            'insertados' => $insertados
        ], 201);
    }
}
