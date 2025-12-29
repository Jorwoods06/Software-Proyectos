<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Modelo para métricas del dashboard de administrador
 * Este modelo contiene ÚNICAMENTE consultas a la base de datos
 * Sin lógica de negocio
 */
class DashboardMetrica extends Model
{
    // Este modelo no tiene tabla física, solo métodos estáticos para consultas

    /**
     * Obtener totales generales de proyectos
     */
    public static function obtenerTotalesProyectos(): ?object
    {
        return DB::table('proyectos')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN estado = "en_progreso" THEN 1 ELSE 0 END) as activos'),
                DB::raw('SUM(CASE WHEN estado = "finalizado" THEN 1 ELSE 0 END) as finalizados'),
                DB::raw('SUM(CASE WHEN estado = "cancelado" THEN 1 ELSE 0 END) as cancelados'),
                DB::raw('SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes')
            )
            ->first();
    }

    /**
     * Obtener totales generales de usuarios
     */
    public static function obtenerTotalesUsuarios(): ?object
    {
        return DB::table('users')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('COUNT(CASE WHEN estado = "activo" THEN 1 END) as activos'),
                DB::raw('COUNT(CASE WHEN estado = "inactivo" THEN 1 END) as inactivos')
            )
            ->first();
    }

    /**
     * Obtener totales de fases (actividades)
     */
    public static function obtenerTotalesFases(): ?object
    {
        return DB::table('actividades')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN estado = "pendiente" THEN 1 ELSE 0 END) as pendientes'),
                DB::raw('SUM(CASE WHEN estado = "en_progreso" THEN 1 ELSE 0 END) as en_progreso'),
                DB::raw('SUM(CASE WHEN estado = "finalizado" THEN 1 ELSE 0 END) as finalizadas'),
                DB::raw('SUM(CASE WHEN estado = "eliminado" THEN 1 ELSE 0 END) as eliminadas')
            )
            ->where('estado', '!=', 'eliminado')
            ->first();
    }

    /**
     * Obtener totales de tareas
     */
    public static function obtenerTotalesTareas(): ?object
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select(
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN tareas.estado = "pendiente" THEN 1 ELSE 0 END) as pendientes'),
                DB::raw('SUM(CASE WHEN tareas.estado = "en_progreso" THEN 1 ELSE 0 END) as en_progreso'),
                DB::raw('SUM(CASE WHEN tareas.estado = "completado" THEN 1 ELSE 0 END) as completadas'),
                DB::raw('SUM(CASE WHEN tareas.estado = "eliminado" THEN 1 ELSE 0 END) as eliminadas')
            )
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->first();
    }

    /**
     * Obtener proyectos por departamento
     */
    public static function obtenerProyectosPorDepartamento(): array
    {
        return DB::table('proyectos')
            ->join('departamentos', 'proyectos.departamento_id', '=', 'departamentos.id')
            ->select(
                'departamentos.id',
                'departamentos.nombre as departamento_nombre',
                DB::raw('COUNT(*) as total_proyectos'),
                DB::raw('SUM(CASE WHEN proyectos.estado = "en_progreso" THEN 1 ELSE 0 END) as proyectos_activos'),
                DB::raw('SUM(CASE WHEN proyectos.estado = "finalizado" THEN 1 ELSE 0 END) as proyectos_finalizados')
            )
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener estructura completa: Departamentos -> Proyectos -> Fases
     */
    public static function obtenerEstructuraPorDepartamento(?int $departamentoId = null): array
    {
        $query = DB::table('departamentos')
            ->leftJoin('proyectos', 'departamentos.id', '=', 'proyectos.departamento_id')
            ->leftJoin('actividades', 'proyectos.id', '=', 'actividades.proyecto_id')
            ->select(
                'departamentos.id as departamento_id',
                'departamentos.nombre as departamento_nombre',
                'proyectos.id as proyecto_id',
                'proyectos.nombre as proyecto_nombre',
                'proyectos.estado as proyecto_estado',
                'actividades.id as fase_id',
                'actividades.nombre as fase_nombre',
                'actividades.estado as fase_estado'
            );

        if ($departamentoId) {
            $query->where('departamentos.id', $departamentoId);
        }

        return $query->orderBy('departamentos.nombre')
            ->orderBy('proyectos.nombre')
            ->orderBy('actividades.nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener lista de departamentos
     */
    public static function obtenerDepartamentos(): array
    {
        return DB::table('departamentos')
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener proyectos con sus fases por departamento
     */
    public static function obtenerProyectosConFasesPorDepartamento(?int $departamentoId = null): array
    {
        $query = DB::table('proyectos')
            ->join('departamentos', 'proyectos.departamento_id', '=', 'departamentos.id')
            ->select(
                'departamentos.id as departamento_id',
                'departamentos.nombre as departamento_nombre',
                'proyectos.id as proyecto_id',
                'proyectos.nombre as proyecto_nombre',
                'proyectos.estado as proyecto_estado',
                DB::raw('COUNT(DISTINCT actividades.id) as total_fases'),
                DB::raw('SUM(CASE WHEN actividades.estado = "pendiente" AND actividades.estado != "eliminado" THEN 1 ELSE 0 END) as fases_pendientes'),
                DB::raw('SUM(CASE WHEN actividades.estado = "en_progreso" THEN 1 ELSE 0 END) as fases_en_progreso'),
                DB::raw('SUM(CASE WHEN actividades.estado = "finalizado" THEN 1 ELSE 0 END) as fases_finalizadas')
            )
            ->leftJoin('actividades', function($join) {
                $join->on('proyectos.id', '=', 'actividades.proyecto_id')
                     ->where('actividades.estado', '!=', 'eliminado');
            });

        if ($departamentoId) {
            $query->where('departamentos.id', $departamentoId);
        }

        return $query->groupBy('departamentos.id', 'departamentos.nombre', 'proyectos.id', 'proyectos.nombre', 'proyectos.estado')
            ->orderBy('departamentos.nombre')
            ->orderBy('proyectos.nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener fases por proyecto
     */
    public static function obtenerFasesPorProyecto(int $proyectoId): array
    {
        return DB::table('actividades')
            ->select(
                'id',
                'nombre',
                'estado',
                'fecha_inicio',
                'fecha_fin'
            )
            ->where('proyecto_id', $proyectoId)
            ->where('estado', '!=', 'eliminado')
            ->orderBy('nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener proyectos vencidos o en riesgo
     */
    public static function obtenerProyectosEnRiesgo(): array
    {
        return DB::table('proyectos')
            ->select('id', 'nombre', 'fecha_fin', 'estado')
            ->where('estado', 'en_progreso')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now())
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas vencidas globales
     */
    public static function obtenerTareasVencidasGlobales(): int
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->whereNotNull('tareas.actividad_id')
            ->whereNotNull('tareas.fecha_fin')
            ->where('tareas.fecha_fin', '<', now())
            ->where('tareas.estado', '!=', 'completado')
            ->where('tareas.estado', '!=', 'eliminado')
            ->count();
    }

    /**
     * Obtener estadísticas de evidencias globales
     */
    public static function obtenerEstadisticasEvidenciasGlobales(): ?object
    {
        $totalEvidencias = DB::table('evidencias')
            ->join('tareas', 'evidencias.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->count();

        $tareasCompletadas = DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->where('tareas.estado', '!=', 'eliminado')
            ->count();

        $tareasCompletadasConEvidencias = DB::table('evidencias')
            ->join('tareas', 'evidencias.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->where('tareas.estado', '!=', 'eliminado')
            ->distinct('tareas.id')
            ->count('tareas.id');

        return (object) [
            'total_evidencias' => $totalEvidencias,
            'tareas_completadas' => $tareasCompletadas,
            'tareas_completadas_con_evidencias' => $tareasCompletadasConEvidencias,
        ];
    }

    /**
     * Obtener estadísticas de comentarios globales
     */
    public static function obtenerEstadisticasComentariosGlobales(): ?object
    {
        return DB::table('comentarios')
            ->join('tareas', 'comentarios.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select(
                DB::raw('COUNT(*) as total_comentarios'),
                DB::raw('COUNT(DISTINCT tareas.id) as tareas_con_comentarios'),
                DB::raw('COUNT(DISTINCT comentarios.user_id) as usuarios_comentando')
            )
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->first();
    }

    /**
     * Obtener top usuarios más productivos
     */
    public static function obtenerTopUsuariosProductivos(int $limite = 10): array
    {
        return DB::table('tarea_usuario')
            ->join('tareas', 'tarea_usuario.tarea_id', '=', 'tareas.id')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->join('users', 'tarea_usuario.user_id', '=', 'users.id')
            ->select(
                'users.id',
                'users.nombre',
                DB::raw('COUNT(*) as total_tareas'),
                DB::raw('SUM(CASE WHEN tareas.estado = "completado" THEN 1 ELSE 0 END) as tareas_completadas')
            )
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->groupBy('users.id', 'users.nombre')
            ->orderByDesc('tareas_completadas')
            ->limit($limite)
            ->get()
            ->toArray();
    }

    /**
     * Obtener proyectos creados en el último mes
     */
    public static function obtenerProyectosUltimoMes(): int
    {
        return DB::table('proyectos')
            ->where('created_at', '>=', now()->subMonth())
            ->count();
    }

    /**
     * Obtener tareas completadas en el último mes
     */
    public static function obtenerTareasCompletadasUltimoMes(): int
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', 'completado')
            ->where('tareas.updated_at', '>=', now()->subMonth())
            ->count();
    }

    /**
     * Obtener usuarios activos en el último mes
     */
    public static function obtenerUsuariosActivosUltimoMes(): int
    {
        return DB::table('tarea_usuario')
            ->join('tareas', 'tarea_usuario.tarea_id', '=', 'tareas.id')
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.updated_at', '>=', now()->subMonth())
            ->distinct('tarea_usuario.user_id')
            ->count('tarea_usuario.user_id');
    }

    /**
     * Obtener distribución de colaboradores por proyecto
     */
    public static function obtenerDistribucionColaboradoresPorProyecto(): array
    {
        return DB::table('proyecto_usuario')
            ->join('proyectos', 'proyecto_usuario.proyecto_id', '=', 'proyectos.id')
            ->select(
                'proyectos.id',
                'proyectos.nombre',
                DB::raw('COUNT(*) as total_colaboradores')
            )
            ->groupBy('proyectos.id', 'proyectos.nombre')
            ->orderByDesc('total_colaboradores')
            ->limit(10)
            ->get()
            ->toArray();
    }

    /**
     * Obtener proyectos con más tareas
     */
    public static function obtenerProyectosConMasTareas(int $limite = 10): array
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->join('proyectos', 'actividades.proyecto_id', '=', 'proyectos.id')
            ->select(
                'proyectos.id',
                'proyectos.nombre',
                DB::raw('COUNT(*) as total_tareas'),
                DB::raw('SUM(CASE WHEN tareas.estado = "completado" THEN 1 ELSE 0 END) as tareas_completadas')
            )
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->groupBy('proyectos.id', 'proyectos.nombre')
            ->orderByDesc('total_tareas')
            ->limit($limite)
            ->get()
            ->toArray();
    }

    /**
     * Obtener proyectos por estado
     */
    public static function obtenerProyectosPorEstado(): array
    {
        return DB::table('proyectos')
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener fases por estado (global)
     */
    public static function obtenerFasesPorEstadoGlobal(): array
    {
        return DB::table('actividades')
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->where('estado', '!=', 'eliminado')
            ->groupBy('estado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener tareas por estado global
     */
    public static function obtenerTareasPorEstadoGlobal(): array
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->select('tareas.estado', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->groupBy('tareas.estado')
            ->get()
            ->toArray();
    }

    /**
     * Obtener ranking de departamentos con mayor número de proyectos
     */
    public static function obtenerDepartamentosConMasProyectos(int $limite = 10): array
    {
        return DB::table('departamentos')
            ->leftJoin('proyectos', 'departamentos.id', '=', 'proyectos.departamento_id')
            ->select(
                'departamentos.id',
                'departamentos.nombre',
                DB::raw('COUNT(proyectos.id) as total_proyectos')
            )
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->orderByDesc('total_proyectos')
            ->limit($limite)
            ->get()
            ->toArray();
    }

    /**
     * Obtener cantidad de tareas creadas por mes, segmentadas por departamento
     */
    public static function obtenerTareasCreadasPorMesPorDepartamento(int $meses = 12): array
    {
        return DB::table('tareas')
            ->join('actividades', 'tareas.actividad_id', '=', 'actividades.id')
            ->join('proyectos', 'actividades.proyecto_id', '=', 'proyectos.id')
            ->join('departamentos', 'proyectos.departamento_id', '=', 'departamentos.id')
            ->select(
                'departamentos.id as departamento_id',
                'departamentos.nombre as departamento_nombre',
                DB::raw('YEAR(tareas.created_at) as año'),
                DB::raw('MONTH(tareas.created_at) as mes'),
                DB::raw('DATE_FORMAT(tareas.created_at, "%Y-%m") as periodo'),
                DB::raw('COUNT(*) as cantidad_tareas')
            )
            ->whereNotNull('tareas.actividad_id')
            ->where('tareas.estado', '!=', 'eliminado')
            ->where('tareas.created_at', '>=', now()->subMonths($meses))
            ->groupBy('departamentos.id', 'departamentos.nombre', 'año', 'mes', 'periodo')
            ->orderBy('periodo')
            ->orderBy('departamentos.nombre')
            ->get()
            ->toArray();
    }

    /**
     * Obtener productividad por departamento (tareas completadas vs asignadas)
     */
    public static function obtenerProductividadPorDepartamento(): array
    {
        return DB::table('departamentos')
            ->leftJoin('proyectos', 'departamentos.id', '=', 'proyectos.departamento_id')
            ->leftJoin('actividades', 'proyectos.id', '=', 'actividades.proyecto_id')
            ->leftJoin('tareas', function($join) {
                $join->on('actividades.id', '=', 'tareas.actividad_id')
                     ->where('tareas.estado', '!=', 'eliminado');
            })
            ->select(
                'departamentos.id',
                'departamentos.nombre',
                DB::raw('COUNT(DISTINCT tareas.id) as total_tareas'),
                DB::raw('SUM(CASE WHEN tareas.estado = "completado" THEN 1 ELSE 0 END) as tareas_completadas'),
                DB::raw('SUM(CASE WHEN tareas.estado != "completado" AND tareas.estado != "eliminado" THEN 1 ELSE 0 END) as tareas_pendientes')
            )
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->havingRaw('total_tareas > 0')
            ->orderByDesc('total_tareas')
            ->get()
            ->toArray();
    }

    /**
     * Obtener proyectos finalizados por departamento con evolución en el tiempo
     */
    public static function obtenerProyectosFinalizadosPorDepartamento(int $meses = 12): array
    {
        // Total de proyectos finalizados por departamento
        $totales = DB::table('departamentos')
            ->leftJoin('proyectos', function($join) {
                $join->on('departamentos.id', '=', 'proyectos.departamento_id')
                     ->where('proyectos.estado', 'finalizado');
            })
            ->select(
                'departamentos.id',
                'departamentos.nombre',
                DB::raw('COUNT(proyectos.id) as total_finalizados')
            )
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->get()
            ->toArray();

        // Evolución por mes de proyectos finalizados
        $evolucion = DB::table('proyectos')
            ->join('departamentos', 'proyectos.departamento_id', '=', 'departamentos.id')
            ->select(
                'departamentos.id as departamento_id',
                'departamentos.nombre as departamento_nombre',
                DB::raw('YEAR(proyectos.updated_at) as año'),
                DB::raw('MONTH(proyectos.updated_at) as mes'),
                DB::raw('DATE_FORMAT(proyectos.updated_at, "%Y-%m") as periodo'),
                DB::raw('COUNT(*) as cantidad_finalizados')
            )
            ->where('proyectos.estado', 'finalizado')
            ->where('proyectos.updated_at', '>=', now()->subMonths($meses))
            ->groupBy('departamentos.id', 'departamentos.nombre', 'año', 'mes', 'periodo')
            ->orderBy('periodo')
            ->orderBy('departamentos.nombre')
            ->get()
            ->toArray();

        return [
            'totales' => $totales,
            'evolucion' => $evolucion,
        ];
    }
}

