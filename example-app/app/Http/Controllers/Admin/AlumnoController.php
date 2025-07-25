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
     * Obtener todos los alumnos ordenados por ID descendente.
     */
    public function index()
    {
        return Alumno::orderBy('id_alumno', 'desc')->get();
    }

    /**
     * Registrar un alumno manualmente desde formulario.
     */
    public function store(Request $request)
    {
        // Validación de campos
        $request->validate([
            'matricula' => 'required|unique:alumnos|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:alumnos',
            'password' => 'required|string|min:6',
            'grupo' => 'nullable|integer', // Debe existir como ID en tabla grupos
            'status' => 'in:activo,inactivo',
        ]);

        // Crear alumno en base de datos
        $alumno = Alumno::create([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'password' => Hash::make($request->password), // Encriptar contraseña
            'rol' => $request->rol ?? 'alumno',
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
     * Actualizar datos de un alumno existente.
     */
    public function update(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);

        // Validar campos y evitar conflictos de claves únicas con el propio registro
        $request->validate([
            'matricula' => ['required', 'size:11', Rule::unique('alumnos')->ignore($alumno->id_alumno, 'id_alumno')],
            'nombre_completo' => 'required|string|max:100',
            'correo' => ['required', 'email', Rule::unique('alumnos')->ignore($alumno->id_alumno, 'id_alumno')],
            'grupo' => 'nullable|integer',
            'status' => 'in:activo,inactivo',
        ]);

        // Actualizar alumno
        $alumno->update([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
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
     * - Valida cada fila.
     * - Reporta errores con línea específica.
     * - Si la fila es válida, inserta automáticamente.
     * - La contraseña se genera en base a la matrícula.
     */
    public function importarDesdeCSV(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt'
        ]);

        // Leer archivo CSV
        $file = $request->file('archivo');
        $data = array_map('str_getcsv', file($file));
        $header = array_map('trim', $data[0]);
        unset($data[0]); // Quitar encabezado

        $errores = [];
        $importados = 0;

        foreach ($data as $index => $fila) {
            // Validar que número de columnas coincida con encabezado
            if (count($fila) !== count($header)) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => ['Cantidad de columnas inválida.']
                ];
                continue;
            }

            // Combinar encabezado con fila
            $fila = array_combine($header, array_map('trim', $fila));

            // Validación de datos
            $validator = Validator::make($fila, [
                'matricula' => 'required|unique:alumnos,matricula|size:11',
                'nombre_completo' => 'required|string|max:100',
                'correo' => 'required|email|unique:alumnos,correo',
                'grupo' => 'nullable|integer', // Asegúrate que sea id válido de la tabla grupos
                'status' => 'in:activo,inactivo',
            ]);

            // Si hay errores en la fila, se guarda y continúa
            if ($validator->fails()) {
                $errores[] = [
                    'linea' => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            // Insertar alumno
            Alumno::create([
                'matricula' => $fila['matricula'],
                'nombre_completo' => $fila['nombre_completo'],
                'correo' => $fila['correo'],
                'password' => Hash::make($fila['matricula']), // La contraseña será la matrícula
                'rol' => 'alumno',
                'grupo' => $fila['grupo'] ?? null,
                'status' => $fila['status'] ?? 'activo',
                'created_by' => 'frontend',
                'modified_by' => 'frontend',
            ]);

            $importados++;
        }

        // Si hubo errores en alguna fila, los devuelve con código 422
        if (count($errores)) {
            return response()->json([
                'message' => 'Carga completada con errores',
                'insertados' => $importados,
                'errores' => $errores
            ], 422);
        }

        // Todo fue correcto
        return response()->json([
            'message' => 'Todos los alumnos fueron importados correctamente.',
            'insertados' => $importados
        ], 201);
    }

    /**
     * Desactivar múltiples alumnos en lote.
     */
    public function desactivarVarios(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:alumnos,id_alumno'
        ]);

        Alumno::whereIn('id_alumno', $request->ids)
            ->update([
                'status' => 'inactivo',
                'modified_by' => 'frontend',
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'Alumnos desactivados correctamente.'
        ]);
    }
}
