<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginProfesor extends Controller
{
    public function login(Request $request)
    {
        // Validación básica
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors'  => $validator->errors()
            ], 422);
        }

        // Autenticación con el guard de profesores
        if (!Auth::guard('profesor')->attempt($request->only('correo', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Usuario autenticado
        $profesor = Auth::guard('profesor')->user();

        // *** RESTRICCIÓN DE ROL: solo administradores ***
        if ($profesor->rol !== 'administrador') {
            // Cerramos la sesión por si el guard creó sesión
            Auth::guard('profesor')->logout();

            return response()->json([
                'message' => 'Acceso restringido: solo administradores pueden iniciar sesión.'
            ], 403);
        }

        // Solo aquí se genera el token (usuario con rol administrador)
        $token = $profesor->createToken('ProfesorToken')->plainTextToken;

        return response()->json([
            'message'  => 'Login exitoso',
            'profesor' => $profesor,
            'token'    => $token
        ]);
    }
}
