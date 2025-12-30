<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Actividad;
use App\Models\Proyecto;
use App\Models\User;
use App\Models\Comentario;
use App\Models\Trazabilidad;
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
        // Verificar que el usuario no sea Auditor
        $auth_user = User::with('roles')->find(session('user_id'));
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los usuarios con rol Auditor no pueden crear tareas. Solo tienen permisos de lectura.'
                ], 403);
            }
            return back()->with('error', 'Los usuarios con rol Auditor no pueden crear tareas. Solo tienen permisos de lectura.');
        }

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

            // Filtrar usuarios con rol Lector (no pueden recibir tareas)
            if (!empty($data['usuarios'])) {
                $usuariosValidos = [];
                foreach ($data['usuarios'] as $userId) {
                    $usuario = User::with(['roles.permisos', 'permisosDirectos'])->find($userId);
                    if ($usuario) {
                        // Excluir usuarios con rol Lector
                        if (!$usuario->hasRole('Lector') && !$usuario->hasRole('lector')) {
                            $usuariosValidos[] = $userId;
                        }
                    }
                }
                $data['usuarios'] = $usuariosValidos;
            }

            // Preparar datos para la tarea usando método del modelo
            $tareaData = Tarea::prepararDatosCreacion($data, session('user_id'));
            $tarea = Tarea::crearConUsuarios($tareaData, $data['usuarios'] ?? null);
            
            // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto
            if ($tarea && !empty($data['actividad_id'])) {
                $actividad = Actividad::find($data['actividad_id']);
                if ($actividad && $actividad->proyecto_id) {
                    $usuariosAsignados = !empty($data['usuarios']) 
                        ? User::whereIn('id', $data['usuarios'])->pluck('nombre')->implode(', ')
                        : 'Ninguno';
                    
                    Trazabilidad::create([
                        'proyecto_id' => $actividad->proyecto_id,
                        'user_id' => session('user_id'),
                        'accion' => "Creó la tarea: {$tarea->nombre}",
                        'detalle' => "Fase: {$actividad->nombre}\nPrioridad: " . ($tarea->prioridad ?? 'media') . "\nEstado: " . ($tarea->estado ?? 'pendiente') . "\nUsuarios asignados: {$usuariosAsignados}",
                        'fecha' => now()
                    ]);
                }
            }
            
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

        // Verificar que el usuario no sea Auditor
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los usuarios con rol Auditor no pueden editar tareas. Solo tienen permisos de lectura.'
                ], 403);
            }
            abort(403, 'Los usuarios con rol Auditor no pueden editar tareas. Solo tienen permisos de lectura.');
        }

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
            // Filtrar usuarios con rol Lector o Auditor (no pueden recibir tareas)
            if (!empty($data['usuarios'])) {
                $usuariosValidos = [];
                foreach ($data['usuarios'] as $userId) {
                    $usuario = User::with(['roles.permisos', 'permisosDirectos'])->find($userId);
                    if ($usuario) {
                        // Excluir usuarios con rol Lector o Auditor
                        if (!$usuario->hasRole('Lector') && !$usuario->hasRole('lector')
                            && !$usuario->hasRole('Auditor') && !$usuario->hasRole('auditor')) {
                            $usuariosValidos[] = $userId;
                        }
                    }
                }
                $data['usuarios'] = $usuariosValidos;
            }

            // Guardar valores anteriores para trazabilidad
            $valoresAnteriores = [
                'nombre' => $tarea->nombre,
                'descripcion' => $tarea->descripcion,
                'fecha_fin' => $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d H:i') : null,
                'prioridad' => $tarea->prioridad,
                'estado' => $tarea->estado,
            ];
            $usuariosAnteriores = $tarea->usuariosAsignados->pluck('id')->toArray();
            $nombresUsuariosAnteriores = $tarea->usuariosAsignados->pluck('nombre')->implode(', ') ?: 'Ninguno';
            
            // Preparar datos usando método del modelo
            $data = $tarea->prepararDatosActualizacion($data);
            $tarea->actualizarConUsuarios($data, $data['usuarios'] ?? null);
            
            // Recargar la tarea para obtener los nuevos usuarios asignados
            $tarea->refresh();
            $usuariosNuevos = $tarea->usuariosAsignados->pluck('id')->toArray();
            $nombresUsuariosNuevos = $tarea->usuariosAsignados->pluck('nombre')->implode(', ') ?: 'Ninguno';
            
            // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto
            if ($tarea->actividad_id) {
                $actividad = $tarea->actividad;
                if ($actividad && $actividad->proyecto_id) {
                    $cambios = [];
                    
                    // Detectar cambios comparando valores anteriores con los nuevos de la tarea actualizada
                    if ($valoresAnteriores['nombre'] != $tarea->nombre) {
                        $cambios[] = "Nombre: '{$valoresAnteriores['nombre']}' → '{$tarea->nombre}'";
                    }
                    if ($valoresAnteriores['descripcion'] != ($tarea->descripcion ?? '')) {
                        $cambios[] = "Descripción modificada";
                    }
                    $fechaNueva = $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d H:i') : null;
                    if ($valoresAnteriores['fecha_fin'] != $fechaNueva) {
                        $cambios[] = "Fecha fin: '" . ($valoresAnteriores['fecha_fin'] ?? 'Sin fecha') . "' → '" . ($fechaNueva ?? 'Sin fecha') . "'";
                    }
                    if ($valoresAnteriores['prioridad'] != $tarea->prioridad) {
                        $cambios[] = "Prioridad: '{$valoresAnteriores['prioridad']}' → '{$tarea->prioridad}'";
                    }
                    if ($valoresAnteriores['estado'] != $tarea->estado) {
                        $cambios[] = "Estado: '{$valoresAnteriores['estado']}' → '{$tarea->estado}'";
                    }
                    if ($nombresUsuariosAnteriores != $nombresUsuariosNuevos) {
                        $cambios[] = "Usuarios asignados: '{$nombresUsuariosAnteriores}' → '{$nombresUsuariosNuevos}'";
                    }
                    
                    if (!empty($cambios)) {
                        Trazabilidad::create([
                            'proyecto_id' => $actividad->proyecto_id,
                            'user_id' => session('user_id'),
                            'accion' => "Editó la tarea: {$tarea->nombre}",
                            'detalle' => "Fase: {$actividad->nombre}\n" . implode("\n", $cambios),
                            'fecha' => now()
                        ]);
                    }
                }
            }
            
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
        $estadoAnterior = $tarea->estado;
        $tarea->toggleCompletada();
        $tarea->refresh();

        $mensaje = $tarea->estaCompletada() 
            ? 'Tarea marcada como completada.' 
            : 'Tarea marcada como pendiente.';

        // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto
        if ($tarea->actividad_id) {
            $actividad = $tarea->actividad;
            if ($actividad && $actividad->proyecto_id) {
                Trazabilidad::create([
                    'proyecto_id' => $actividad->proyecto_id,
                    'user_id' => session('user_id'),
                    'accion' => $tarea->estaCompletada() 
                        ? "Completó la tarea: {$tarea->nombre}"
                        : "Marcó como pendiente la tarea: {$tarea->nombre}",
                    'detalle' => "Fase: {$actividad->nombre}\nEstado anterior: {$estadoAnterior}\nEstado nuevo: {$tarea->estado}",
                    'fecha' => now()
                ]);
            }
        }

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

        $estadoAnterior = $tarea->estado;
        $tarea->update(['estado' => $data['estado']]);
        $tarea->refresh();

        // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto
        if ($tarea->actividad_id && $estadoAnterior != $data['estado']) {
            $actividad = $tarea->actividad;
            if ($actividad && $actividad->proyecto_id) {
                Trazabilidad::create([
                    'proyecto_id' => $actividad->proyecto_id,
                    'user_id' => $userId,
                    'accion' => "Cambió el estado de la tarea: {$tarea->nombre}",
                    'detalle' => "Fase: {$actividad->nombre}\nEstado anterior: {$estadoAnterior}\nEstado nuevo: {$data['estado']}",
                    'fecha' => now()
                ]);
            }
        }

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

        $fechaAnterior = $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d H:i') : null;
        
        // Actualizar fecha usando método del modelo
        $tarea->actualizarFecha($data);
        $tarea->refresh();
        
        $fechaNueva = $tarea->fecha_fin ? $tarea->fecha_fin->format('Y-m-d H:i') : null;

        // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto y hubo cambio
        if ($tarea->actividad_id && $fechaAnterior != $fechaNueva) {
            $actividad = $tarea->actividad;
            if ($actividad && $actividad->proyecto_id) {
                Trazabilidad::create([
                    'proyecto_id' => $actividad->proyecto_id,
                    'user_id' => $userId,
                    'accion' => "Actualizó la fecha de la tarea: {$tarea->nombre}",
                    'detalle' => "Fase: {$actividad->nombre}\nFecha anterior: " . ($fechaAnterior ?? 'Sin fecha') . "\nFecha nueva: " . ($fechaNueva ?? 'Sin fecha'),
                    'fecha' => now()
                ]);
            }
        }

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

        // Filtrar usuarios con rol Lector o Auditor (no pueden recibir tareas)
        $usuariosValidos = [];
        foreach ($data['usuarios'] as $userId) {
            $usuario = User::with(['roles.permisos', 'permisosDirectos'])->find($userId);
            if ($usuario) {
                // Excluir usuarios con rol Lector o Auditor
                if (!$usuario->hasRole('Lector') && !$usuario->hasRole('lector')
                    && !$usuario->hasRole('Auditor') && !$usuario->hasRole('auditor')) {
                    $usuariosValidos[] = $userId;
                }
            }
        }

        if (empty($usuariosValidos)) {
            return back()->with('error', 'Ninguno de los usuarios seleccionados puede recibir tareas (usuarios con rol Lector o Auditor no pueden recibir tareas).');
        }

        $tarea = Tarea::findOrFail($id);
        $usuariosAnteriores = $tarea->usuariosAsignados->pluck('nombre')->implode(', ') ?: 'Ninguno';
        $tarea->asignarUsuarios($usuariosValidos);
        $tarea->refresh();
        $usuariosNuevos = $tarea->usuariosAsignados->pluck('nombre')->implode(', ') ?: 'Ninguno';

        // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto
        if ($tarea->actividad_id && $usuariosAnteriores != $usuariosNuevos) {
            $actividad = $tarea->actividad;
            if ($actividad && $actividad->proyecto_id) {
                Trazabilidad::create([
                    'proyecto_id' => $actividad->proyecto_id,
                    'user_id' => session('user_id'),
                    'accion' => "Asignó usuarios a la tarea: {$tarea->nombre}",
                    'detalle' => "Fase: {$actividad->nombre}\nUsuarios anteriores: {$usuariosAnteriores}\nUsuarios nuevos: {$usuariosNuevos}",
                    'fecha' => now()
                ]);
            }
        }

        return back()->with('success', 'Usuarios asignados correctamente.');
    }

    public function destroy($id)
    {
        $tarea = Tarea::findOrFail($id);
        $auth_user = User::with('roles')->find(session('user_id'));

        // Verificar que el usuario no sea Auditor
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            abort(403, 'Los usuarios con rol Auditor no pueden eliminar tareas. Solo tienen permisos de lectura.');
        }

        // Validar permisos si la tarea pertenece a una actividad
        if ($tarea->actividad_id) {
            $actividad = $tarea->actividad;
            $proyecto = $actividad->proyecto;
            
            if (!$proyecto->puedeGestionarActividadesYTareas($auth_user)) {
                abort(403, 'No tienes permisos para eliminar esta tarea.');
            }
            
            // Registrar trazabilidad antes de eliminar
            if ($actividad && $actividad->proyecto_id) {
                Trazabilidad::create([
                    'proyecto_id' => $actividad->proyecto_id,
                    'user_id' => $auth_user->id,
                    'accion' => "Eliminó la tarea: {$tarea->nombre}",
                    'detalle' => "Fase: {$actividad->nombre}\nEstado: {$tarea->estado}\nPrioridad: {$tarea->prioridad}",
                    'fecha' => now()
                ]);
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

        $descripcionAnterior = $tarea->descripcion;
        $tarea->actualizarDescripcion($data['descripcion'] ?? '');
        $tarea->refresh();

        // Registrar trazabilidad si la tarea pertenece a una actividad/proyecto y hubo cambio
        if ($tarea->actividad_id && $descripcionAnterior != ($data['descripcion'] ?? '')) {
            $actividad = $tarea->actividad;
            if ($actividad && $actividad->proyecto_id) {
                Trazabilidad::create([
                    'proyecto_id' => $actividad->proyecto_id,
                    'user_id' => session('user_id'),
                    'accion' => "Actualizó la descripción de la tarea: {$tarea->nombre}",
                    'detalle' => "Fase: {$actividad->nombre}",
                    'fecha' => now()
                ]);
            }
        }
        
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

        // Verificar que el usuario no sea Auditor
        $auth_user = User::with(['roles.permisos', 'permisosDirectos'])->find($userId);
        if ($auth_user && ($auth_user->hasRole('Auditor') || $auth_user->hasRole('auditor'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Los usuarios con rol Auditor no pueden comentar en tareas. Solo tienen permisos de lectura.'
                ], 403);
            }
            return back()->with('error', 'Los usuarios con rol Auditor no pueden comentar en tareas. Solo tienen permisos de lectura.');
        }

        // Validar que el usuario tenga permiso "comentar tarea"
        if (!$auth_user || !$auth_user->hasPermission('comentar tarea')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para comentar en tareas.'
                ], 403);
            }
            return back()->with('error', 'No tienes permiso para comentar en tareas.');
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
