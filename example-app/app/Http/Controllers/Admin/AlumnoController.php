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
     * GET /api/alumnos
     * 
     * Obtiene la lista de alumnos registrados con su grupo asociado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return Alumno::with('grupo')->orderBy('id_alumno', 'desc')->get();
    }

    /**
     * POST /api/alumnos
     * 
     * Registra un nuevo alumno.
     * 
     *  Reglas de validación:
     * - Matricula única (11 caracteres).
     * - Nombre requerido, máx. 100 caracteres.
     * - Correo único, formato válido.
     * - Contraseña mínima de 6 caracteres.
     * - Grupo opcional (debe existir).
     * - Status puede ser "activo" o "inactivo".
     * 
     *  Rol por defecto: alumno.
     *  Password se almacena encriptado.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'matricula' => 'required|unique:alumnos|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:alumnos',
            'password' => 'required|string|min:6',
            'grupo' => 'nullable|exists:grupos,id_grupo',
            'status' => 'in:activo,inactivo',
        ]);

        $alumno = Alumno::create([
            'matricula' => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo' => $request->correo,
            'password' => Hash::make($request->password),
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
     * PUT /api/alumnos/{id}
     * 
     * Actualiza los datos de un alumno.
     * 
     *   Valida que:
     * - Matrícula y correo sigan siendo únicos excepto el del propio alumno.
     * - Solo actualiza la contraseña si se envía.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  ID del alumno a actualizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $alumno = Alumno::findOrFail($id);

        $request->validate([
            'matricula' => [
                'required',
                'size:11',
                Rule::unique('alumnos', 'matricula')->ignore($alumno->id_alumno, 'id_alumno')
            ],
            'nombre_completo' => 'required|string|max:100',
            'correo' => [
                'required',
                'email',
                Rule::unique('alumnos', 'correo')->ignore($alumno->id_alumno, 'id_alumno')
            ],
            'grupo' => 'nullable|exists:grupos,id_grupo',
            'status' => 'in:activo,inactivo',
        ]);

        $alumno->matricula = $request->matricula;
        $alumno->nombre_completo = $request->nombre_completo;
        $alumno->correo = $request->correo;
        $alumno->grupo = $request->grupo;
        $alumno->status = $request->status;
        $alumno->modified_by = 'frontend';

        if (!empty($request->password)) {
            $alumno->password = Hash::make($request->password);
        }

        $alumno->save();

        return response()->json([
            'message' => 'Alumno actualizado correctamente',
            'data' => $alumno
        ]);
    }

    /**
     * POST /api/alumnos/importar
     * 
     * Importa alumnos desde un archivo CSV.
     * 
     *  El archivo debe tener cabeceras válidas como:
     * - matricula, nombre_completo, correo, grupo, status
     * 
     *  Reglas de validación:
     * - Todas las filas se validan individualmente.
     * - Se ignoran filas mal formateadas o con errores.
     * - Contraseña generada automáticamente (matrícula).
     */
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
    // Saltar filas vacías
        if (count(array_filter($fila)) === 0) {
        continue;
        }

        if (count($fila) !== count($header)) {
        $errores[] = [
            'linea' => $index + 2,
            'errores' => ['Cantidad de columnas inválida.']
        ];
        continue;
        }    

        $fila = array_combine($header, array_map('trim', $fila));

        $validator = Validator::make($fila, [
            'matricula' => 'required|unique:alumnos,matricula|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:alumnos,correo',
            'grupo' => 'nullable|exists:grupos,id_grupo',
            'status' => 'in:activo,inactivo',
            'password' => 'nullable|string|min:6|max:50' //Nueva validación
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
            'password' => Hash::make($fila['password'] ?? $fila['matricula']), // Si no trae password, usa matrícula
            'rol' => 'alumno',
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
    /**
     * PUT /api/alumnos/desactivar
     * 
     * Desactiva varios alumnos en lote, cambiando su estado a "inactivo".
     * 
     * Validación:
     * - Debe recibir un array de IDs válidos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
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
