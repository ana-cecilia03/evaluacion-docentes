<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GrupoController extends Controller
{
    public function index()
    {
        return Grupo::orderBy('id_grupo', 'desc')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|unique:grupos|max:10',
            'carrera' => 'required|max:100',
        ]);

        $grupo = Grupo::create([
            'clave' => $request->clave,
            'carrera' => $request->carrera,
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Grupo registrado correctamente',
            'data' => $grupo
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $grupo = Grupo::findOrFail($id);

        $request->validate([
            'clave' => 'required|max:10|unique:grupos,clave,' . $grupo->id_grupo . ',id_grupo',
            'carrera' => 'required|max:100',
        ]);

        $grupo->update([
            'clave' => $request->clave,
            'carrera' => $request->carrera,
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Grupo actualizado correctamente',
            'data' => $grupo
        ]);
    }

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
                'clave' => 'required|unique:grupos,clave|max:10',
                'carrera' => 'required|max:100',
            ]);

            if ($validator->fails()) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            Grupo::create([
                'clave' => $fila['clave'],
                'carrera' => $fila['carrera'],
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
