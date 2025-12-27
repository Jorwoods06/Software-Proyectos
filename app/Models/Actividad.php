<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Actividad extends Model
{
    protected $table = 'actividades';
    protected $fillable = ['proyecto_id','nombre','descripcion','fecha_inicio','fecha_fin','estado'];
    
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    /** Preparar datos para crear una actividad */
    public static function prepararDatosCreacion(array $data): array
    {
        // Si no se proporciona fecha_inicio, usar la fecha/hora actual
        if (empty($data['fecha_inicio'])) {
            $data['fecha_inicio'] = now()->format('Y-m-d H:i:s');
        } else {
            // Si se proporciona fecha_inicio, usar hora 00:00:00
            $data['fecha_inicio'] = $data['fecha_inicio'] . ' 00:00:00';
        }
        
        // Combinar fecha_fin con hora_fin si están presentes
        if (!empty($data['fecha_fin']) && !empty($data['hora_fin'])) {
            $data['fecha_fin'] = $data['fecha_fin'] . ' ' . $data['hora_fin'] . ':59';
        } elseif (!empty($data['fecha_fin'])) {
            $data['fecha_fin'] = $data['fecha_fin'] . ' 23:59:59';
        }

        // Eliminar campo hora_fin del array antes de crear
        unset($data['hora_fin']);

        return $data;
    }

    /** Preparar datos para actualizar una actividad */
    public function prepararDatosActualizacion(array $data): array
    {
        // Si no se proporciona fecha_inicio, mantener la existente o usar la actual
        if (empty($data['fecha_inicio'])) {
            if ($this->fecha_inicio) {
                // Mantener la fecha_inicio existente
                unset($data['fecha_inicio']);
            } else {
                // Si no hay fecha_inicio existente, usar la actual
                $data['fecha_inicio'] = now()->format('Y-m-d H:i:s');
            }
        } else {
            // Si se proporciona fecha_inicio, mantener la hora existente o usar 00:00:00
            if ($this->fecha_inicio) {
                $data['fecha_inicio'] = $data['fecha_inicio'] . ' ' . $this->fecha_inicio->format('H:i:s');
            } else {
                $data['fecha_inicio'] = $data['fecha_inicio'] . ' 00:00:00';
            }
        }
        
        // Combinar fecha_fin con hora_fin si están presentes
        if (!empty($data['fecha_fin']) && !empty($data['hora_fin'])) {
            $data['fecha_fin'] = $data['fecha_fin'] . ' ' . $data['hora_fin'] . ':59';
        } elseif (!empty($data['fecha_fin']) && !isset($data['hora_fin'])) {
            // Si solo hay fecha, mantener la hora existente o usar 23:59:59
            if ($this->fecha_fin) {
                $data['fecha_fin'] = $data['fecha_fin'] . ' ' . $this->fecha_fin->format('H:i:s');
            } else {
                $data['fecha_fin'] = $data['fecha_fin'] . ' 23:59:59';
            }
        }

        // Eliminar campo hora_fin del array antes de actualizar
        unset($data['hora_fin']);

        return $data;
    }

    /** Consulta de actividades por proyecto con paginación */
    public static function listarPorProyecto(int $proyectoId, int $perPage = 10)
    {
        return static::where('proyecto_id', $proyectoId)
            ->sinEliminar()
            ->with('tareas.evidencias')
            ->orderBy('created_at','desc')
            ->paginate($perPage);
    }

    /** Obtener actividades de un proyecto con tareas y usuarios asignados */
    public static function obtenerConTareasPorProyecto(int $proyectoId)
    {
        return static::where('proyecto_id', $proyectoId)
            ->sinEliminar()
            ->withCount(['tareas' => function($query) {
                $query->where('estado', '!=', 'eliminado');
            }])
            ->withCount(['tareas as tareas_pendientes' => function($query) {
                $query->where('estado', '!=', 'completado')
                      ->where('estado', '!=', 'eliminado');
            }])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /** Obtener cantidad de tareas pendientes */
    public function getTareasPendientesAttribute(): int
    {
        return $this->tareas->where('estado', '!=', 'completado')->where('estado', '!=', 'eliminado')->count();
    }

    /** Obtener total de tareas */
    public function getTotalTareasAttribute(): int
    {
        return $this->tareas->where('estado', '!=', 'eliminado')->count();
    }

    /**
     * Scope para excluir actividades eliminadas (soft delete)
     */
    public function scopeSinEliminar($query)
    {
        return $query->where('estado', '!=', 'eliminado');
    }

    /**
     * Soft delete: marcar como eliminada en lugar de eliminar físicamente
     */
    public function eliminar(): bool
    {
        return $this->update(['estado' => 'eliminado']);
    }

    /**
     * Verificar si está eliminada
     */
    public function estaEliminada(): bool
    {
        return $this->estado === 'eliminado';
    }

    /**
     * Restaurar actividad eliminada
     */
    public function restaurar($nuevoEstado = 'pendiente'): bool
    {
        if ($this->estaEliminada()) {
            return $this->update(['estado' => $nuevoEstado]);
        }
        return false;
    }
}
