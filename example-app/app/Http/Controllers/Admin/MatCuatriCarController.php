<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MatCuatriCar;

class MatCuatriCarController extends Controller
{
    /**
     * GET /api/mat-cuatri-car
     * Devuelve todas las relaciones carrera-cuatrimestre-materia
     */
    public function index()
    {
        return response()->json(MatCuatriCar::all());
    }

    /**
     * POST /api/mat-cuatri-car
     * Registra una nueva relación carrera-cuatrimestre-materia
     */
    public function store(Request $request)
    {
        $request->validate([
            'carrera_nombre'   => 'required|string|max:100',
            'cuatri_num'       => 'required|integer',
            'materia_nombre'   => 'required|string|max:100',
        ]);

        $relacion = MatCuatriCar::create($request->all());

        return response()->json([
            'message' => 'Relación registrada correctamente',
            'data'    => $relacion
        ], 201);
    }

    /**
     * GET /api/carreras/por-periodo/{num}
     * Devuelve las carreras disponibles para un número de periodo (cuatrimestre)
     */
    public function carrerasPorPeriodo($num)
    {
        $carreras = MatCuatriCar::where('cuatri_num', $num)
            ->select('id_mat_cuatri_car', 'carrera_nombre')
            ->distinct()
            ->get();

        return response()->json($carreras);
    }

    /**
     * GET /api/materias/por-periodo/{num}
     * Devuelve las materias disponibles para un número de periodo (cuatrimestre)
     */
    public function materiasPorPeriodo($num)
    {
        $materias = MatCuatriCar::where('cuatri_num', $num)
            ->select('materia_nombre')
            ->distinct()
            ->get();

        return response()->json($materias);
    }
}
