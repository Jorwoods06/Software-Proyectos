<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Evidencia extends Model
{
    use HasFactory;

    protected $table = 'evidencias';

    protected $fillable = [
        'tarea_id',
        'archivo',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // La tabla solo tiene created_at, no updated_at
    public $timestamps = true;
    const UPDATED_AT = null;

    // Relación con Tarea
    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tarea_id');
    }

    /**
     * Crear evidencia mediante consulta SQL
     */
    public static function crearEvidencia(int $tareaId, string $rutaArchivo, string $tipoArchivo): int
    {
        return DB::table('evidencias')->insertGetId([
            'tarea_id' => $tareaId,
            'archivo' => $rutaArchivo,
            'tipo' => $tipoArchivo,
            'created_at' => now()
        ]);
    }

    /**
     * Obtener evidencias de una tarea mediante consulta SQL
     */
    public static function obtenerPorTarea(int $tareaId): array
    {
        return DB::table('evidencias')
            ->select('id', 'tarea_id', 'archivo', 'tipo', 'created_at')
            ->where('tarea_id', $tareaId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($evidencia) {
                return [
                    'id' => $evidencia->id,
                    'tarea_id' => $evidencia->tarea_id,
                    'archivo' => $evidencia->archivo,
                    'tipo' => $evidencia->tipo,
                    'nombre_archivo' => basename($evidencia->archivo),
                    'created_at' => $evidencia->created_at,
                    'created_at_formatted' => \Carbon\Carbon::parse($evidencia->created_at)->format('d/m/Y g:i A')
                ];
            })
            ->toArray();
    }

    /**
     * Obtener evidencia por ID mediante consulta SQL
     */
    public static function obtenerPorId(int $evidenciaId): ?object
    {
        return DB::table('evidencias')
            ->select('id', 'tarea_id', 'archivo', 'tipo', 'created_at')
            ->where('id', $evidenciaId)
            ->first();
    }

    /**
     * Eliminar evidencia mediante consulta SQL
     */
    public static function eliminarEvidencia(int $evidenciaId): bool
    {
        return DB::table('evidencias')
            ->where('id', $evidenciaId)
            ->delete() > 0;
    }

    /**
     * Verificar si la evidencia existe
     */
    public static function existe(int $evidenciaId): bool
    {
        return DB::table('evidencias')
            ->where('id', $evidenciaId)
            ->exists();
    }

    /**
     * Obtener URL temporal firmada desde S3
     * @param string $rutaArchivo Ruta del archivo en S3
     * @param int $minutos Minutos de validez de la URL (default: 60)
     * @return string|null URL temporal o null si hay error
     */
    public static function obtenerUrlTemporal(string $rutaArchivo, int $minutos = 60): ?string
    {
        try {
            // Verificar que la ruta no esté vacía
            if (empty($rutaArchivo)) {
                Log::error('Ruta de archivo vacía al obtener URL temporal');
                return null;
            }
            
            // Generar URL temporal directamente (sin verificar existencia)
            // La verificación de existencia puede fallar si hay problemas de configuración
            // pero la URL temporal puede generarse igualmente
            return Storage::disk('s3')->temporaryUrl($rutaArchivo, now()->addMinutes($minutos));
        } catch (\Exception $e) {
            // Si hay error al generar URL, retornar null
            Log::error('Error obteniendo URL temporal de evidencia: ' . $e->getMessage() . ' - Ruta: ' . $rutaArchivo);
            return null;
        }
    }
}
