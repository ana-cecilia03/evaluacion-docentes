<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\EvaluacionAlumno1;
use App\Models\EvaluacionProfesor;
use App\Models\Profesor;

class PuntajeFinalController extends Controller
{
    /** Utilidad: limita a [0,10] y redondea a 2 decimales */
    private function clamp10(?float $n): ?float
    {
        if ($n === null) return null;
        $n = max(0, min(10, $n));
        return round($n, 2);
    }

    /**
     * GET /api/admin/reportes/puntaje-final/{profesorId}
     * - Alumno: 0–10
     * - Admin: 1–5 -> 0–10 (×2)
     * - Total: promedio 50/50 (0–10)
     * - Todo se limita a 10 por seguridad.
     */
    public function show(int $profesorId): JsonResponse
    {
        $profesor = Profesor::select('id_profesor', 'nombre_completo')->find($profesorId);
        if (!$profesor) {
            return response()->json(['message' => 'Profesor no encontrado.'], 404);
        }

        $a = EvaluacionAlumno1::promedioPorProfesor($profesorId);      // 0–10 (teórico)
        $b = EvaluacionProfesor::calificacionAdminPorProfesor($profesorId); // 1–5

        $alumnos10 = $this->clamp10($a === null ? null : (float) $a);
        $admin10   = $this->clamp10($b === null ? null : ((float) $b) * 2);

        $total = (is_null($alumnos10) || is_null($admin10))
            ? null
            : $this->clamp10(($alumnos10 + $admin10) / 2);

        return response()->json([
            'profesor_id'        => $profesor->id_profesor,
            'profesor'           => $profesor->nombre_completo,
            'promedio_alumnos'   => $alumnos10,   // 0–10, limitado
            'calificacion_admin' => $admin10,     // 0–10, limitado
            'total'              => $total,       // 0–10, limitado
        ]);
    }

    /**
     * GET /api/admin/reportes/puntaje-final
     * Lista todos con los mismos límites [0,10].
     */
    public function index(Request $request): JsonResponse
    {
        $query = Profesor::query()->select('id_profesor', 'nombre_completo');

        $request->filled('status')
            ? $query->where('status', $request->string('status'))
            : $query->where('status', 'activo');

        $profes = $query->orderBy('nombre_completo')->get();

        $data = $profes->map(function ($p) {
            $a = EvaluacionAlumno1::promedioPorProfesor($p->id_profesor);       // 0–10
            $b = EvaluacionProfesor::calificacionAdminPorProfesor($p->id_profesor); // 1–5

            $alumnos10 = $this->clamp10($a === null ? null : (float) $a);
            $admin10   = $this->clamp10($b === null ? null : ((float) $b) * 2);

            $total = (is_null($alumnos10) || is_null($admin10))
                ? null
                : $this->clamp10(($alumnos10 + $admin10) / 2);

            return [
                'profesor_id'        => $p->id_profesor,
                'profesor'           => $p->nombre_completo,
                'promedio_alumnos'   => $alumnos10,  // 0–10
                'calificacion_admin' => $admin10,    // 0–10
                'total'              => $total,      // 0–10
            ];
        });

        return response()->json($data);
    }
}
