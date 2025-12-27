<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Tarea extends Model
{
    protected $table = 'tareas';
    protected $fillable = [
        'actividad_id',
        'nombre',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'prioridad',
        'responsable_id',
        'user_id' // Para tareas independientes (asignadas directamente al usuario)
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    // Relación con usuario para tareas independientes
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Verificar si es una tarea independiente (sin actividad)
    public function esIndependiente(): bool
    {
        return is_null($this->actividad_id);
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(Evidencia::class);
    }

    // Relación con comentarios
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'tarea_id');
    }

    // Relación con usuarios asignados (muchos a muchos)
    public function usuariosAsignados(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'tarea_usuario',
            'tarea_id',
            'user_id'
        )->withTimestamps();
    }

    // Relación con responsable (uno a uno - para compatibilidad)
    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /** Listar tareas de una actividad */
    public static function listarPorActividad(int $actividadId, int $perPage = 10)
    {
        return static::where('actividad_id', $actividadId)
            ->sinEliminar()
            ->with(['evidencias', 'usuariosAsignados'])
            ->paginate($perPage);
    }

    /** Obtener todas las tareas de una actividad con relaciones */
    public static function obtenerPorActividad(int $actividadId)
    {
        return static::where('actividad_id', $actividadId)
            ->sinEliminar()
            ->with(['usuariosAsignados', 'evidencias'])
            ->get();
    }

    /** Preparar datos para crear una tarea */
    public static function prepararDatosCreacion(array $data, int $userId): array
    {
        $tareaData = [
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'prioridad' => $data['prioridad'] ?? 'media',
            'estado' => $data['estado'] ?? 'pendiente',
        ];

        // Si no se proporciona fecha_inicio, usar la fecha/hora actual automáticamente
        if (empty($data['fecha_inicio'])) {
            $tareaData['fecha_inicio'] = now()->format('Y-m-d H:i:s');
        } else {
            // Si se proporciona fecha_inicio, usar hora 00:00:00
            $tareaData['fecha_inicio'] = $data['fecha_inicio'] . ' 00:00:00';
        }
        
        // Combinar fecha_fin con hora_fin si están presentes
        if (!empty($data['fecha_fin']) && !empty($data['hora_fin'])) {
            $tareaData['fecha_fin'] = $data['fecha_fin'] . ' ' . $data['hora_fin'] . ':59';
        } elseif (!empty($data['fecha_fin'])) {
            $tareaData['fecha_fin'] = $data['fecha_fin'] . ' 23:59:59';
        }

        // Si es tarea independiente, asignar al usuario de sesión y NO incluir actividad_id
        if (!empty($data['es_independiente']) || empty($data['actividad_id'])) {
            $tareaData['user_id'] = $userId;
            // No incluir actividad_id para tareas independientes
        } else {
            // Si tiene actividad_id, incluirlo
            $tareaData['actividad_id'] = $data['actividad_id'];
        }

        return $tareaData;
    }

    /** Crear tarea con usuarios asignados */
    public static function crearConUsuarios(array $datosTarea, ?array $usuariosIds = null): self
    {
        return DB::transaction(function () use ($datosTarea, $usuariosIds) {
            // Preparar datos para crear, solo incluir actividad_id si está presente
            $createData = [
                'nombre' => $datosTarea['nombre'],
                'descripcion' => $datosTarea['descripcion'] ?? null,
                'fecha_inicio' => $datosTarea['fecha_inicio'] ?? null,
                'fecha_fin' => $datosTarea['fecha_fin'] ?? null,
                'prioridad' => $datosTarea['prioridad'] ?? 'media',
                'estado' => $datosTarea['estado'] ?? 'pendiente',
            ];

            // Solo incluir actividad_id si está presente en el array
            if (isset($datosTarea['actividad_id'])) {
                $createData['actividad_id'] = $datosTarea['actividad_id'];
            }

            // Solo incluir user_id si está presente en el array
            if (isset($datosTarea['user_id'])) {
                $createData['user_id'] = $datosTarea['user_id'];
            }

            $tarea = static::create($createData);

            if (!empty($usuariosIds)) {
                $tarea->usuariosAsignados()->sync($usuariosIds);
            }

            return $tarea->load('usuariosAsignados');
        });
    }

    /** Obtener tareas independientes de un usuario (solo pendientes) */
    public static function obtenerIndependientesPorUsuario(int $userId)
    {
        return static::whereNull('actividad_id')
            ->where('user_id', $userId)
            ->sinEliminar()
            ->where('estado', '!=', 'completado')
            ->with(['usuariosAsignados', 'usuario'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /** Obtener todas las tareas del usuario (con y sin proyecto) - excluye tareas independientes y completadas */
    public static function obtenerTodasPorUsuario(int $userId)
    {
        return static::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereHas('usuariosAsignados', function($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        })
        ->sinEliminar()
        ->whereNotNull('actividad_id') // Excluir tareas independientes
        ->where('estado', '!=', 'completado')
        ->with(['actividad.proyecto', 'usuariosAsignados', 'usuario'])
        ->orderBy('fecha_fin', 'asc')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /** Obtener tareas próximas a vencer - excluye tareas independientes */
    public static function obtenerProximasAVencer(int $userId, int $dias = 7)
    {
        $fechaLimite = now()->addDays($dias);
        
        return static::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereHas('usuariosAsignados', function($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        })
        ->sinEliminar()
        ->whereNotNull('actividad_id') // Excluir tareas independientes
        ->where('estado', '!=', 'completado')
        ->whereNotNull('fecha_fin')
        ->where('fecha_fin', '>=', now()) // Comparación datetime completa
        ->where('fecha_fin', '<=', $fechaLimite)
        ->with(['actividad.proyecto', 'usuariosAsignados', 'usuario'])
        ->orderBy('fecha_fin', 'asc')
        ->get();
    }

    /** Obtener tareas vencidas - considera fecha y hora completa - excluye tareas independientes */
    public static function obtenerVencidas(int $userId)
    {
        return static::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereHas('usuariosAsignados', function($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        })
        ->sinEliminar()
        ->whereNotNull('actividad_id') // Excluir tareas independientes
        ->where('estado', '!=', 'completado')
        ->whereNotNull('fecha_fin')
        ->where('fecha_fin', '<', now()) // Comparación datetime completa (solo vencidas si fecha y hora ya pasaron)
        ->with(['actividad.proyecto', 'usuariosAsignados', 'usuario'])
        ->orderBy('fecha_fin', 'asc')
        ->get();
    }

    /** Obtener últimas tareas del usuario (limitadas) */
    public static function obtenerUltimasPorUsuario(int $userId, int $limite = 10)
    {
        return static::obtenerTodasPorUsuario($userId)->take($limite);
    }

    /** Obtener últimas tareas del usuario paginadas - excluye tareas independientes y completadas */
    public static function obtenerUltimasPorUsuarioPaginadas(int $userId, int $perPage = 5)
    {
        return static::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereHas('usuariosAsignados', function($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        })
        ->sinEliminar()
        ->whereNotNull('actividad_id') // Excluir tareas independientes
        ->where('estado', '!=', 'completado')
        ->with(['actividad.proyecto', 'usuariosAsignados', 'usuario'])
        ->orderBy('fecha_fin', 'asc')
        ->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'ultimas_page');
    }

    /** Obtener tareas próximas a vencer paginadas - excluye tareas independientes */
    public static function obtenerProximasAVencerPaginadas(int $userId, int $dias = 7, int $perPage = 5)
    {
        $fechaLimite = now()->addDays($dias);
        
        return static::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereHas('usuariosAsignados', function($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        })
        ->sinEliminar()
        ->whereNotNull('actividad_id') // Excluir tareas independientes
        ->where('estado', '!=', 'completado')
        ->whereNotNull('fecha_fin')
        ->where('fecha_fin', '>=', now())
        ->where('fecha_fin', '<=', $fechaLimite)
        ->with(['actividad.proyecto', 'usuariosAsignados', 'usuario'])
        ->orderBy('fecha_fin', 'asc')
        ->paginate($perPage, ['*'], 'proximas_page');
    }

    /** Obtener tareas vencidas paginadas - excluye tareas independientes */
    public static function obtenerVencidasPaginadas(int $userId, int $perPage = 5)
    {
        return static::where(function($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhereHas('usuariosAsignados', function($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        })
        ->sinEliminar()
        ->whereNotNull('actividad_id') // Excluir tareas independientes
        ->where('estado', '!=', 'completado')
        ->whereNotNull('fecha_fin')
        ->where('fecha_fin', '<', now())
        ->with(['actividad.proyecto', 'usuariosAsignados', 'usuario'])
        ->orderBy('fecha_fin', 'asc')
        ->paginate($perPage, ['*'], 'vencidas_page');
    }

    /** Preparar datos para actualizar una tarea */
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

    /** Actualizar tarea con usuarios asignados */
    public function actualizarConUsuarios(array $datosTarea, ?array $usuariosIds = null): bool
    {
        return DB::transaction(function () use ($datosTarea, $usuariosIds) {
            $actualizado = $this->update([
                'nombre' => $datosTarea['nombre'],
                'descripcion' => $datosTarea['descripcion'] ?? $this->descripcion,
                'fecha_inicio' => $datosTarea['fecha_inicio'] ?? $this->fecha_inicio,
                'fecha_fin' => $datosTarea['fecha_fin'] ?? $this->fecha_fin,
                'prioridad' => $datosTarea['prioridad'] ?? $this->prioridad,
                'estado' => $datosTarea['estado'] ?? $this->estado,
            ]);

            if ($actualizado && isset($usuariosIds)) {
                $this->usuariosAsignados()->sync($usuariosIds);
            }

            return $actualizado;
        });
    }

    /** Verificar si un usuario está asignado a la tarea */
    public function usuarioAsignado(int $userId): bool
    {
        // Verificar si es el usuario asignado directamente (tareas independientes)
        if ($this->user_id === $userId) {
            return true;
        }
        
        // Verificar si está en la lista de usuarios asignados (relación muchos a muchos)
        return $this->usuariosAsignados()->where('users.id', $userId)->exists();
    }

    /** Actualizar fecha de la tarea */
    public function actualizarFecha(array $data): bool
    {
        // Combinar fecha_fin con hora_fin si están presentes
        if (!empty($data['fecha_fin']) && !empty($data['hora_fin'])) {
            $this->fecha_fin = $data['fecha_fin'] . ' ' . $data['hora_fin'] . ':59';
        } elseif (!empty($data['fecha_fin']) && !isset($data['hora_fin'])) {
            // Si solo hay fecha, mantener la hora existente o usar 23:59:59
            if ($this->fecha_fin) {
                $this->fecha_fin = $data['fecha_fin'] . ' ' . $this->fecha_fin->format('H:i:s');
            } else {
                $this->fecha_fin = $data['fecha_fin'] . ' 23:59:59';
            }
        }

        return $this->save();
    }

    /** Asignar usuarios a la tarea */
    public function asignarUsuarios(array $usuariosIds): void
    {
        $this->usuariosAsignados()->sync($usuariosIds);
    }

    /** Marcar tarea como completada */
    public function completar(): bool
    {
        return $this->update(['estado' => 'completado']);
    }

    /** Marcar tarea como pendiente */
    public function marcarPendiente(): bool
    {
        return $this->update(['estado' => 'pendiente']);
    }

    /** Alternar estado completado/pendiente */
    public function toggleCompletada(): bool
    {
        if ($this->estaCompletada()) {
            return $this->marcarPendiente();
        }
        return $this->completar();
    }

    /** Verificar si la tarea está completada */
    public function estaCompletada(): bool
    {
        return $this->estado === 'completado';
    }

    /** Obtener color de prioridad */
    public function getColorPrioridadAttribute(): string
    {
        return match($this->prioridad) {
            'alta' => '#DC3545', // Rojo
            'media' => '#0D6EFD', // Azul
            'baja' => '#6C757D', // Gris
            default => '#6C757D'
        };
    }

    /** Obtener color de estado */
    public function getColorEstadoAttribute(): string
    {
        return match($this->estado) {
            'completado' => '#198754', // Verde
            'en_progreso' => '#FFC107', // Amarillo/Naranja
            'pendiente' => '#6C757D', // Gris
            'eliminado' => '#DC3545', // Rojo
            default => '#6C757D'
        };
    }

    /**
     * Scope para excluir tareas eliminadas (soft delete)
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
     * Restaurar tarea eliminada
     */
    public function restaurar($nuevoEstado = 'pendiente'): bool
    {
        if ($this->estaEliminada()) {
            return $this->update(['estado' => $nuevoEstado]);
        }
        return false;
    }

    /**
     * Verificar si la tarea está vencida (considera fecha y hora)
     */
    public function estaVencida(): bool
    {
        if ($this->estado === 'completado' || $this->estado === 'eliminado' || !$this->fecha_fin) {
            return false;
        }
        return $this->fecha_fin->isPast(); // Compara fecha y hora completa
    }

    /**
     * Cambiar el estado entre pendiente y en_progreso
     * Solo permitido para usuarios asignados a la tarea
     */
    public function cambiarEstadoPendienteEnProgreso(int $userId): bool
    {
        // Verificar que el usuario esté asignado a la tarea
        $usuarioAsignado = false;
        
        // Verificar si es el usuario asignado directamente (tareas independientes)
        if ($this->user_id === $userId) {
            $usuarioAsignado = true;
        }
        
        // Verificar si está en la lista de usuarios asignados (relación muchos a muchos)
        if (!$usuarioAsignado && $this->relationLoaded('usuariosAsignados')) {
            $usuarioAsignado = $this->usuariosAsignados->contains('id', $userId);
        } elseif (!$usuarioAsignado) {
            // Si no está cargada la relación, hacer la consulta
            $usuarioAsignado = $this->usuariosAsignados()->where('users.id', $userId)->exists();
        }
        
        if (!$usuarioAsignado) {
            return false;
        }
        
        // Solo permitir cambiar entre pendiente y en_progreso
        if ($this->estado === 'pendiente') {
            return $this->update(['estado' => 'en_progreso']);
        } elseif ($this->estado === 'en_progreso') {
            return $this->update(['estado' => 'pendiente']);
        }
        
        return false;
    }

    public function actualizarDescripcion(string $nuevaDescripcion): bool
    {
        return $this->update(['descripcion' => $nuevaDescripcion]);
    }

    /**
     * Agregar un comentario a la tarea
     */
    public function agregarComentario(string $comentario, int $userId): Comentario
    {
        return Comentario::create([
            'tarea_id' => $this->id,
            'user_id' => $userId,
            'comentario' => $comentario
        ]);
    }

    /**
     * Obtener descripción de la tarea mediante consulta SQL
     */
    public static function obtenerDescripcion(int $tareaId): ?string
    {
        $resultado = DB::table('tareas')
            ->select('descripcion')
            ->where('id', $tareaId)
            ->where('estado', '!=', 'eliminado')
            ->first();

        return $resultado ? $resultado->descripcion : null;
    }

}
