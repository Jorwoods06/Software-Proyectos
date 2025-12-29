<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Proyecto;
use App\Models\Permisos;
use App\Models\Departamentos;
use App\Models\ProyectoUsuario;
use App\Models\ProyectoUserPermiso;
use App\Models\Trazabilidad;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ProyectoController extends Controller
{
   public function index()
{
    $auth_user = User::with('roles')->find(session('user_id'));
    if (!$auth_user) {
        return redirect()->route('login');
    }

    $proyectos = Proyecto::obtenerVisiblesPorUsuario($auth_user);
    $permisosDisponibles = Permisos::orderBy('nombre')->get();

    return view('proyectos.index', [
        'proyectos' => $proyectos,
        'auth_user' => $auth_user,
        'permisosDisponibles' => $permisosDisponibles,
    ]);
}




    public function create()
    {
        $departamentos = Departamentos::obtenerConUsuariosActivos();
        return view('proyectos.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:150',
            'descripcion'  => 'nullable|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'colaboradores' => 'nullable|array',
            'colaboradores.*.user_id' => 'required|exists:users,id',
            'colaboradores.*.rol_proyecto' => 'nullable|in:lider,colaborador,visor',
        ]);

        $usuario = User::find(session('user_id'));
        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado.');
        }

        $data['departamento_id'] = $usuario->departamento;

        try {
            $proyecto = Proyecto::crearConColaboradores(
                $data,
                $usuario->id,
                $data['colaboradores'] ?? null
            );

            Trazabilidad::create([
                'proyecto_id' => $proyecto->id,
                'user_id'     => $usuario->id,
                'accion'      => 'Creó el proyecto: ' . $proyecto->nombre
            ]);

            return redirect()->route('proyectos.index')
                ->with('success', 'Proyecto creado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el proyecto: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $usuario = User::find(session('user_id'));
        
        $proyecto = Proyecto::findOrFail($id);
        Trazabilidad::create([
            'proyecto_id' => $proyecto->id,
            'user_id'     => $usuario->id,
            'accion'      => 'Creó el proyecto: ' . $proyecto->nombre
        ]);

        // En edición ya no necesitamos la lista de usuarios
        return view('proyectos.edit', compact('proyecto'));
    }

 

public function update(Request $request, $id)
{
    $data = $request->validate([
        'nombre'       => 'required|string|max:255',
        'descripcion'  => 'nullable|string',
        'fecha_inicio' => 'required|date',
        'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
        'justificacion_fecha' => 'nullable|string|min:10'
    ]);

    $proyecto = Proyecto::findOrFail($id);
    $usuario = User::find(session('user_id'));

    // Detectar cambios
    $cambios = $proyecto->detectarCambios($data);

    // Si hay cambio de fechas, exigir justificación
    if (
        (isset($cambios['fecha_inicio']) || isset($cambios['fecha_fin'])) &&
        empty($data['justificacion_fecha'])
    ) {
        return back()
            ->withErrors(['justificacion_fecha' => 'Debes justificar el cambio de fechas'])
            ->withInput();
    }

    // Asignar color si no tiene uno
    if (empty($proyecto->color)) {
        $colors = [
            '#0D6EFD', '#6F42C1', '#D63384', '#DC3545', '#FD7E14',
            '#FFC107', '#20C997', '#198754', '#0DCAF0', '#6610F2',
            '#E83E8C', '#6C757D', '#0DCAF0', '#FF6B6B', '#4ECDC4',
            '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE'
        ];
        $data['color'] = $colors[($proyecto->id - 1) % count($colors)];
    }

    unset($data['justificacion_fecha']);
    $proyecto->update($data);

    // Registrar trazabilidad si hay cambios
    if (!empty($cambios)) {
        $detalle = "Modificaciones:\n";
        foreach ($cambios as $campo => $valores) {
            $detalle .= "- {$campo}: '{$valores['antes']}' → '{$valores['despues']}'\n";
        }

        if (!empty($request->input('justificacion_fecha'))) {
            $detalle .= "Justificación cambio de fechas: {$request->input('justificacion_fecha')}\n";
        }

        Trazabilidad::create([
            'proyecto_id' => $proyecto->id,
            'user_id'     => $usuario->id,
            'accion'      => "Actualizó el proyecto: {$proyecto->nombre}",
            'detalle'     => $detalle,
            'fecha'       => now()
        ]);
    }

    return redirect()->route('proyectos.index')
        ->with('success', 'Proyecto actualizado correctamente.');
}


    public function destroy($id)
    {
        $usuario = User::with('roles')->find(session('user_id'));

        if (!$usuario) {
            return back()->with('error', 'Usuario no encontrado.');
        }

        // Obtener nombres de los roles como array
        $roles = $usuario->roles->pluck('nombre')->toArray();

        // Verificar si tiene los permisos necesarios
        if (!in_array('TI', $roles) && !in_array('Administrador', $roles)) {
            return redirect()->route('proyectos.index')
                ->with('error', 'No tiene permisos para cancelar proyectos.');
        }

        $proyecto = Proyecto::findOrFail($id);
        $proyecto->estado = 'cancelado';
        $proyecto->save();

        // Registrar trazabilidad
        Trazabilidad::create([
            'proyecto_id' => $proyecto->id,
            'user_id'     => $usuario->id,
            'accion'      => 'Canceló el proyecto: ' . $proyecto->nombre
        ]);

        return redirect()->route('proyectos.index')
            ->with('success', 'Proyecto cancelado correctamente.');
    }

    // Mostrar modal: listar usuarios por departamento (AJAX)
   public function usuariosPorDepartamento($proyectoId)
{
    $proyecto = Proyecto::findOrFail($proyectoId);
    
    // Obtener usuarios activos que NO están en el proyecto, agrupados por departamento
    $departamentos = Departamentos::with(['usuarios' => function($query) use ($proyectoId) {
        $query->where('estado', 'activo')
              ->whereNotIn('users.id', function($subQuery) use ($proyectoId) {
                  $subQuery->select('user_id')
                           ->from('proyecto_usuario')
                           ->where('proyecto_id', $proyectoId);
              })
              ->orderBy('nombre');
    }])
    ->whereHas('usuarios', function($query) use ($proyectoId) {
        $query->where('estado', 'activo')
              ->whereNotIn('users.id', function($subQuery) use ($proyectoId) {
                  $subQuery->select('user_id')
                           ->from('proyecto_usuario')
                           ->where('proyecto_id', $proyectoId);
              });
    })
    ->orderBy('nombre')
    ->get();

    return response()->json($departamentos);
}



    // Invitar usuario a proyecto (con sistema de invitaciones)
   public function invitarUsuario(Request $request, $proyectoId)
{
    $data = $request->validate([
        'usuarios' => 'required|array',
        'usuarios.*.user_id' => 'required|exists:users,id',
        'usuarios.*.rol_proyecto' => 'required|string|in:lider,colaborador,visor',
        'usuarios.*.permisos' => 'nullable|array',
        'usuarios.*.permisos.*' => 'exists:permisos,id',
    ]);

    $proyecto = Proyecto::findOrFail($proyectoId);
    $usuarioInvitador = User::find(session('user_id'));
    $notificationService = new NotificationService();
    
    try {
        DB::beginTransaction();
        
        foreach ($data['usuarios'] as $usuarioData) {
            // Verificar si ya existe en el proyecto (aceptada o pendiente)
            $existeEnProyecto = ProyectoUsuario::where('proyecto_id', $proyectoId)
                ->where('user_id', $usuarioData['user_id'])
                ->whereIn('estado_invitacion', ['aceptada', 'pendiente'])
                ->exists();
            
            if ($existeEnProyecto) {
                continue;
            }
            
            // Crear invitación pendiente
            $invitacion = ProyectoUsuario::crearInvitacion(
                $proyectoId,
                $usuarioData['user_id'],
                $usuarioData['rol_proyecto']
            );
            
            // Guardar permisos para cuando acepte la invitación
            if (!empty($usuarioData['permisos'])) {
                foreach ($usuarioData['permisos'] as $permisoId) {
                    DB::table('proyecto_user_permiso')->updateOrInsert([
                        'proyecto_id' => $proyectoId,
                        'user_id' => $usuarioData['user_id'],
                        'permiso_id' => $permisoId,
                    ], [
                        'tipo' => 'allow'
                    ]);
                }
            }
            
            // Enviar correo de invitación
            $usuarioInvitado = User::find($usuarioData['user_id']);
            if ($usuarioInvitado) {
                $notificationService->enviarInvitacionProyecto(
                    $proyecto,
                    $usuarioInvitado,
                    $invitacion->token_invitacion,
                    $usuarioData['rol_proyecto']
                );
            }
            
            // Registrar trazabilidad
            if ($usuarioInvitador && $usuarioInvitado) {
                Trazabilidad::create([
                    'proyecto_id' => $proyectoId,
                    'user_id' => $usuarioInvitador->id,
                    'accion' => "Invitó a {$usuarioInvitado->nombre} al proyecto con rol {$usuarioData['rol_proyecto']}",
                    'detalle' => !empty($usuarioData['permisos']) ? 'Con permisos específicos asignados' : null,
                    'fecha' => now()
                ]);
            }
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Invitaciones enviadas correctamente.'
        ]);
        
    } catch (\Exception $e) {
        DB::rollback();
        
        return response()->json([
            'success' => false,
            'message' => 'Error al enviar invitaciones: ' . $e->getMessage()
        ], 500);
    }
}


    // Asignar permisos a un usuario dentro del proyecto
    public function asignarPermisoProyecto(Request $request, $proyectoId)
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'permiso_id' => 'required|exists:permisos,id',
            'tipo'       => 'required|in:allow,deny'
        ]);

        ProyectoUserPermiso::updateOrCreate(
            [
                'proyecto_id' => $proyectoId,
                'user_id'     => $data['user_id'],
                'permiso_id'  => $data['permiso_id'],
            ],
            ['tipo' => $data['tipo']]
        );

        return back()->with('success', 'Permiso asignado correctamente.');
    }


    public function verTrazabilidad($id)
    {
        $proyecto = Proyecto::with('departamento')->findOrFail($id);
        $usuario  = User::find(session('user_id'));

        // Verificar si es líder del proyecto
        $esLiderProyecto = $proyecto->usuarios()
            ->wherePivot('rol_proyecto', 'lider')
            ->where('users.id', $usuario->id)
            ->exists();

        // Verificar si es líder del departamento
        $esLiderDepartamento = $proyecto->departamento 
            && $proyecto->departamento->lider_id === $usuario->id;

        if (! $esLiderProyecto && ! $esLiderDepartamento) {
            return redirect()->route('proyectos.index')
                ->with('error', 'No tienes permisos para ver la trazabilidad de este proyecto.');
        }

        $trazas = Trazabilidad::where('proyecto_id', $proyecto->id)
                    ->with('usuario')
                    ->orderByDesc('fecha')
                    ->get();

        return view('proyectos.trazabilidad', compact('proyecto', 'trazas'));
    }

    public function obtenerUsuariosInvitables($proyectoId)
{
    // Usuarios activos no administradores
    $usuarios = User::where('estado', 'activo')
        ->where('rol', '!=', 'administrador')
        ->whereDoesntHave('proyectos', function($query) use ($proyectoId) {
            $query->where('proyecto_id', $proyectoId);
        })
        ->get(['id', 'nombre', 'correo']);

    return response()->json($usuarios);
}

}
