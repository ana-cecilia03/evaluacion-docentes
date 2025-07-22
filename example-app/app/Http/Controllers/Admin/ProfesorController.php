<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profesor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProfesorController extends Controller
{
    /**
     *  Mostrar todos los profesores registrados, ordenados por el ID descendente.
     */
    public function index()
    {
        return Profesor::orderBy('id_profesor', 'desc')->get();
    }

    /**
     * Registrar manualmente un nuevo profesor desde formulario.
     * Valida campos requeridos y encripta la contraseña antes de guardar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|unique:profesores|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:profesores',
            'password' => 'required|string|min:6',
            'curp' => 'required|unique:profesores|size:18',
            'status' => 'in:activo,inactivo',
        ]);

        $profesor = Profesor::create([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'password' => Hash::make($request->password), // 🔐 Se encripta la contraseña
            'rol' => $request->rol ?? 'profesor',
            'curp' => $request->curp,
            'status' => $request->status ?? 'activo',
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Profesor registrado correctamente',
            'data' => $profesor
        ], 201);
    }

    /**
     * Actualizar los datos de un profesor existente.
     * Usa validación condicional para permitir mantener valores únicos (como email o curp).
     */
    public function update(Request $request, $id)
    {
        $profesor = Profesor::findOrFail($id);

        $request->validate([
            'matricula' => ['required', 'size:11', Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')],
            'nombre_completo' => 'required|string|max:100',
            'correo' => ['required', 'email', Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')],
            'curp' => ['required', 'size:18', Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')],
            'status' => 'in:activo,inactivo',
        ]);

        $profesor->update([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'curp' => $request->curp,
            'status' => $request->status,
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Profesor actualizado correctamente',
            'data' => $profesor
        ]);
    }

    /**
     * Carga masiva de profesores desde un archivo CSV.
     * Cada fila es validada individualmente. Se reportan errores por línea si ocurren.
     */
    public function importarDesdeCSV(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('archivo');
        $data = array_map('str_getcsv', file($file));
        $header = array_map('trim', $data[0]);
        unset($data[0]); // Se elimina la cabecera

        $errores = [];
        $importados = 0;

        foreach ($data as $index => $fila) {
            // Validación: cantidad de columnas debe coincidir con encabezado
            if (count($fila) !== count($header)) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => ['Cantidad de columnas inválida.']
                ];
                continue;
            }

            $fila = array_combine($header, array_map('trim', $fila));

            $validator = Validator::make($fila, [
                'matricula' => 'required|unique:profesores,matricula|size:11',
                'nombre_completo' => 'required|string|max:100',
                'correo' => 'required|email|unique:profesores,correo',
                'password' => 'required|string|min:6',
                'curp' => 'required|unique:profesores,curp|size:18',
                'status' => 'nullable|in:activo,inactivo',
            ]);

            if ($validator->fails()) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            Profesor::create([
                'matricula' => $fila['matricula'],
                'nombre_completo' => $fila['nombre_completo'],
                'correo' => $fila['correo'],
                'password' => Hash::make($fila['password']),
                'rol' => 'profesor',
                'curp' => $fila['curp'],
                'status' => $fila['status'] ?? 'activo',
                'created_by' => 'frontend',
                'modified_by' => 'frontend',
            ]);

            $importados++;
        }

        // Si hubo errores, devolverlos con código 422
        if (count($errores)) {
            return response()->json([
                'message' => 'Carga completada con errores',
                'insertados' => $importados,
                'errores' => $errores
            ], 422);
        }

        // Todo correcto
        return response()->json([
            'message' => 'Todos los profesores fueron importados correctamente.',
            'insertados' => $importados
        ], 201);
    }
}
