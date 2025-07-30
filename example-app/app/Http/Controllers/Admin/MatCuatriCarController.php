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
        return response()->json(
            MatCuatriCar::orderBy('carrera_nombre')
                ->orderBy('cuatri_num')
                ->orderBy('materia_nombre')
                ->get()
        );
    }

    /**
     * POST /api/mat-cuatri-car
     * Registra una nueva relación carrera-cuatrimestre-materia
     */
    public function store(Request $request)
    {
        $request->validate([
            'carrera_nombre' => 'required|string|max:100',
            'cuatri_num'     => 'required|integer',
            'materia_nombre' => 'required|string|max:100',
        ]);

        $relacion = MatCuatriCar::create([
            'carrera_nombre' => $request->carrera_nombre,
            'cuatri_num'     => $request->cuatri_num,
            'materia_nombre' => $request->materia_nombre,
        ]);

        return response()->json([
            'message' => 'Relación registrada correctamente',
            'data'    => $relacion
        ], 201);
    }

    /**
     * PUT /api/mat-cuatri-car/{id}
     * Actualiza una relación existente carrera-cuatrimestre-materia
     */
    public function update(Request $request, $id)
    {
        $relacion = MatCuatriCar::findOrFail($id);

        $request->validate([
            'carrera_nombre' => 'required|string|max:100',
            'cuatri_num'     => 'required|integer',
            'materia_nombre' => 'required|string|max:100',
        ]);

        $relacion->update([
            'carrera_nombre' => $request->carrera_nombre,
            'cuatri_num'     => $request->cuatri_num,
            'materia_nombre' => $request->materia_nombre,
        ]);

        return response()->json([
            'message' => 'Relación actualizada correctamente',
            'data'    => $relacion
        ]);
    }

    /**
     * GET /api/carreras/nombres
     * Devuelve todas las carreras distintas en orden alfabético
     */
    public function carrerasAlfabetico()
    {
        $carreras = MatCuatriCar::select('carrera_nombre')
            ->distinct()
            ->orderBy('carrera_nombre', 'asc')
            ->get();

        return response()->json($carreras);
    }

    /**
     * GET /api/carreras/por-periodo/{num}
     * Devuelve las carreras disponibles para un número de periodo (cuatrimestre)
     */
    public function carrerasPorPeriodo($num)
    {
        $carreras = MatCuatriCar::where('cuatri_num', $num)
            ->select('carrera_nombre')
            ->distinct()
            ->orderBy('carrera_nombre', 'asc')
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
            ->orderBy('materia_nombre', 'asc')
            ->get();

        return response()->json($materias);
    }
}
