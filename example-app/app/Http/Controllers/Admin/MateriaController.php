<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MateriaController extends Controller
{
    // Obtener solo los nombres de materias (para dropdowns)
    public function nombres()
    {
        return Materia::select('nombre_materia')->orderBy('nombre_materia')->get();
    }

    // Obtener todas las materias
    public function index()
    {
        return Materia::orderBy('id_materia', 'desc')->get();
    }

    // Obtener una materia por ID
    public function show($id)
    {
        $materia = Materia::find($id);

        if (!$materia) {
            return response()->json(['error' => 'Materia no encontrada'], 404);
        }

        return response()->json($materia);
    }

    // Registrar nueva materia
    public function store(Request $request)
    {
        $request->validate([
            'clave' => 'required|string|max:15|unique:materias,clave',
            'nombre_materia' => 'required|string|max:100'
        ]);

        $materia = Materia::create([
            'clave' => $request->clave,
            'nombre_materia' => $request->nombre_materia,
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Materia registrada correctamente',
            'data' => $materia
        ], 201);
    }

    // Actualizar materia existente
    public function update(Request $request, $id)
    {
        $materia = Materia::find($id);

        if (!$materia) {
            return response()->json(['error' => 'Materia no encontrada'], 404);
        }

        $request->validate([
            'clave' => 'required|string|max:15|unique:materias,clave,' . $materia->id_materia . ',id_materia',
            'nombre_materia' => 'required|string|max:100'
        ]);

        $materia->update([
            'clave' => $request->clave,
            'nombre_materia' => $request->nombre_materia,
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Materia actualizada correctamente',
            'data' => $materia
        ]);
    }

    // Cargar materias desde archivo CSV
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
        $importados = 0;

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
                'clave' => 'required|string|max:15|unique:materias,clave',
                'nombre_materia' => 'required|string|max:100',
            ]);

            if ($validator->fails()) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            Materia::create([
                'clave' => $fila['clave'],
                'nombre_materia' => $fila['nombre_materia'],
                'created_by' => 'frontend',
                'modified_by' => 'frontend',
            ]);

            $importados++;
        }

        if (count($errores)) {
            return response()->json([
                'message' => 'Carga completada con errores',
                'insertados' => $importados,
                'errores' => $errores
            ], 422);
        }

        return response()->json([
            'message' => 'Todas las materias fueron importadas correctamente.',
            'insertados' => $importados
        ], 201);
    }
}
