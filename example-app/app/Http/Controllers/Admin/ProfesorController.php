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
     * Listar todos los profesores, ordenados por ID descendente.
     * Retorna un JSON con la colección completa.
     */
    public function index()
    {
        return Profesor::orderBy('id_profesor', 'desc')->get();
    }

    /**
     * Registrar un nuevo profesor.
     * Valida datos, cifra la contraseña y persiste el registro.
     */
    public function store(Request $request)
    {
        // Validación de entrada
        $request->validate([
            'matricula'        => 'required|unique:profesores|size:11',
            'nombre_completo'  => 'required|string|max:100',
            'correo'           => 'required|email|unique:profesores',
            'password'         => 'required|string|min:6',
            'cargo'            => 'required|in:PTC,PA',
            'status'           => 'required|in:activo,inactivo',
            // Se valida el rol para evitar valores fuera del enum
            'rol'              => 'required|in:profesor,administrador',
        ]);

        // Creación del profesor
        $profesor = Profesor::create([
            'matricula'       => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo'          => $request->correo,
            'password'        => Hash::make($request->password),
            'rol'             => $request->rol ?? 'profesor', // Default en caso de que no llegue
            'cargo'           => $request->cargo,
            'status'          => $request->status,
            'created_by'      => 'frontend',
            'modified_by'     => 'frontend',
        ]);

        return response()->json([
            'message' => 'Profesor registrado correctamente',
            'data'    => $profesor
        ], 201);
    }

    /**
     * Actualizar un profesor existente.
     * Permite cambio de contraseña opcional y actualización de rol.
     */
    public function update(Request $request, $id)
    {
        $profesor = Profesor::findOrFail($id);

        // Validación con reglas de unicidad que ignoran el registro actual
        $request->validate([
            'matricula' => [
                'required',
                'size:11',
                Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor'),
            ],
            'nombre_completo' => 'required|string|max:100',
            'correo' => [
                'required',
                'email',
                Rule::unique('profesores')->ignore($profesor->id_profesor, 'id_profesor'),
            ],
            'password' => 'nullable|string|min:6',
            'cargo'    => 'required|in:PTC,PA',
            'status'   => 'required|in:activo,inactivo',
            // Se permite actualizar el rol
            'rol'      => 'required|in:profesor,administrador',
        ]);

        // Actualizar contraseña solo si se envía
        $profesor->password = $request->filled('password')
            ? Hash::make($request->password)
            : $profesor->password;

        // Actualizar campos restantes
        $profesor->update([
            'matricula'       => $request->matricula,
            'nombre_completo' => $request->nombre_completo,
            'correo'          => $request->correo,
            'cargo'           => $request->cargo,
            'status'          => $request->status,
            'password'        => $profesor->password,
            'rol'             => $request->rol, // Se persiste el rol seleccionado
            'modified_by'     => 'frontend',
        ]);

        return response()->json([
            'message' => 'Profesor actualizado correctamente',
            'data'    => $profesor
        ]);
    }

    /**
     * Importar varios profesores desde CSV.
     * Valida por fila, permite rol opcional y usa la matrícula como contraseña si no viene.
     * Formato esperado de cabecera: matricula,nombre_completo,correo,password,cargo,status,rol
     */
    public function importarDesdeCSV(Request $request)
    {
        // Validación básica del archivo
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt'
        ]);

        $file   = $request->file('archivo');
        $data   = array_map('str_getcsv', file($file));
        $header = array_map('trim', $data[0] ?? []);
        unset($data[0]); // Eliminar cabecera

        $errores    = [];
        $importados = 0;

        foreach ($data as $index => $fila) {
            // Saltar filas totalmente vacías
            if (count(array_filter($fila)) === 0) {
                continue;
            }

            // Validar cantidad de columnas
            if (count($fila) !== count($header)) {
                $errores[] = [
                    'linea'   => $index + 2,
                    'errores' => ['Cantidad de columnas inválida.']
                ];
                continue;
            }

            // Asociar valores con sus claves de cabecera
            $fila = array_combine($header, array_map('trim', $fila));

            // Validaciones por fila
            $validator = Validator::make($fila, [
                'matricula'       => 'required|unique:profesores,matricula|size:11',
                'nombre_completo' => 'required|string|max:100',
                'correo'          => 'required|email|unique:profesores,correo',
                'cargo'           => 'required|in:PTC,PA',
                'status'          => 'nullable|in:activo,inactivo',
                'password'        => 'nullable|string|min:6|max:50',
                // Se admite rol opcional en CSV
                'rol'             => 'nullable|in:profesor,administrador',
            ]);

            if ($validator->fails()) {
                $errores[] = [
                    'linea'   => $index + 2,
                    'errores' => $validator->errors()->all()
                ];
                continue;
            }

            // Crear profesor; usa matrícula como contraseña si no viene
            Profesor::create([
                'matricula'       => $fila['matricula'],
                'nombre_completo' => $fila['nombre_completo'],
                'correo'          => $fila['correo'],
                'password'        => Hash::make($fila['password'] ?? $fila['matricula']),
                'rol'             => $fila['rol'] ?? 'profesor',
                'cargo'           => $fila['cargo'],
                'status'          => $fila['status'] ?? 'activo',
                'created_by'      => 'frontend',
                'modified_by'     => 'frontend',
            ]);

            $importados++;
        }

        // Respuesta con errores parciales si aplica
        if (count($errores)) {
            return response()->json([
                'message'    => 'Carga completada con errores',
                'insertados' => $importados,
                'errores'    => $errores
            ], 422);
        }

        // Respuesta exitosa
        return response()->json([
            'message'    => 'Todos los profesores fueron importados correctamente.',
            'insertados' => $importados
        ], 201);
    }

    /**
     * Listar profesores activos.
     * Incluye un flag calculado "evaluado" según si el usuario autenticado ya los evaluó.
     */
    public function activos()
    {
        $evaluadorId = auth()->id();

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
     * Mostrar un profesor por ID.
     * Lanza 404 si no existe.
     */
    public function show($id)
    {
        return Profesor::findOrFail($id);
    }
}
