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
    // Listar profesores
    public function index()
    {
        return Profesor::orderBy('id_profesor', 'desc')->get();
    }

    // Registrar nuevo profesor
    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|unique:profesores|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:profesores',
            'password' => 'required|string|min:6',
            'cargo' => 'required|in:PTC,PA',
            'status' => 'required|in:activo,inactivo',
        ]);

        $profesor = Profesor::create([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
            'rol' => $request->rol ?? 'profesor',
            'cargo' => $request->cargo,
            'status' => $request->status,
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Profesor registrado correctamente',
            'data' => $profesor
        ], 201);
    }

    // Actualizar profesor
    public function update(Request $request, $id)
    {
        $profesor = Profesor::findOrFail($id);

        $request->validate([
            'matricula' => ['required', 'size:11', Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')],
            'nombre_completo' => 'required|string|max:100',
            'correo' => ['required', 'email', Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')],
            'password' => 'nullable|string|min:6',
            'cargo' => 'required|in:PTC,PA',
            'status' => 'required|in:activo,inactivo',
        ]);

        $profesor->password = $request->filled('password')
            ? Hash::make($request->password)
            : $profesor->password;

        $profesor->update([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'cargo' => $request->cargo,
            'status' => $request->status,
            'password' => $profesor->password,
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Profesor actualizado correctamente',
            'data' => $profesor
        ]);
    }

    // Importar desde CSV
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
                'matricula' => 'required|unique:profesores,matricula|size:11',
                'nombre_completo' => 'required|string|max:100',
                'correo' => 'required|email|unique:profesores,correo',
                'password' => 'required|string|min:6',
                'cargo' => 'required|in:PTC,PA',
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
                'cargo' => $fila['cargo'],
                'status' => $fila['status'] ?? 'activo',
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
            'message' => 'Todos los profesores fueron importados correctamente.',
            'insertados' => $importados
        ], 201);
    }
    
    // Listar profesores activos (solo para historial o select)
    public function activos()
    {
    return Profesor::where('status', 'activo')
        ->select('id_profesor', 'matricula', 'nombre_completo', 'cargo')
        ->orderBy('nombre_completo')
        ->get();
    }
}
