<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RespuestaEvaluacion;
use App\Models\Profesor;

class EvaluacionProfesor extends Model
{
    // Tabla actual (no cambiamos nada)
    protected $table = 'evaluaciones_profesores';

    // Columnas “fillable” (tal cual las tenías)
    protected $fillable = [
        'profesor_id',
        'evaluador_id',
        'tipo',
        'periodo',
        'calif_i',
        'calif_ii',
        'calificacion_final',
        'comentario'
    ];

    // La tabla sí maneja timestamps
    public $timestamps = true;

    // Aseguramos tipos numéricos (no rompe nada)
    protected $casts = [
        'calif_i'            => 'float',
        'calif_ii'           => 'float',
        'calificacion_final' => 'float',
    ];

    /* =================== Relaciones (igual que ya tenías) =================== */
    public function respuestas()
    {
        return $this->hasMany(RespuestaEvaluacion::class, 'evaluacion_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Profesor::class, 'profesor_id');
    }

    /* ===================== NUEVO: Scopes & Helper ===================== */

    /** Scope: filtra por profesor */
    public function scopeDeProfesor($query, int $profesorId)
    {
        return $query->where('profesor_id', $profesorId);
    }

    /** Scope: última por created_at (desc) */
    public function scopeUltima($query)
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Helper: devuelve la calificación_final más reciente del admin
     * para el profesor dado. Retorna float|null.
     */
    public static function calificacionAdminPorProfesor(int $profesorId): ?float
    {
        $valor = static::query()
            ->deProfesor($profesorId)
            ->ultima()
            ->value('calificacion_final'); // float|string|null

        return is_null($valor) ? null : round((float) $valor, 2);
    }
}
