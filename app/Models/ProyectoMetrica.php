<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para métricas de proyectos
 * Este modelo contiene ÚNICAMENTE consultas a la base de datos
 * Sin lógica de negocio
 */
class ProyectoMetrica extends Model
{
    // Este modelo no tiene tabla física, solo métodos estáticos para consultas

    /**
     * Obtener información básica del proyecto
     */
    public static function obtenerInformacionBasica(int $proyectoId): ?object
    {
        return DB::table('proyectos')
            ->select('id', 'nombre', 'descripcion', 'estado', 'departamento_id', 'created_by', 
                     'fecha_inicio', 'fecha_fin', 'color', 'created_at', 'updated_at')
            ->where('id', $proyectoId)
            ->first();
    }

    /**
     * Obtener colaboradores y sus roles del proyecto
     */
    public static function obtenerColaboradores(int $proyectoId): array
    {
        return DB::table('proyecto_usuario')
            ->join('users', 'proyecto_usuario.user_id', '=', 'users.id')
            ->leftJoin('departamentos', 'users.departamento', '=', 'departamentos.id')
            ->select('users.id', 'users.nombre', 'users.email', 
                     'proyecto_usuario.rol_proyecto', 'proyecto_usuario.created_at',
                     'departamentos.nombre as departamento_nombre')
            ->where('proyecto_usuario.proyecto_id', $proyectoId)
            ->get()
            ->toArray();
    }

    /**
     * Obtener total de actividades por estado
     */
    public static function obtenerActividadesPorEstado(int $proyectoId): array
    {
        return DB::table('actividades')
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->where('proyecto_id', $proyectoId)
            ->groupBy('estado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener actividades vencidas
     */
    public static function obtenerActividadesVencidas(int $proyectoId): array
    {
        return DB::table('actividades')
            ->select('id', 'nombre', 'fecha_fin', 'estado')
            ->where('proyecto_id', $proyectoId)
            ->where('fecha_fin', '<', now())
            ->where('estado', '!=', 'finalizado')
            ->where('estado', '!=', 'eliminado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener actividades próximas a vencer (próximos 7 días)
     */
    public static function obtenerActividadesProximasAVencer(int $proyectoId, int $dias = 7): array
    {
        $fechaLimite = now()->addDays($dias);
        return DB::table('actividades')
            ->select('id', 'nombre', 'fecha_fin', 'estado')
            ->where('proyecto_id', $proyectoId)
            ->where('fecha_fin', '>=', now())
            ->where('fecha_fin', '<=', $fechaLimite)
            ->where('estado', '!=', 'finalizado')
            ->where('estado', '!=', 'eliminado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener estadísticas de duración de actividades
     */
    public static function obtenerDuracionActividades(int $proyectoId): ?object
    {
        return DB::table('actividades')
            ->select(
                DB::raw('AVG(DATEDIFF(COALESCE(fecha_fin, NOW()), fecha_inicio)) as promedio_dias'),
                DB::raw('MIN(DATEDIFF(COALESCE(fecha_fin, NOW()), fecha_inicio)) as minimo_dias'),
                DB::raw('MAX(DATEDIFF(COALESCE(fecha_fin, NOW()), fecha_inicio)) as maximo_dias')
            )
            ->where('proyecto_id', $proyectoId)
            ->whereNotNull('fecha_inicio')
            ->first();
    }

    /**
     * Obtener total de tareas por estado (excluyendo independientes)
     */
    public static function obtenerTareasPorEstado(int $proyectoId): array
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select('tareas.estado', DB::raw('COUNT(*) as total'))
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->groupBy('tareas.estado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener total de tareas por prioridad (excluyendo independientes)
     */
    public static function obtenerTareasPorPrioridad(int $proyectoId): array
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select('tareas.prioridad', DB::raw('COUNT(*) as total'))
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->groupBy('tareas.prioridad')
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas vencidas
     */
    public static function obtenerTareasVencidas(int $proyectoId): array
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select('tareas.id', 'tareas.nombre', 'tareas.fecha_fin', 'tareas.estado', 'tareas.prioridad')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->whereNotNull('tareas.fecha_fin')
            ->where('tareas.fecha_fin', '<', now())
            ->where('tareas.estado', '!=', 'completado')
            ->where('tareas.estado', '!=', 'eliminado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas próximas a vencer (próximos 7 días)
     */
    public static function obtenerTareasProximasAVencer(int $proyectoId, int $dias = 7): array
    {
        $fechaLimite = now()->addDays($dias);
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select('tareas.id', 'tareas.nombre', 'tareas.fecha_fin', 'tareas.estado', 'tareas.prioridad')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->whereNotNull('tareas.fecha_fin')
            ->where('tareas.fecha_fin', '>=', now())
            ->where('tareas.fecha_fin', '<=', $fechaLimite)
            ->where('tareas.estado', '!=', 'completado')
            ->where('tareas.estado', '!=', 'eliminado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas por usuario asignado
     */
    public static function obtenerTareasPorUsuario(int $proyectoId): array
    {
        return DB::table('tarea_usuario')
            ->join('tareas', 'tarea_usuario.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->join('users', 'tarea_usuario.user_id', '=', 'users.id')
            ->select(
                'users.id as usuario_id',
                'users.nombre as usuario_nombre',
                DB::raw('COUNT(*) as total_tareas'),
                DB::raw('SUM(CASE WHEN tareas.estado = "completado" THEN 1 ELSE 0 END) as tareas_completadas'),
                DB::raw('SUM(CASE WHEN tareas.estado = "pendiente" THEN 1 ELSE 0 END) as tareas_pendientes'),
                DB::raw('SUM(CASE WHEN tareas.estado = "en_progreso" THEN 1 ELSE 0 END) as tareas_en_progreso'),
                DB::raw('SUM(CASE WHEN tareas.fecha_fin < NOW() AND tareas.estado != "completado" AND tareas.estado != "eliminado" THEN 1 ELSE 0 END) as tareas_vencidas')
            )
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->groupBy('users.id', 'users.nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener estadísticas de duración de tareas
     */
    public static function obtenerDuracionTareas(int $proyectoId): ?object
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select(
                DB::raw('AVG(DATEDIFF(COALESCE(tareas.fecha_fin, NOW()), tareas.fecha_inicio)) as promedio_dias'),
                DB::raw('MIN(DATEDIFF(COALESCE(tareas.fecha_fin, NOW()), tareas.fecha_inicio)) as minimo_dias'),
                DB::raw('MAX(DATEDIFF(COALESCE(tareas.fecha_fin, NOW()), tareas.fecha_inicio)) as maximo_dias')
            )
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->whereNotNull('tareas.fecha_inicio')
            ->where('tareas.estado', '!=', 'eliminado')
            ->first();
    }

    /**
     * Obtener tiempo promedio de completar tareas (desde creación hasta completado)
     */
    public static function obtenerTiempoPromedioCompletadoTareas(int $proyectoId): ?object
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select(
                DB::raw('AVG(DATEDIFF(tareas.updated_at, tareas.created_at)) as promedio_dias_completado')
            )
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->where('tareas.updated_at', '>=', DB::raw('tareas.created_at'))
            ->first();
    }

    /**
     * Obtener estadísticas de evidencias
     */
    public static function obtenerEstadisticasEvidencias(int $proyectoId): ?object
    {
        $resultado = DB::table('evidencias')
            ->join('tareas', 'evidencias.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select(
                DB::raw('COUNT(*) as total_evidencias'),
                DB::raw('COUNT(DISTINCT tareas.id) as tareas_con_evidencias')
            )
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->first();

        // Obtener tareas completadas con evidencias por separado
        $tareasCompletadasConEvidencias = DB::table('evidencias')
            ->join('tareas', 'evidencias.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->distinct('tareas.id')
            ->count('tareas.id');

        if ($resultado) {
            $resultado->tareas_completadas_con_evidencias = $tareasCompletadasConEvidencias;
        }

        return $resultado;
    }

    /**
     * Obtener total de tareas completadas sin evidencias
     */
    public static function obtenerTareasCompletadasSinEvidencias(int $proyectoId): int
    {
        $tareasCompletadas = DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->count();

        $tareasCompletadasConEvidencias = DB::table('evidencias')
            ->join('tareas', 'evidencias.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->distinct('tareas.id')
            ->count('tareas.id');

        return max(0, $tareasCompletadas - $tareasCompletadasConEvidencias);
    }

    /**
     * Obtener estadísticas de comentarios
     */
    public static function obtenerEstadisticasComentarios(int $proyectoId): ?object
    {
        return DB::table('comentarios')
            ->join('tareas', 'comentarios.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select(
                DB::raw('COUNT(*) as total_comentarios'),
                DB::raw('COUNT(DISTINCT tareas.id) as tareas_con_comentarios'),
                DB::raw('COUNT(DISTINCT comentarios.user_id) as usuarios_comentando')
            )
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->first();
    }

    /**
     * Obtener acciones de trazabilidad del proyecto
     */
    public static function obtenerTrazabilidad(int $proyectoId, int $limite = 10): array
    {
        return DB::table('trazabilidad')
            ->join('users', 'trazabilidad.user_id', '=', 'users.id')
            ->select('trazabilidad.id', 'trazabilidad.accion', 'trazabilidad.detalle', 
                     'trazabilidad.fecha', 'users.nombre as usuario_nombre')
            ->where('trazabilidad.proyecto_id', $proyectoId)
            ->orderBy('trazabilidad.fecha', 'desc')
            ->limit($limite)
            ->get()
            ->toArray();
    }

    /**
     * Obtener fechas reales del proyecto (primera actividad iniciada, última actividad finalizada)
     */
    public static function obtenerFechasRealesProyecto(int $proyectoId): ?object
    {
        return DB::table('actividades')
            ->select(
                DB::raw('MIN(fecha_inicio) as fecha_inicio_real'),
                DB::raw('MAX(CASE WHEN estado = "finalizado" THEN fecha_fin END) as fecha_fin_real')
            )
            ->where('proyecto_id', $proyectoId)
            ->whereNotNull('fecha_inicio')
            ->first();
    }

    /**
     * Obtener total de tareas sin asignar
     */
    public static function obtenerTareasSinAsignar(int $proyectoId): int
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->leftJoin('tarea_usuario', 'tareas.id', '=', 'tarea_usuario.tarea_id')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->whereNull('tarea_usuario.tarea_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->distinct('tareas.id')
            ->count('tareas.id');
    }

    /**
     * Obtener distribución de colaboradores por departamento
     */
    public static function obtenerColaboradoresPorDepartamento(int $proyectoId): array
    {
        return DB::table('proyecto_usuario')
            ->join('users', 'proyecto_usuario.user_id', '=', 'users.id')
            ->leftJoin('departamentos', 'users.departamento', '=', 'departamentos.id')
            ->select('departamentos.id as departamento_id', 'departamentos.nombre as departamento_nombre',
                     DB::raw('COUNT(*) as total_colaboradores'))
            ->where('proyecto_usuario.proyecto_id', $proyectoId)
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener usuario creador del proyecto
     */
    public static function obtenerCreadorProyecto(int $proyectoId): ?object
    {
        return DB::table('proyectos')
            ->join('users', 'proyectos.created_by', '=', 'users.id')
            ->select('users.id', 'users.nombre', 'users.email')
            ->where('proyectos.id', $proyectoId)
            ->first();
    }

    /**
     * Obtener departamento del proyecto
     */
    public static function obtenerDepartamentoProyecto(int $proyectoId): ?object
    {
        return DB::table('proyectos')
            ->join('departamentos', 'proyectos.departamento_id', '=', 'departamentos.id')
            ->select('departamentos.id', 'departamentos.nombre')
            ->where('proyectos.id', $proyectoId)
            ->first();
    }

    /**
     * Obtener total de actividades
     */
    public static function obtenerTotalActividades(int $proyectoId): int
    {
        return DB::table('actividades')
            ->where('proyecto_id', $proyectoId)
            ->where('estado', '!=', 'eliminado')
            ->count();
    }

    /**
     * Obtener total de tareas (excluyendo independientes)
     */
    public static function obtenerTotalTareas(int $proyectoId): int
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->where('actividades.proyecto_id', $proyectoId)
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->count();
    }
}

