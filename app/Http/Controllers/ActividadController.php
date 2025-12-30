<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use App\Http\Controllers\ProyectoMetricaController;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActividadController extends Controller
{
    public function index($proyectoId, Request $request)
    {
        $proyecto = Proyecto::obtenerConColaboradores($proyectoId);
        
        if (!$proyecto) {
            return back()->with('error', 'Proyecto no encontrado.');
        }

        // Verificar que el usuario tenga acceso al proyecto
        $auth_user = User::with('roles')->find(session('user_id'));
        if (!$auth_user) {
            return redirect()->route('login');
        }

        if (!$proyecto->usuarioTieneAcceso($auth_user)) {
            abort(403, 'No tienes acceso a este proyecto.');
        }

        $actividades = Actividad::obtenerConTareasPorProyecto($proyectoId);
        $colaboradores = $proyecto->colaboradores;

        // Lógica para el calendario
        $mes = $request->get('mes', Carbon::now('America/Bogota')->month);
        $ano = $request->get('ano', Carbon::now('America/Bogota')->year);
        
        // Validar mes y año
        $mes = max(1, min(12, (int)$mes));
        $ano = max(2020, min(2100, (int)$ano));
        
        // Obtener todas las tareas del proyecto con fecha_fin
        $todasTareas = collect();
        foreach ($actividades as $actividad) {
            $tareas = $actividad->tareas()
                ->where('estado', '!=', 'eliminado')
                ->whereNotNull('fecha_fin')
                ->with(['usuariosAsignados', 'actividad'])
                ->get();
            $todasTareas = $todasTareas->merge($tareas);
        }
        
        // Agrupar tareas por fecha
        $tareasPorFecha = $todasTareas->groupBy(function($tarea) {
            return Carbon::parse($tarea->fecha_fin)->format('Y-m-d');
        });
        
        // Generar calendario - Solo días del mes actual
        $primerDia = Carbon::create($ano, $mes, 1, 0, 0, 0, 'America/Bogota');
        $ultimoDia = $primerDia->copy()->endOfMonth();
        $diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
        $diasMes = [];
        
        // Calcular el día de la semana del primer día (0 = domingo, 6 = sábado)
        $diaSemanaInicio = $primerDia->dayOfWeek;
        
        // Agregar celdas vacías al inicio para alinear el primer día en su columna correcta
        for ($i = 0; $i < $diaSemanaInicio; $i++) {
            $diasMes[] = [
                'dia' => null,
                'fecha' => null,
                'otro_mes' => false,
                'es_hoy' => false,
                'tareas' => collect(),
                'vacio' => true
            ];
        }
        
        // Días del mes actual
        $hoy = Carbon::now('America/Bogota');
        for ($dia = 1; $dia <= $ultimoDia->day; $dia++) {
            $fecha = Carbon::create($ano, $mes, $dia, 0, 0, 0, 'America/Bogota');
            $fechaStr = $fecha->format('Y-m-d');
            $esHoy = $fecha->isSameDay($hoy);
            
            $diasMes[] = [
                'dia' => $dia,
                'fecha' => $fechaStr,
                'otro_mes' => false,
                'es_hoy' => $esHoy,
                'tareas' => $tareasPorFecha->get($fechaStr, collect()),
                'vacio' => false
            ];
        }
        
        // Determinar qué pestaña está activa
        if ($request->has('tab')) {
            $tabActivo = $request->get('tab');
        } elseif ($request->has('mes') || $request->has('ano')) {
            $tabActivo = 'calendario';
        } else {
            $tabActivo = 'fases';
        }
        
        // Nombres de meses en español
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        $nombreMes = $meses[$mes];

        // Cargar métricas si se solicita la pestaña de análisis
        $metricas = null;
        $infoBasica = null;
        if ($tabActivo === 'analisis') {
            $metricasController = new ProyectoMetricaController();
            $metricas = $metricasController->calcularMetricas($proyectoId);
            $infoBasica = \App\Models\ProyectoMetrica::obtenerInformacionBasica($proyectoId);
        }

        return view('actividades.index', compact(
            'actividades', 'proyectoId', 'proyecto', 'colaboradores',
            'mes', 'ano', 'nombreMes', 'diasMes', 'diasSemana', 'tareasPorFecha', 'tabActivo',
            'metricas', 'infoBasica'
        ));
    }

    public function store(Request $request)
    {
        // Verificar que el usuario no sea Auditor
        $auth_user = User::with('roles')->find(session('user_id'));
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los usuarios con rol Auditor no pueden crear fases. Solo tienen permisos de lectura.'
                ], 403);
            }
            return back()->with('error', 'Los usuarios con rol Auditor no pueden crear fases. Solo tienen permisos de lectura.');
        }

        $data = $request->validate([
            'proyecto_id' => 'required|exists:proyectos,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'hora_fin' => 'nullable|date_format:H:i'
        ]);

        // Preparar datos usando método del modelo
        $data = Actividad::prepararDatosCreacion($data);
        Actividad::create($data);
        
        return back()->with('success', 'Actividad creada correctamente.');
    }

    public function update(Request $request, $id)
    {
        $actividad = Actividad::findOrFail($id);
        $proyecto = $actividad->proyecto;
        $auth_user = User::with('roles')->find(session('user_id'));

        // Verificar que el usuario no sea Auditor
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los usuarios con rol Auditor no pueden editar fases. Solo tienen permisos de lectura.'
                ], 403);
            }
            abort(403, 'Los usuarios con rol Auditor no pueden editar fases. Solo tienen permisos de lectura.');
        }

        // Validar permisos: Administrador, TI o creador del proyecto
        if (!$proyecto->puedeGestionarActividadesYTareas($auth_user)) {
            abort(403, 'No tienes permisos para editar esta actividad.');
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'hora_fin' => 'nullable|date_format:H:i',
            'estado' => 'nullable|in:pendiente,en_progreso,finalizado'
        ]);

        // Preparar datos usando método del modelo
        $data = $actividad->prepararDatosActualizacion($data);
        $actividad->update($data);
        
        return back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy($id)
    {
        $actividad = Actividad::findOrFail($id);
        $proyecto = $actividad->proyecto;
        $auth_user = User::with('roles')->find(session('user_id'));

        // Verificar que el usuario no sea Auditor
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            abort(403, 'Los usuarios con rol Auditor no pueden eliminar fases. Solo tienen permisos de lectura.');
        }

        // Validar permisos: Administrador, TI o creador del proyecto
        if (!$proyecto->puedeGestionarActividadesYTareas($auth_user)) {
            abort(403, 'No tienes permisos para eliminar esta actividad.');
        }

        // Soft delete: cambiar estado a 'eliminado' en lugar de eliminar físicamente
        $actividad->eliminar();
        return back()->with('success', 'Actividad eliminada correctamente.');
    }

    /**
     * Obtener tareas paginadas de una actividad
     */
    public function getTareas($actividadId, Request $request)
    {
        $actividad = Actividad::findOrFail($actividadId);
        $proyecto = $actividad->proyecto;
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $tareas = $actividad->tareas()
            ->where('estado', '!=', 'eliminado')
            ->with(['usuariosAsignados', 'evidencias'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('actividades._tareas_table', [
                    'tareas' => $tareas,
                    'actividad' => $actividad,
                    'proyecto' => $proyecto
                ])->render()
            ]);
        }

        return view('actividades._tareas_table', [
            'tareas' => $tareas,
            'actividad' => $actividad,
            'proyecto' => $proyecto
        ]);
    }
}
