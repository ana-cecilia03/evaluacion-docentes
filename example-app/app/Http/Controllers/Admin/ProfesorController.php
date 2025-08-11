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
     * Obtener todos los profesores (ordenados por ID descendente).
     */
    public function index()
    {
        return Profesor::orderBy('id_profesor', 'desc')->get();
    }

    /**
     * Registrar un nuevo profesor con validación y cifrado de contraseña.
     */
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

        // Crear nuevo profesor
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

    /**
     * Actualizar un profesor existente (con validación y cambio de contraseña opcional).
     */
    public function update(Request $request, $id)
    {
        $profesor = Profesor::findOrFail($id);

        $request->validate([
            'matricula' => [
                'required',
                'size:11',
                Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')
            ],
            'nombre_completo' => 'required|string|max:100',
            'correo' => [
                'required',
                'email',
                Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor')
            ],
            'password' => 'nullable|string|min:6',
            'cargo' => 'required|in:PTC,PA',
            'status' => 'required|in:activo,inactivo',
        ]);

        // Solo cambia la contraseña si fue enviada
        $profesor->password = $request->filled('password')
            ? Hash::make($request->password)
            : $profesor->password;

        // Actualizar campos
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

    /**
     * Importar varios profesores desde un archivo CSV (validación por fila).
     */
    public function importarDesdeCSV(Request $request)
    {
    $request->validate([
        'archivo' => 'required|file|mimes:csv,txt'
    ]);

    $file = $request->file('archivo');
    $data = array_map('str_getcsv', file($file));
    $header = array_map('trim', $data[0]);
    unset($data[0]); // Eliminar cabecera

    $errores = [];
    $importados = 0;

    foreach ($data as $index => $fila) {
        // Saltar filas vacías
        if (count(array_filter($fila)) === 0) {
            continue;
        }

        // Validar cantidad de columnas
        if (count($fila) !== count($header)) {
            $errores[] = [
                'linea' => $index + 2,
                'errores' => ['Cantidad de columnas inválida.']
            ];
            continue;
        }

        // Asociar valores con sus claves
        $fila = array_combine($header, array_map('trim', $fila));

        // Validaciones por fila
        $validator = Validator::make($fila, [
            'matricula' => 'required|unique:profesores,matricula|size:11',
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email|unique:profesores,correo',
            'cargo' => 'required|in:PTC,PA',
            'status' => 'nullable|in:activo,inactivo',
            'password' => 'nullable|string|min:6|max:50', // password opcional
        ]);

        if ($validator->fails()) {
            $errores[] = [
                'linea' => $index + 2,
                'errores' => $validator->errors()->all()
            ];
            continue;
        }

        // Crear profesor (usa matrícula como contraseña si no viene)
        Profesor::create([
            'matricula' => $fila['matricula'],
            'nombre_completo' => $fila['nombre_completo'],
            'correo' => $fila['correo'],
            'password' => Hash::make($fila['password'] ?? $fila['matricula']),
            'rol' => 'profesor',
            'cargo' => $fila['cargo'],
            'status' => $fila['status'] ?? 'activo',
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        $importados++;
    }

    // Devolver errores si existen
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

    /**
     * Obtener profesores activos (usado en listas o historial).
     */
    public function activos()
    {
    $evaluadorId = auth()->id(); // ID del usuario logueado

    return Profesor::where('status', 'activo')
        ->select('id_profesor', 'matricula', 'nombre_completo', 'cargo', 'status')
        ->selectRaw('
            EXISTS (
                SELECT 1 
                FROM evaluaciones_profesores ep
                WHERE ep.profesor_id = profesores.id_profesor
                  AND ep.evaluador_id = ?
            ) AS evaluado
        ', [$evaluadorId])
        ->orderBy('nombre_completo')
        ->get();
    }

    /**
     * Mostrar un solo profesor por su ID.
     */
    public function show($id)
    {
        return Profesor::findOrFail($id);
    }
}
