<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;

class LoginAlumno extends Controller
{
    public function login(Request $request)
    {
        // ✅ Validación de datos
        $request->validate([
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        // 🔎 Buscar al alumno por correo
        $alumno = Alumno::where('correo', $request->correo)->first();

        // ❌ Si no existe o la contraseña no coincide
        if (!$alumno || !Hash::check($request->password, $alumno->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // 🔑 Generar un token para API
        $token = $alumno->createToken('AlumnoToken')->plainTextToken;

        // ✅ Respuesta con token y datos del alumno
        return response()->json([
            'message' => 'Login exitoso',
            'alumno' => $alumno,
            'token' => $token
        ]);
    }
}

