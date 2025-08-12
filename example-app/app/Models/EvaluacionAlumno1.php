<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Alumno;
use App\Models\Relacion;
use App\Models\RespuestaEvaluacionalum;

class EvaluacionAlumno1 extends Model
{
    protected $table = 'evaluaciones_alumnos1';
    protected $primaryKey = 'id_evaluacion';
    public $timestamps = false;

    protected $fillable = ['id_alumno', 'relacion_id', 'fecha'];

    public function alumno()    { return $this->belongsTo(Alumno::class, 'id_alumno'); }
    public function relacion()  { return $this->belongsTo(Relacion::class, 'relacion_id'); }
    public function respuestas(){ return $this->hasMany(RespuestaEvaluacionalum::class, 'id_evaluacion'); }

    public function scopeDeProfesor($q, $profesorId)
    {
        return $q->where('relacion_id', $profesorId);
    }

    /**
     * Promedio de calificaciones de alumnos para un profesor.
     * - Si existe columna e.calificacion en evaluaciones_alumnos1 => AVG directo.
     * - Si NO existe: promedia desde respuestas_evaluacionalum.calificacion (TEXT) casteando a DECIMAL.
     */
    public static function promedioPorProfesor(int $profesorId): ?float
{
    $tablaEval = 'evaluaciones_alumnos1';
    $tablaResp = 'respuestas_evaluacionalum';

    // 1) Obtener los relacion_id del profesor (prioriza 'relacions')
    $relacionIds = [];

    if (\Schema::hasTable('relacions') && \Schema::hasColumn('relacions', 'profesor_id')) {
        $pkRel = \Schema::hasColumn('relacions', 'id_relacion') ? 'id_relacion'
               : (\Schema::hasColumn('relacions', 'id') ? 'id' : null);

        if ($pkRel) {
            $relacionIds = \DB::table('relacions')
                ->where('profesor_id', $profesorId)
                ->pluck($pkRel)
                ->all();
        }
    } elseif (\Schema::hasTable('relaciones') && \Schema::hasColumn('relaciones', 'profesor_id')) {
        $pkRel = \Schema::hasColumn('relaciones', 'id_relacion') ? 'id_relacion'
               : (\Schema::hasColumn('relaciones', 'id') ? 'id' : null);

        if ($pkRel) {
            $relacionIds = \DB::table('relaciones')
                ->where('profesor_id', $profesorId)
                ->pluck($pkRel)
                ->all();
        }
    }

    // Compatibilidad: si no hallamos relaciones, intenta el “truco” relacion_id = profesorId
    if (empty($relacionIds)) {
        $existenConMismoId = \DB::table($tablaEval)
            ->where('relacion_id', $profesorId)
            ->exists();

        if ($existenConMismoId) {
            $relacionIds = [$profesorId];
        } else {
            return null; // no hay de dónde promediar
        }
    }

    // 2) ¿Existe columna calificacion directa en evaluaciones_alumnos1?
    $tieneCalifDirecta = \Schema::hasColumn($tablaEval, 'calificacion');

    if ($tieneCalifDirecta) {
        $avg = \DB::table($tablaEval)
            ->whereIn('relacion_id', $relacionIds)
            ->avg('calificacion');
        return is_null($avg) ? null : round((float)$avg, 2);
    }

    // 3) Promediar desde respuestas_evaluacionalum.calificacion (TEXT → DECIMAL)
    if (
        \Schema::hasTable($tablaResp) &&
        \Schema::hasColumn($tablaResp, 'id_evaluacion') &&
        \Schema::hasColumn($tablaResp, 'calificacion') &&
        \Schema::hasColumn($tablaEval, 'id_evaluacion') &&
        \Schema::hasColumn($tablaEval, 'relacion_id')
    ) {
        $avg = \DB::table($tablaResp . ' as r')
            ->join($tablaEval . ' as e', 'e.id_evaluacion', '=', 'r.id_evaluacion')
            ->whereIn('e.relacion_id', $relacionIds)
            ->whereNotNull('r.calificacion')
            // acepta "8", "8.5", "8,5" (con punto o coma, con espacios)
            ->whereRaw("TRIM(r.calificacion) REGEXP '^-?[0-9]+([.,][0-9]+)?$'")
            ->avg(\DB::raw("CAST(REPLACE(TRIM(r.calificacion), ',', '.') AS DECIMAL(10,4))"));

        return is_null($avg) ? null : round((float)$avg, 2);
    }

    return null;
}


}
