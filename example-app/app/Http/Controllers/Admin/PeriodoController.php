<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Periodo;

class PeriodoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nombre_periodo' => 'required|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        $periodo = Periodo::create([
            'nombre_periodo' => $request->nombre_periodo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'created_by' => 'frontend',
            'modified_by' => 'frontend',
        ]);

        return response()->json([
            'message' => 'Periodo creado correctamente',
            'data' => $periodo
        ], 201);
    }
}
