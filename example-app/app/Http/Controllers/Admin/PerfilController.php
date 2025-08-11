<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user(); // modelo Profesor autenticado
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $request->validate([
            'nombre'   => 'required|string|min:3|max:255',
            'correo'   => 'required|email|max:255|unique:profesores,correo,' . $user->id_profesor . ',id_profesor',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->nombre_completo = $request->input('nombre');
        $user->correo          = $request->input('correo');

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado correctamente',
            'user' => [
                'id'     => $user->id_profesor,
                'nombre' => $user->nombre_completo,
                'correo' => $user->correo,
                'rol'    => $user->rol,
            ],
        ]);
    }
}
