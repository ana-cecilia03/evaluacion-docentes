<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Periodo;

class PeriodoController extends Controller
{
    /**
     * GET /api/periodos
     * Devuelve todos los periodos registrados, ordenados del más reciente al más antiguo.
     */
    public function index()
    {
        $periodos = Periodo::orderBy('id_periodo', 'desc')->get();

        return response()->json($periodos);
    }

    /**
     * POST /api/periodos
     * Guarda un nuevo periodo en la base de datos.
     * 
     * Notas:
     * - `num_periodo`: número identificador lógico (por ejemplo, 1, 2, 3...).
     * - `estado`: opcional, por defecto será 'inactivo'. Solo acepta 'activo' o 'inactivo'.
     * - Las fechas se validan para que la de fin no sea anterior a la de inicio.
     */
    public function store(Request $request)
    {
        // Validaciones de entrada
        $request->validate([
            'num_periodo'     => 'required|integer|unique:periodos,num_periodo',
            'nombre_periodo'  => 'required|string|max:100',
            'fecha_inicio'    => 'required|date',
            'fecha_fin'       => 'required|date|after_or_equal:fecha_inicio',
            'estado'          => 'in:activo,inactivo' // Opcional
        ]);

        // Crea el nuevo periodo con valores por defecto para estado y auditoría
        $periodo = Periodo::create([
            'num_periodo'    => $request->num_periodo,
            'nombre_periodo'=> $request->nombre_periodo,
            'fecha_inicio'  => $request->fecha_inicio,
            'fecha_fin'     => $request->fecha_fin,
            'estado'        => $request->estado ?? 'inactivo',
            'created_by'    => 'frontend', // Reemplaza si usas auth()->user()->name
            'modified_by'   => 'frontend',
        ]);

        return response()->json([
            'message' => 'Periodo creado correctamente',
            'data'    => $periodo
        ], 201);
    }

    /**
     * PUT /api/periodos/{id}/estado
     * Cambia el estado del periodo (activo/inactivo).
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:activo,inactivo'
        ]);

        $periodo = Periodo::findOrFail($id);
        $periodo->estado = $request->estado;
        $periodo->modified_by = 'frontend';
        $periodo->save();

        return response()->json([
            'message' => 'Estado actualizado correctamente.'
        ]);
    }
}
