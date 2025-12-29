<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tarea;
use App\Models\Proyecto;
use App\Models\DashboardMetrica;
use Illuminate\Http\Request;

/**
 * Controlador para el dashboard de administrador
 * Este controlador contiene ÚNICAMENTE lógica de negocio
 * Las consultas a la base de datos están en el modelo DashboardMetrica
 */
class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $usuario = User::find($userId);

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Verificar que el usuario sea administrador o TI
        if (!$usuario->hasRole('Administrador') && !$usuario->hasRole('admin') && !$usuario->hasRole('TI')) {
            return redirect()->route('inicio')
                ->with('error', 'No tienes permisos para acceder al dashboard de administrador.');
        }

        // Calcular todas las métricas del dashboard
        $metricas = $this->calcularMetricasDashboard();

        return view('dashboard', [
            'usuario' => $usuario,
            'metricas' => $metricas
        ]);
    }

    public function inicio(Request $request)
    {
        $userId = session('user_id');
        $usuario = User::find($userId);

        if (!$usuario) {
            return redirect()->route('login');
        }

        $proyectos = Proyecto::obtenerPorUsuario($userId);
        $tareasIndependientes = Tarea::obtenerIndependientesPorUsuario($userId);
        
        // Obtener totales para los badges
        $totalUltimas = Tarea::obtenerTodasPorUsuario($userId)->count();
        $totalProximas = Tarea::obtenerProximasAVencer($userId, 7)->count();
        $totalVencidas = Tarea::obtenerVencidas($userId)->count();
        
        // Determinar qué tab está activo
        $tabActivo = $request->get('tab', 'ultimas');
        $perPage = 5;
        
        // Paginar según el tab activo y preservar parámetros de query
        switch ($tabActivo) {
            case 'proximas':
                $tareasProximas = Tarea::obtenerProximasAVencerPaginadas($userId, 7, $perPage)
                    ->appends($request->query());
                $ultimasTareas = null;
                $tareasVencidas = null;
                break;
            case 'vencidas':
                $tareasVencidas = Tarea::obtenerVencidasPaginadas($userId, $perPage)
                    ->appends($request->query());
                $ultimasTareas = null;
                $tareasProximas = null;
                break;
            default: // 'ultimas'
                $ultimasTareas = Tarea::obtenerUltimasPorUsuarioPaginadas($userId, $perPage)
                    ->appends($request->query());
                $tareasProximas = null;
                $tareasVencidas = null;
                break;
        }

        return view('inicio', compact(
            'usuario',
            'proyectos',
            'tareasIndependientes',
            'ultimasTareas',
            'tareasProximas',
            'tareasVencidas',
            'tabActivo',
            'totalUltimas',
            'totalProximas',
            'totalVencidas'
        ));
    }

    /**
     * Calcular todas las métricas del dashboard aplicando lógica de negocio
     */
    private function calcularMetricasDashboard(): array
    {
        // 1. RANKING DE DEPARTAMENTOS CON MAYOR NÚMERO DE PROYECTOS
        $departamentosConMasProyectos = DashboardMetrica::obtenerDepartamentosConMasProyectos(20);

        // 2. TAREAS CREADAS POR MES SEGMENTADAS POR DEPARTAMENTO
        $tareasPorMesPorDepartamento = DashboardMetrica::obtenerTareasCreadasPorMesPorDepartamento(12);
        $tareasPorMesFormateadas = $this->formatearTareasPorMes($tareasPorMesPorDepartamento);

        // 3. PRODUCTIVIDAD POR DEPARTAMENTO
        $productividadPorDepartamento = DashboardMetrica::obtenerProductividadPorDepartamento();
        $productividadFormateada = $this->calcularProductividad($productividadPorDepartamento);

        // 4. PROYECTOS FINALIZADOS POR DEPARTAMENTO
        $proyectosFinalizados = DashboardMetrica::obtenerProyectosFinalizadosPorDepartamento(12);
        $evolucionFormateada = $this->formatearEvolucionProyectosFinalizados($proyectosFinalizados['evolucion']);

        return [
            'ranking_departamentos' => $departamentosConMasProyectos,
            'tareas_por_mes' => $tareasPorMesFormateadas,
            'productividad' => $productividadFormateada,
            'proyectos_finalizados' => [
                'totales' => $proyectosFinalizados['totales'],
                'evolucion' => $evolucionFormateada,
            ],
        ];
    }

    /**
     * Formatear tareas por mes para gráfico
     */
    private function formatearTareasPorMes(array $datos): array
    {
        $departamentos = [];
        $periodos = [];
        $series = [];

        foreach ($datos as $item) {
            $deptNombre = $item->departamento_nombre;
            $periodo = $item->periodo;

            if (!in_array($periodo, $periodos)) {
                $periodos[] = $periodo;
            }

            if (!isset($departamentos[$deptNombre])) {
                $departamentos[$deptNombre] = [];
            }

            $departamentos[$deptNombre][$periodo] = $item->cantidad_tareas;
        }

        sort($periodos);

        foreach ($departamentos as $deptNombre => $datosDept) {
            $valores = [];
            foreach ($periodos as $periodo) {
                $valores[] = $datosDept[$periodo] ?? 0;
            }
            $series[] = [
                'nombre' => $deptNombre,
                'datos' => $valores,
            ];
        }

        return [
            'periodos' => $periodos,
            'series' => $series,
        ];
    }

    /**
     * Calcular porcentajes de productividad
     */
    private function calcularProductividad(array $datos): array
    {
        $resultado = [];
        foreach ($datos as $item) {
            $porcentaje = $item->total_tareas > 0 
                ? round(($item->tareas_completadas / $item->total_tareas) * 100, 2) 
                : 0;

            $resultado[] = [
                'departamento_id' => $item->id,
                'departamento_nombre' => $item->nombre,
                'total_tareas' => $item->total_tareas,
                'tareas_completadas' => $item->tareas_completadas,
                'tareas_pendientes' => $item->tareas_pendientes,
                'porcentaje_completado' => $porcentaje,
            ];
        }

        // Ordenar por porcentaje descendente
        usort($resultado, function($a, $b) {
            return $b['porcentaje_completado'] <=> $a['porcentaje_completado'];
        });

        return $resultado;
    }

    /**
     * Formatear evolución de proyectos finalizados
     */
    private function formatearEvolucionProyectosFinalizados(array $datos): array
    {
        $departamentos = [];
        $periodos = [];
        $series = [];

        foreach ($datos as $item) {
            $deptNombre = $item->departamento_nombre;
            $periodo = $item->periodo;

            if (!in_array($periodo, $periodos)) {
                $periodos[] = $periodo;
            }

            if (!isset($departamentos[$deptNombre])) {
                $departamentos[$deptNombre] = [];
            }

            $departamentos[$deptNombre][$periodo] = $item->cantidad_finalizados;
        }

        sort($periodos);

        foreach ($departamentos as $deptNombre => $datosDept) {
            $valores = [];
            foreach ($periodos as $periodo) {
                $valores[] = $datosDept[$periodo] ?? 0;
            }
            $series[] = [
                'nombre' => $deptNombre,
                'datos' => $valores,
            ];
        }

        return [
            'periodos' => $periodos,
            'series' => $series,
        ];
    }
}
