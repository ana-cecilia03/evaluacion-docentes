<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends Controller
{
    /**
     * Devuelve la lista de alumnos ordenados por ID descendente.
     */
    public function index()
    {
        return Alumno::orderBy('id_alumno', 'desc')->get();
    }

    /**
     * Registra manualmente un nuevo alumno (vía formulario).
     * Valida datos y encripta la contraseña antes de guardar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|unique:alumnos|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:alumnos',
            'password' => 'required|string|min:6',
            'curp' => 'required|unique:alumnos|size:18',
            'grupo' => 'nullable|string|max:10',
            'status' => 'in:activo,inactivo',
        ]);

        $alumno = Alumno::create([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'password' => Hash::make($request->password), // Encripta contraseña
            'rol' => $request->rol ?? 'alumno',
            'curp' => $request->curp,
            'grupo' => $request->grupo,
            'status' => $request->status ?? 'activo',
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Alumno registrado correctamente',
            'data' => $alumno
        ], 201);
    }

    /**
     *  Actualiza los datos de un alumno existente.
     * Usa validación con excepciones para evitar errores con valores únicos repetidos.
     */
    public function update(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);

        $request->validate([
            'matricula' => ['required', 'size:11', Rule::unique('alumnos')->ignore($alumno->id_alumno, 'id_alumno')],
            'nombre_completo' => 'required|string|max:100',
            'correo' => ['required', 'email', Rule::unique('alumnos')->ignore($alumno->id_alumno, 'id_alumno')],
            'curp' => ['required', 'size:18', Rule::unique('alumnos')->ignore($alumno->id_alumno, 'id_alumno')],
            'grupo' => 'nullable|string|max:10',
            'status' => 'in:activo,inactivo',
        ]);

        $alumno->update([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'curp' => $request->curp,
            'grupo' => $request->grupo,
            'status' => $request->status,
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Alumno actualizado correctamente',
            'data' => $alumno
        ]);
    }

    /**
     * Carga masiva de alumnos desde archivo CSV.
     * Valida cada fila y reporta errores detallados por línea si los hay.
     */
    public function importarDesdeCSV(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('archivo');
        $data = array_map('str_getcsv', file($file)); // Parsea el CSV en array
        $header = array_map('trim', $data[0]);
        unset($data[0]); // Remueve la cabecera

        $errores = [];
        $importados = 0;

        foreach ($data as $index => $fila) {
            // Valida que la fila tenga la misma cantidad de columnas que el encabezado
            if (count($fila) !== count($header)) {
                $errores[] = [
                    'linea' => $index + 2, // +2 porque se omite cabecera y se empieza en línea 1
                    'errores' => ['Cantidad de columnas inválida.']
                ];
                continue;
            }

            $fila = array_combine($header, array_map('trim', $fila));

            $validator = Validator::make($fila, [
                'matricula' => 'required|unique:alumnos,matricula|size:11',
                'nombre_completo' => 'required|string|max:100',
                'correo' => 'required|email|unique:alumnos,correo',
                'curp' => 'required|unique:alumnos,curp|size:18',
                'grupo' => 'nullable|string|max:10',
                'status' => 'in:activo,inactivo',
            ]);

            if ($validator->fails()) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            Alumno::create([
                'matricula' => $fila['matricula'],
                'nombre_completo' => $fila['nombre_completo'],
                'correo' => $fila['correo'],
                'password' => Hash::make($fila['matricula']), // Se usa matrícula como contraseña inicial
                'rol' => 'alumno',
                'curp' => $fila['curp'],
                'grupo' => $fila['grupo'] ?? null,
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
            'message' => 'Todos los alumnos fueron importados correctamente.',
            'insertados' => $importados
        ], 201);
    }
}

