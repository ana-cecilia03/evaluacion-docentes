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
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::guard('profesor')->attempt($request->only('correo', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $profesor = Auth::guard('profesor')->user();
        $token = $profesor->createToken('ProfesorToken')->plainTextToken;

        return response()->json([
            'message' => 'Login exitoso',
            'profesor' => $profesor,
            'token' => $token
        ]);
    }
}
