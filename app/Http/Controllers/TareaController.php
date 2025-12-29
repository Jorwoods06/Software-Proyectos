<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Actividad;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Comentario;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TareaController extends Controller
{
    public function index($actividadId)
    {
        $tareas = Tarea::obtenerPorActividad($actividadId);
        return view('tareas.index', compact('tareas', 'actividadId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'actividad_id' => 'nullable|exists:actividades,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'hora_fin' => 'nullable|date_format:H:i',
            'prioridad' => 'nullable|in:baja,media,alta',
            'estado' => 'nullable|in:pendiente,en_progreso,completado',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id',
            'es_independiente' => 'nullable|boolean'
        ]);

        try {
            // Validar permisos si la tarea pertenece a una actividad
            if (!empty($data['actividad_id'])) {
                $actividad = Actividad::findOrFail($data['actividad_id']);
                $proyecto = $actividad->proyecto;
                $auth_user = User::with('roles')->find(session('user_id'));
                
                if (!$proyecto->puedeGestionarActividadesYTareas($auth_user)) {
                    abort(403, 'No tienes permisos para crear tareas en este proyecto.');
                }
            }

            // Preparar datos para la tarea usando método del modelo
            $tareaData = Tarea::prepararDatosCreacion($data, session('user_id'));
            $tarea = Tarea::crearConUsuarios($tareaData, $data['usuarios'] ?? null);
            
            // Enviar notificaciones a usuarios asignados
            if (!empty($data['usuarios']) && $tarea) {
                $notificationService = new NotificationService();
                $notificationService->notificarUsuariosAsignados($tarea, 'asignada');
            }
            
            return back()->with('success', 'Tarea creada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear la tarea: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $auth_user = User::with('roles')->find(session('user_id'));

        // Validar permisos si la tarea pertenece a una actividad
        if ($tarea->actividad_id) {
            $actividad = $tarea->actividad;
            $proyecto = $actividad->proyecto;
            
            if (!$proyecto->puedeGestionarActividadesYTareas($auth_user)) {
                abort(403, 'No tienes permisos para editar esta tarea.');
            }
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date',
            'hora_fin' => 'nullable|date_format:H:i',
            'prioridad' => 'nullable|in:baja,media,alta',
            'estado' => 'nullable|in:pendiente,en_progreso,completado',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:users,id'
        ]);

        try {
            // Obtener usuarios asignados antes de actualizar
            $usuariosAnteriores = $tarea->usuariosAsignados->pluck('id')->toArray();
            
            // Preparar datos usando método del modelo
            $data = $tarea->prepararDatosActualizacion($data);
            $tarea->actualizarConUsuarios($data, $data['usuarios'] ?? null);
            
            // Recargar la tarea para obtener los nuevos usuarios asignados
            $tarea->refresh();
            $usuariosNuevos = $tarea->usuariosAsignados->pluck('id')->toArray();
            
            // Enviar notificaciones solo a usuarios nuevos
            if (!empty($data['usuarios'])) {
                $usuariosParaNotificar = array_diff($usuariosNuevos, $usuariosAnteriores);
                if (!empty($usuariosParaNotificar)) {
                    $notificationService = new NotificationService();
                    foreach ($usuariosParaNotificar as $userId) {
                        $usuario = User::find($userId);
                        if ($usuario) {
                            $notificationService->enviarTareaAsignada($tarea, $usuario);
                        }
                    }
                }
            }
            
            return back()->with('success', 'Tarea actualizada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al actualizar la tarea: ' . $e->getMessage());
        }
    }

    public function toggleCompletada(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $tarea->toggleCompletada();

        $mensaje = $tarea->estaCompletada() 
            ? 'Tarea marcada como completada.' 
            : 'Tarea marcada como pendiente.';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'estado' => $tarea->estado
            ]);
        }

        return back()->with('success', $mensaje);
    }

    public function toggleEstadoPendienteEnProgreso(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $userId = session('user_id');

        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado.'
                ], 401);
            }
            return back()->with('error', 'Usuario no autenticado.');
        }

        $data = $request->validate([
            'estado' => 'required|in:pendiente,en_progreso,completado'
        ]);

        // Verificar que el usuario esté asignado a la tarea
        if (!$tarea->usuarioAsignado($userId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para cambiar el estado de esta tarea.'
                ], 403);
            }
            return back()->with('error', 'No tienes permisos para cambiar el estado de esta tarea.');
        }

        $tarea->update(['estado' => $data['estado']]);
        $tarea->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado correctamente.',
                'estado' => $tarea->estado
            ]);
        }

        return back()->with('success', 'Estado actualizado correctamente.');
    }

    public function updateFecha(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $userId = session('user_id');

        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado.'
                ], 401);
            }
            return back()->with('error', 'Usuario no autenticado.');
        }

        $data = $request->validate([
            'fecha_fin' => 'nullable|date',
            'hora_fin' => 'nullable|date_format:H:i'
        ]);

        // Verificar que el usuario esté asignado a la tarea
        if (!$tarea->usuarioAsignado($userId)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para cambiar la fecha de esta tarea.'
                ], 403);
            }
            return back()->with('error', 'No tienes permisos para cambiar la fecha de esta tarea.');
        }

        // Actualizar fecha usando método del modelo
        $tarea->actualizarFecha($data);
        $tarea->refresh();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fecha actualizada correctamente.',
                'fecha_fin' => $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d') : null,
                'hora_fin' => $tarea->fecha_fin ? $tarea->fecha_fin->format('H:i') : null
            ]);
        }

        return back()->with('success', 'Fecha actualizada correctamente.');
    }

    public function asignarUsuarios(Request $request, $id)
    {
        $data = $request->validate([
            'usuarios' => 'required|array',
            'usuarios.*' => 'exists:users,id'
        ]);

        $tarea = Tarea::findOrFail($id);
        $tarea->asignarUsuarios($data['usuarios']);

        return back()->with('success', 'Usuarios asignados correctamente.');
    }

    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);
        $auth_user = User::with('roles')->find(session('user_id'));

        // Validar permisos si la tarea pertenece a una actividad
        if ($tarea->actividad_id) {
            $actividad = $tarea->actividad;
            $proyecto = $actividad->proyecto;
            
            if (!$proyecto->puedeGestionarActividadesYTareas($auth_user)) {
                abort(403, 'No tienes permisos para eliminar esta tarea.');
            }
        }

        $tarea->eliminar();
        return back()->with('success', 'Tarea eliminada correctamente.');
    }

    public function updateDescripcion(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $data = $request->validate([
            'descripcion' => 'nullable|string'
        ]);

        $tarea->actualizarDescripcion($data['descripcion'] ?? '');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Descripción actualizada correctamente.'
            ]);
        }

        return back()->with('success', 'Descripción actualizada correctamente.');
    }

    public function updateComentarios(Request $request, $id)
    {
        $tarea = Tarea::findOrFail($id);
        $data = $request->validate([
            'comentario' => 'required|string'
        ]);

        $userId = session('user_id');
        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado.'
                ], 401);
            }
            return back()->with('error', 'Usuario no autenticado.');
        }

        try {
            $comentario = $tarea->agregarComentario($data['comentario'], $userId);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Comentario agregado correctamente.',
                    'comentario' => [
                        'id' => $comentario->id,
                        'comentario' => $comentario->comentario,
                        'user_id' => $comentario->user_id,
                        'created_at' => $comentario->created_at
                    ]
                ]);
            }

            return back()->with('success', 'Comentario agregado correctamente.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al agregar comentario: ' . $e->getMessage()
                ], 500);
            }
            return back()->with('error', 'Error al agregar comentario: ' . $e->getMessage());
        }
    }

    /**
     * Obtener descripción de una tarea
     */
    public function obtenerDescripcion($id)
    {
        $descripcion = Tarea::obtenerDescripcion($id);

        return response()->json([
            'success' => true,
            'descripcion' => $descripcion ?? ''
        ]);
    }

    /**
     * Obtener todos los comentarios de una tarea
     */
    public function obtenerComentarios($id)
    {
        $tarea = Tarea::findOrFail($id);
        $comentarios = $tarea->comentarios()
            ->with('usuario:id,nombre')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($comentario) {
                return [
                    'id' => $comentario->id,
                    'comentario' => $comentario->comentario,
                    'usuario_nombre' => $comentario->usuario->nombre ?? 'Usuario desconocido',
                    'user_id' => $comentario->user_id,
                    'created_at' => $comentario->created_at->format('Y-m-d H:i:s'),
                    'created_at_formatted' => $comentario->created_at->format('d/m/Y') . ' ' . $comentario->created_at->format('g:i A')
                ];
            });

        return response()->json([
            'success' => true,
            'comentarios' => $comentarios
        ]);
    }
}
