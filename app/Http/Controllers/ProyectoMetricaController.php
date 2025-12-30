<?php

namespace App\Http\Controllers;

use App\Models\ProyectoMetrica;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Controlador para métricas de proyectos
 * Este controlador contiene ÚNICAMENTE lógica de negocio
 * Las consultas a la base de datos están en el modelo ProyectoMetrica
 */
class ProyectoMetricaController extends Controller
{
    /**
     * Mostrar dashboard de métricas del proyecto
     */
    public function index($proyectoId)
    {
        $usuario = User::find(session('user_id'));
        if (!$usuario) {
            return redirect()->route('login');
        }

        // Verificar que el proyecto existe
        $proyecto = Proyecto::findOrFail($proyectoId);

        // Verificar permisos: el usuario debe ser colaborador del proyecto, administrador o TI
        $puedeVer = false;
        
        if ($usuario->hasRole('Administrador') || $usuario->hasRole('admin') || $usuario->hasRole('TI')) {
            $puedeVer = true;
        } else {
            // Verificar si el usuario es colaborador del proyecto
            $esColaborador = $proyecto->colaboradores()->where('users.id', $usuario->id)->exists();
            if ($esColaborador) {
                $puedeVer = true;
            }
        }

        if (!$puedeVer) {
            return redirect()->route('proyectos.index')
                ->with('error', 'No tienes permisos para ver las métricas de este proyecto.');
        }

        // Obtener información básica del proyecto
        $infoBasica = ProyectoMetrica::obtenerInformacionBasica($proyectoId);
        if (!$infoBasica) {
            return redirect()->route('proyectos.index')
                ->with('error', 'Proyecto no encontrado.');
        }

        // Calcular métricas usando el modelo (solo consultas) y aplicar lógica de negocio
        $metricas = $this->calcularMetricas($proyectoId);

        return view('proyectos.metricas', [
            'proyecto' => $proyecto,
            'infoBasica' => $infoBasica,
            'metricas' => $metricas,
            'usuario' => $usuario
        ]);
    }

    /**
     * Calcular todas las métricas del proyecto aplicando lógica de negocio
     */
    public function calcularMetricas(int $proyectoId): array
    {
        // 1. MÉTRICAS GENERALES DEL PROYECTO
        $infoBasica = ProyectoMetrica::obtenerInformacionBasica($proyectoId);
        $colaboradores = ProyectoMetrica::obtenerColaboradores($proyectoId);
        $creador = ProyectoMetrica::obtenerCreadorProyecto($proyectoId);
        $departamento = ProyectoMetrica::obtenerDepartamentoProyecto($proyectoId);
        $fechasReales = ProyectoMetrica::obtenerFechasRealesProyecto($proyectoId);

        // Calcular fechas y plazos
        $fechasYPlazos = $this->calcularFechasYPlazos($infoBasica, $fechasReales);
        
        // Calcular colaboradores y roles
        $colaboradoresYMetricas = $this->calcularColaboradoresYMetricas($colaboradores);

        // 2. MÉTRICAS DE ACTIVIDADES
        $actividadesPorEstado = ProyectoMetrica::obtenerActividadesPorEstado($proyectoId);
        $totalActividades = ProyectoMetrica::obtenerTotalActividades($proyectoId);
        $actividadesVencidas = ProyectoMetrica::obtenerActividadesVencidas($proyectoId);
        $actividadesProximasAVencer = ProyectoMetrica::obtenerActividadesProximasAVencer($proyectoId);
        $duracionActividades = ProyectoMetrica::obtenerDuracionActividades($proyectoId);
        
        $metricasActividades = $this->calcularMetricasActividades(
            $actividadesPorEstado,
            $totalActividades,
            $actividadesVencidas,
            $actividadesProximasAVencer,
            $duracionActividades
        );

        // 3. MÉTRICAS DE TAREAS
        $tareasPorEstado = ProyectoMetrica::obtenerTareasPorEstado($proyectoId);
        $tareasPorPrioridad = ProyectoMetrica::obtenerTareasPorPrioridad($proyectoId);
        $totalTareas = ProyectoMetrica::obtenerTotalTareas($proyectoId);
        $tareasVencidas = ProyectoMetrica::obtenerTareasVencidas($proyectoId);
        $tareasProximasAVencer = ProyectoMetrica::obtenerTareasProximasAVencer($proyectoId);
        $tareasPorUsuario = ProyectoMetrica::obtenerTareasPorUsuario($proyectoId);
        $duracionTareas = ProyectoMetrica::obtenerDuracionTareas($proyectoId);
        $tiempoPromedioCompletado = ProyectoMetrica::obtenerTiempoPromedioCompletadoTareas($proyectoId);
        $tareasSinAsignar = ProyectoMetrica::obtenerTareasSinAsignar($proyectoId);
        
        $metricasTareas = $this->calcularMetricasTareas(
            $tareasPorEstado,
            $tareasPorPrioridad,
            $totalTareas,
            $tareasVencidas,
            $tareasProximasAVencer,
            $tareasPorUsuario,
            $duracionTareas,
            $tiempoPromedioCompletado,
            $tareasSinAsignar
        );

        // 4. MÉTRICAS DE EVIDENCIAS
        $estadisticasEvidencias = ProyectoMetrica::obtenerEstadisticasEvidencias($proyectoId);
        $tareasCompletadasSinEvidencias = ProyectoMetrica::obtenerTareasCompletadasSinEvidencias($proyectoId);
        
        $metricasEvidencias = $this->calcularMetricasEvidencias(
            $estadisticasEvidencias,
            $totalTareas,
            $tareasCompletadasSinEvidencias,
            $metricasTareas['completadas']
        );

        // 5. MÉTRICAS DE COMENTARIOS
        $estadisticasComentarios = ProyectoMetrica::obtenerEstadisticasComentarios($proyectoId);
        
        $metricasComentarios = $this->calcularMetricasComentarios(
            $estadisticasComentarios,
            $totalTareas
        );

        // 6. MÉTRICAS DE TRAZABILIDAD
        $trazabilidad = ProyectoMetrica::obtenerTrazabilidad($proyectoId, 10);

        // 7. KPIs DEL PROYECTO
        $kpis = $this->calcularKPIs($metricasActividades, $metricasTareas, $metricasEvidencias);

        return [
            'generales' => [
                'info_basica' => $infoBasica,
                'creador' => $creador,
                'departamento' => $departamento,
                'fechas_y_plazos' => $fechasYPlazos,
                'colaboradores' => $colaboradoresYMetricas,
            ],
            'actividades' => $metricasActividades,
            'tareas' => $metricasTareas,
            'evidencias' => $metricasEvidencias,
            'comentarios' => $metricasComentarios,
            'trazabilidad' => $trazabilidad,
            'kpis' => $kpis,
        ];
    }

    /**
     * Calcular fechas y plazos del proyecto
     */
    private function calcularFechasYPlazos($infoBasica, $fechasReales): array
    {
        $fechaInicioPlanificada = $infoBasica->fecha_inicio ? Carbon::parse($infoBasica->fecha_inicio) : null;
        $fechaFinPlanificada = $infoBasica->fecha_fin ? Carbon::parse($infoBasica->fecha_fin) : null;
        
        $fechaInicioReal = $fechasReales && $fechasReales->fecha_inicio_real 
            ? Carbon::parse($fechasReales->fecha_inicio_real) 
            : null;
        $fechaFinReal = $fechasReales && $fechasReales->fecha_fin_real 
            ? Carbon::parse($fechasReales->fecha_fin_real) 
            : null;

        $duracionPlanificada = null;
        $duracionReal = null;
        $diasRestantes = null;
        $diasRetrasoAdelanto = null;
        $porcentajeTiempoTranscurrido = null;

        if ($fechaInicioPlanificada && $fechaFinPlanificada) {
            $duracionPlanificada = $fechaInicioPlanificada->diffInDays($fechaFinPlanificada);
            $diasTranscurridos = $fechaInicioPlanificada->diffInDays(now());
            $diasRestantes = round(now()->diffInDays($fechaFinPlanificada, false));
            $diasRetrasoAdelanto = $diasRestantes < 0 ? abs($diasRestantes) : ($diasRestantes > $duracionPlanificada ? -($diasRestantes - $duracionPlanificada) : 0);
            
            if ($duracionPlanificada > 0) {
                $porcentajeTiempoTranscurrido = min(100, ($diasTranscurridos / $duracionPlanificada) * 100);
            }
        }

        if ($fechaInicioReal && $fechaFinReal) {
            $duracionReal = $fechaInicioReal->diffInDays($fechaFinReal);
        }

        return [
            'fecha_inicio_planificada' => $fechaInicioPlanificada?->format('d/m/Y'),
            'fecha_fin_planificada' => $fechaFinPlanificada?->format('d/m/Y'),
            'fecha_inicio_real' => $fechaInicioReal?->format('d/m/Y'),
            'fecha_fin_real' => $fechaFinReal?->format('d/m/Y'),
            'duracion_planificada_dias' => $duracionPlanificada,
            'duracion_real_dias' => $duracionReal,
            'dias_restantes' => $diasRestantes,
            'dias_retraso_adelanto' => $diasRetrasoAdelanto,
            'porcentaje_tiempo_transcurrido' => round($porcentajeTiempoTranscurrido ?? 0, 2),
        ];
    }

    /**
     * Calcular métricas de colaboradores
     */
    private function calcularColaboradoresYMetricas(array $colaboradores): array
    {
        $total = count($colaboradores);
        $lideres = 0;
        $colaboradoresCount = 0;
        $visores = 0;
        $porDepartamento = [];

        foreach ($colaboradores as $colaborador) {
            $rol = $colaborador->rol_proyecto ?? 'colaborador';
            
            switch ($rol) {
                case 'lider':
                    $lideres++;
                    break;
                case 'colaborador':
                    $colaboradoresCount++;
                    break;
                case 'visor':
                    $visores++;
                    break;
            }

            $deptId = $colaborador->departamento_nombre ?? 'Sin departamento';
            if (!isset($porDepartamento[$deptId])) {
                $porDepartamento[$deptId] = 0;
            }
            $porDepartamento[$deptId]++;
        }

        return [
            'total' => $total,
            'lideres' => $lideres,
            'colaboradores' => $colaboradoresCount,
            'visores' => $visores,
            'por_departamento' => $porDepartamento,
            'lista' => $colaboradores,
        ];
    }

    /**
     * Calcular métricas de actividades
     */
    private function calcularMetricasActividades(
        array $actividadesPorEstado,
        int $totalActividades,
        array $actividadesVencidas,
        array $actividadesProximasAVencer,
        $duracionActividades
    ): array {
        $pendientes = 0;
        $enProgreso = 0;
        $finalizadas = 0;
        $eliminadas = 0;

        foreach ($actividadesPorEstado as $item) {
            switch ($item->estado) {
                case 'pendiente':
                    $pendientes = $item->total;
                    break;
                case 'en_progreso':
                    $enProgreso = $item->total;
                    break;
                case 'finalizado':
                    $finalizadas = $item->total;
                    break;
                case 'eliminado':
                    $eliminadas = $item->total;
                    break;
            }
        }

        $porcentajeCompletadas = $totalActividades > 0 
            ? round(($finalizadas / $totalActividades) * 100, 2) 
            : 0;

        $distribucionPorEstado = [];
        foreach ($actividadesPorEstado as $item) {
            $porcentaje = $totalActividades > 0 
                ? round(($item->total / $totalActividades) * 100, 2) 
                : 0;
            $distribucionPorEstado[$item->estado] = [
                'total' => $item->total,
                'porcentaje' => $porcentaje,
            ];
        }

        return [
            'total' => $totalActividades,
            'pendientes' => $pendientes,
            'en_progreso' => $enProgreso,
            'finalizadas' => $finalizadas,
            'eliminadas' => $eliminadas,
            'porcentaje_completadas' => $porcentajeCompletadas,
            'distribucion_por_estado' => $distribucionPorEstado,
            'vencidas' => count($actividadesVencidas),
            'actividades_vencidas_lista' => $actividadesVencidas,
            'proximas_a_vencer' => count($actividadesProximasAVencer),
            'actividades_proximas_lista' => $actividadesProximasAVencer,
            'duracion_promedio_dias' => $duracionActividades?->promedio_dias ? round($duracionActividades->promedio_dias, 2) : null,
            'duracion_minima_dias' => $duracionActividades?->minimo_dias,
            'duracion_maxima_dias' => $duracionActividades?->maximo_dias,
        ];
    }

    /**
     * Calcular métricas de tareas
     */
    private function calcularMetricasTareas(
        array $tareasPorEstado,
        array $tareasPorPrioridad,
        int $totalTareas,
        array $tareasVencidas,
        array $tareasProximasAVencer,
        array $tareasPorUsuario,
        $duracionTareas,
        $tiempoPromedioCompletado,
        int $tareasSinAsignar
    ): array {
        $pendientes = 0;
        $enProgreso = 0;
        $completadas = 0;
        $eliminadas = 0;

        foreach ($tareasPorEstado as $item) {
            switch ($item->estado) {
                case 'pendiente':
                    $pendientes = $item->total;
                    break;
                case 'en_progreso':
                    $enProgreso = $item->total;
                    break;
                case 'completado':
                    $completadas = $item->total;
                    break;
                case 'eliminado':
                    $eliminadas = $item->total;
                    break;
            }
        }

        $porcentajeCompletadas = $totalTareas > 0 
            ? round(($completadas / $totalTareas) * 100, 2) 
            : 0;

        $distribucionPorEstado = [];
        foreach ($tareasPorEstado as $item) {
            $porcentaje = $totalTareas > 0 
                ? round(($item->total / $totalTareas) * 100, 2) 
                : 0;
            $distribucionPorEstado[$item->estado] = [
                'total' => $item->total,
                'porcentaje' => $porcentaje,
            ];
        }

        $alta = 0;
        $media = 0;
        $baja = 0;

        foreach ($tareasPorPrioridad as $item) {
            switch ($item->prioridad) {
                case 'alta':
                    $alta = $item->total;
                    break;
                case 'media':
                    $media = $item->total;
                    break;
                case 'baja':
                    $baja = $item->total;
                    break;
            }
        }

        $totalPorPrioridad = $alta + $media + $baja;
        $distribucionPorPrioridad = [];
        foreach ($tareasPorPrioridad as $item) {
            $porcentaje = $totalPorPrioridad > 0 
                ? round(($item->total / $totalPorPrioridad) * 100, 2) 
                : 0;
            $distribucionPorPrioridad[$item->prioridad] = [
                'total' => $item->total,
                'porcentaje' => $porcentaje,
            ];
        }

        // Encontrar usuario con mayor carga de trabajo y mayor porcentaje de completado
        $usuarioMayorCarga = null;
        $usuarioMayorCompletado = null;
        $maxCarga = 0;
        $maxPorcentajeCompletado = 0;

        foreach ($tareasPorUsuario as $usuario) {
            if ($usuario->total_tareas > $maxCarga) {
                $maxCarga = $usuario->total_tareas;
                $usuarioMayorCarga = $usuario;
            }

            if ($usuario->total_tareas > 0) {
                $porcentaje = ($usuario->tareas_completadas / $usuario->total_tareas) * 100;
                if ($porcentaje > $maxPorcentajeCompletado) {
                    $maxPorcentajeCompletado = $porcentaje;
                    $usuarioMayorCompletado = $usuario;
                }
            }
        }

        return [
            'total' => $totalTareas,
            'pendientes' => $pendientes,
            'en_progreso' => $enProgreso,
            'completadas' => $completadas,
            'eliminadas' => $eliminadas,
            'porcentaje_completadas' => $porcentajeCompletadas,
            'distribucion_por_estado' => $distribucionPorEstado,
            'distribucion_por_prioridad' => $distribucionPorPrioridad,
            'alta' => $alta,
            'media' => $media,
            'baja' => $baja,
            'vencidas' => count($tareasVencidas),
            'tareas_vencidas_lista' => $tareasVencidas,
            'proximas_a_vencer' => count($tareasProximasAVencer),
            'tareas_proximas_lista' => $tareasProximasAVencer,
            'por_usuario' => $tareasPorUsuario,
            'usuario_mayor_carga' => $usuarioMayorCarga,
            'usuario_mayor_completado' => $usuarioMayorCompletado,
            'duracion_promedio_dias' => $duracionTareas?->promedio_dias ? round($duracionTareas->promedio_dias, 2) : null,
            'duracion_minima_dias' => $duracionTareas?->minimo_dias,
            'duracion_maxima_dias' => $duracionTareas?->maximo_dias,
            'tiempo_promedio_completado_dias' => $tiempoPromedioCompletado?->promedio_dias_completado ? round($tiempoPromedioCompletado->promedio_dias_completado, 2) : null,
            'sin_asignar' => $tareasSinAsignar,
        ];
    }

    /**
     * Calcular métricas de evidencias
     */
    private function calcularMetricasEvidencias(
        $estadisticasEvidencias,
        int $totalTareas,
        int $tareasCompletadasSinEvidencias,
        int $tareasCompletadas
    ): array {
        $totalEvidencias = $estadisticasEvidencias->total_evidencias ?? 0;
        $tareasConEvidencias = $estadisticasEvidencias->tareas_con_evidencias ?? 0;
        $tareasCompletadasConEvidencias = $estadisticasEvidencias->tareas_completadas_con_evidencias ?? 0;

        $promedioEvidenciasPorTarea = $tareasConEvidencias > 0 
            ? round($totalEvidencias / $tareasConEvidencias, 2) 
            : 0;

        $porcentajeTareasConEvidencias = $totalTareas > 0 
            ? round(($tareasConEvidencias / $totalTareas) * 100, 2) 
            : 0;

        $porcentajeTareasCompletadasConEvidencias = $tareasCompletadas > 0 
            ? round(($tareasCompletadasConEvidencias / $tareasCompletadas) * 100, 2) 
            : 0;

        return [
            'total' => $totalEvidencias,
            'tareas_con_evidencias' => $tareasConEvidencias,
            'tareas_completadas_con_evidencias' => $tareasCompletadasConEvidencias,
            'tareas_completadas_sin_evidencias' => $tareasCompletadasSinEvidencias,
            'promedio_por_tarea' => $promedioEvidenciasPorTarea,
            'porcentaje_tareas_con_evidencias' => $porcentajeTareasConEvidencias,
            'porcentaje_tareas_completadas_con_evidencias' => $porcentajeTareasCompletadasConEvidencias,
        ];
    }

    /**
     * Calcular métricas de comentarios
     */
    private function calcularMetricasComentarios($estadisticasComentarios, int $totalTareas): array
    {
        $totalComentarios = $estadisticasComentarios->total_comentarios ?? 0;
        $tareasConComentarios = $estadisticasComentarios->tareas_con_comentarios ?? 0;
        $usuariosComentando = $estadisticasComentarios->usuarios_comentando ?? 0;

        $promedioComentariosPorTarea = $tareasConComentarios > 0 
            ? round($totalComentarios / $tareasConComentarios, 2) 
            : 0;

        $porcentajeTareasConComentarios = $totalTareas > 0 
            ? round(($tareasConComentarios / $totalTareas) * 100, 2) 
            : 0;

        return [
            'total' => $totalComentarios,
            'tareas_con_comentarios' => $tareasConComentarios,
            'usuarios_comentando' => $usuariosComentando,
            'promedio_por_tarea' => $promedioComentariosPorTarea,
            'porcentaje_tareas_con_comentarios' => $porcentajeTareasConComentarios,
        ];
    }

    /**
     * Calcular KPIs del proyecto
     */
    private function calcularKPIs(array $metricasActividades, array $metricasTareas, array $metricasEvidencias): array
    {
        return [
            'completitud_tareas' => $metricasTareas['porcentaje_completadas'],
            'completitud_actividades' => $metricasActividades['porcentaje_completadas'],
            'avance_proyecto' => ($metricasTareas['porcentaje_completadas'] + $metricasActividades['porcentaje_completadas']) / 2,
            'calidad_evidencias' => $metricasEvidencias['porcentaje_tareas_completadas_con_evidencias'],
        ];
    }
}

