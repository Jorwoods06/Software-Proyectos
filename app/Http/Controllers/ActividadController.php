<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Http\Request;

class ActividadController extends Controller
{
    public function index($proyectoId)
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

        return view('actividades.index', compact('actividades', 'proyectoId', 'proyecto', 'colaboradores'));
    }

    public function store(Request $request)
    {
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
