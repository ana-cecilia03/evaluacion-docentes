<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profesor;

class PerfilController extends Controller
{
    public function mostrar()
    {
        $profesor = Auth::user(); // Asegúrate de que el profesor esté autenticado vía Sanctum

        if (!$profesor) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        return response()->json([
            'nombre_completo' => $profesor->nombre_completo,
            'correo' => $profesor->correo,
        ]);
    }

    public function actualizar(Request $request)
    {
        $profesor = Auth::user();

        if (!$profesor) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'correo' => 'required|email',
            'password' => 'nullable|string|min:6',
        ]);

        $profesor->nombre_completo = $request->nombre_completo;
        $profesor->correo = $request->correo;

        if ($request->filled('password')) {
            $profesor->password = bcrypt($request->password);
        }

        $profesor->save();

        return response()->json(['message' => 'Perfil actualizado']);
    }
}
