<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Proyecto extends Model
{
    protected $table = 'proyectos';
    public $timestamps = true;
    protected $fillable = [
        'nombre', 'descripcion', 'fecha_inicio', 'fecha_fin', 'estado', 'departamento_id', 'color', 'created_by'
    ];

    /**
     * Accessor para obtener el color del proyecto o generar uno basado en su ID
     */
    public function getColorAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        // Generar color basado en el ID del proyecto
        $colors = [
            '#0D6EFD', '#6F42C1', '#D63384', '#DC3545', '#FD7E14',
            '#FFC107', '#20C997', '#198754', '#0DCAF0', '#6610F2',
            '#E83E8C', '#6C757D', '#0DCAF0', '#FF6B6B', '#4ECDC4',
            '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE'
        ];
        
        $id = $this->attributes['id'] ?? $this->id ?? 1;
        return $colors[($id - 1) % count($colors)] ?? '#0D6EFD';
    }

    // Relación con usuarios (muchos a muchos) a través de proyecto_usuario
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'proyecto_usuario')
                    ->withPivot('rol_proyecto');
    }

    // Relación con el departamento
    public function departamento()
    {
        return $this->belongsTo(Departamentos::class, 'departamento_id');
    }

    // Relación con el usuario creador del proyecto
    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    
    // Actividades del proyecto
    public function actividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'proyecto_id');
    }

    // Colaboradores (usuarios) - pivot proyecto_usuario con rol_proyecto
    // Solo incluye usuarios con invitación aceptada
    public function colaboradores(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'proyecto_usuario',
            'proyecto_id',
            'user_id'
        )->wherePivot('estado_invitacion', 'aceptada')
         ->withPivot('rol_proyecto', 'created_at', 'estado_invitacion');
    }

    // Todos los usuarios del proyecto (incluyendo pendientes y rechazados)
    public function todosUsuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'proyecto_usuario',
            'proyecto_id',
            'user_id'
        )->withPivot('rol_proyecto', 'estado_invitacion', 'created_at');
    }

    // Permisos a nivel de proyecto para usuarios (tabla proyecto_user_permiso)
    public function permisosProyecto()
    {
        return $this->hasMany(ProyectoUserPermiso::class, 'proyecto_id');
    }

    // Comprueba si $user (User model o user id) tiene $permisoNombre sobre ESTE proyecto
    // Lógica: 1) buscar permiso a nivel de proyecto (directo) -> deny gana, allow permite
    //        2) si no hay permiso directo en proyecto, preguntar al user->hasPermission (global)
    //        3) Si el usuario es Administrador o creador del proyecto, tiene permisos completos
    public function userHasPermission($user, string $permisoNombre): bool
    {
        if (!$user) return false;
        $userId = $user instanceof \App\Models\User ? $user->id : (int) $user;
        $userModel = $user instanceof \App\Models\User ? $user : \App\Models\User::find($userId);
        if (!$userModel) return false;

        // Si el usuario es Administrador (por rol), permitimos directamente
        if ($userModel->hasRole('Administrador') || $userModel->hasRole('admin') || $userModel->hasRole('TI')) {
            return true;
        }

        // Si el usuario es el creador del proyecto, tiene permisos completos
        if ($this->created_by && $this->created_by == $userId) {
            return true;
        }

        // buscar permiso por nombre
        $permiso = \App\Models\Permisos::where('nombre', $permisoNombre)->first();
        if (!$permiso) {
            return false; // permiso no existe
        }

        // 1) permiso en nivel proyecto
        $p = ProyectoUserPermiso::where('proyecto_id', $this->id)
                ->where('user_id', $userId)
                ->where('permiso_id', $permiso->id)
                ->first();

        if ($p) {
            return $p->tipo === 'allow';
        }

        // 2) si no hay permiso a nivel del proyecto, se evalúa permiso global del usuario
        return $userModel->hasPermission($permisoNombre);
    }

    /**
     * Verificar si el usuario es el creador del proyecto
     */
    public function esCreadorPor($user): bool
    {
        if (!$user) return false;
        $userId = $user instanceof \App\Models\User ? $user->id : (int) $user;
        return $this->created_by && $this->created_by == $userId;
    }

    /**
     * Verificar si el usuario tiene acceso al proyecto (puede verlo)
     * (Administrador, TI, creador del proyecto o colaborador explícito)
     */
    public function usuarioTieneAcceso($user): bool
    {
        if (!$user) return false;
        $userId = $user instanceof \App\Models\User ? $user->id : (int) $user;
        $userModel = $user instanceof \App\Models\User ? $user : \App\Models\User::with('roles')->find($userId);
        if (!$userModel) return false;

        // Asegurar que los roles estén cargados
        if (!$userModel->relationLoaded('roles')) {
            $userModel->load('roles');
        }

        // Administrador o TI tienen acceso completo
        if ($userModel->hasRole('Administrador') || $userModel->hasRole('admin') || $userModel->hasRole('TI')) {
            return true;
        }

        // Auditor puede ver todos los proyectos (solo lectura)
        if ($userModel->hasRole('Auditor') || $userModel->hasRole('auditor')) {
            return true;
        }

        // El usuario es el creador del proyecto
        if ($this->esCreadorPor($userModel)) {
            return true;
        }

        // El usuario está explícitamente asignado como colaborador
        return $this->colaboradores()->where('users.id', $userId)->exists();
    }

    /**
     * Verificar si el usuario puede editar/eliminar actividades y tareas del proyecto
     * (Administrador, TI o creador del proyecto)
     */
    public function puedeGestionarActividadesYTareas($user): bool
    {
        if (!$user) return false;
        $userId = $user instanceof \App\Models\User ? $user->id : (int) $user;
        $userModel = $user instanceof \App\Models\User ? $user : \App\Models\User::find($userId);
        if (!$userModel) return false;

        // Administrador o TI tienen acceso completo
        if ($userModel->hasRole('Administrador') || $userModel->hasRole('admin') || $userModel->hasRole('TI')) {
            return true;
        }

        // El creador del proyecto tiene acceso completo
        return $this->esCreadorPor($userModel);
    }

    /** Generar color automático para el proyecto */
    public static function generarColorAutomatico(): string
    {
        $colors = [
            '#0D6EFD', '#6F42C1', '#D63384', '#DC3545', '#FD7E14',
            '#FFC107', '#20C997', '#198754', '#0DCAF0', '#6610F2',
            '#E83E8C', '#6C757D', '#0DCAF0', '#FF6B6B', '#4ECDC4',
            '#45B7D1', '#FFA07A', '#98D8C8', '#F7DC6F', '#BB8FCE'
        ];
        
        $nextId = static::max('id') ?? 0;
        $nextId += 1;
        
        return $colors[($nextId - 1) % count($colors)] ?? '#0D6EFD';
    }

    /** Crear proyecto con colaboradores */
    public static function crearConColaboradores(array $datosProyecto, int $usuarioCreadorId, ?array $colaboradores = null): self
    {
        return DB::transaction(function () use ($datosProyecto, $usuarioCreadorId, $colaboradores) {
            // Asignar color automático si no se proporciona
            if (empty($datosProyecto['color'])) {
                $datosProyecto['color'] = static::generarColorAutomatico();
            }

            // Asignar created_by antes de crear
            $datosProyecto['created_by'] = $usuarioCreadorId;
            $proyecto = static::create($datosProyecto);

            // Registrar al usuario creador como líder (aceptada automáticamente)
            ProyectoUsuario::create([
                'proyecto_id' => $proyecto->id,
                'user_id' => $usuarioCreadorId,
                'rol_proyecto' => 'lider',
                'estado_invitacion' => 'aceptada'
            ]);

            // Asignar colaboradores si se proporcionaron
            if (!empty($colaboradores)) {
                foreach ($colaboradores as $colaboradorData) {
                    // Evitar duplicar al creador
                    if ($colaboradorData['user_id'] == $usuarioCreadorId) {
                        continue;
                    }

                    // Al crear proyecto, los colaboradores se asignan directamente (aceptada)
                    ProyectoUsuario::create([
                        'proyecto_id' => $proyecto->id,
                        'user_id' => $colaboradorData['user_id'],
                        'rol_proyecto' => $colaboradorData['rol_proyecto'] ?? 'colaborador',
                        'estado_invitacion' => 'aceptada'
                    ]);
                }
            }

            return $proyecto->load('colaboradores');
        });
    }

    /** Obtener proyecto con colaboradores cargados */
    public static function obtenerConColaboradores(int $proyectoId): ?self
    {
        return static::with('colaboradores')->find($proyectoId);
    }

    /** Obtener proyectos en los que participa un usuario */
    public static function obtenerPorUsuario(int $userId)
{
    $usuario = User::find($userId);
    if (!$usuario) {
        return collect([]);
    }
    
    // Verificar directamente en la base de datos si el usuario tiene rol Admin, TI o Auditor
    // Esto es más confiable que confiar solo en relaciones cargadas
    $rolesUsuario = DB::table('user_role')
        ->join('roles', 'user_role.rol_id', '=', 'roles.id')
        ->where('user_role.user_id', $userId)
        ->pluck('roles.nombre')
        ->map(function($nombre) {
            return strtolower($nombre);
        })
        ->toArray();
    
    $esAdmin = in_array('administrador', $rolesUsuario) || in_array('admin', $rolesUsuario) || in_array('ti', $rolesUsuario);
    $esAuditor = in_array('auditor', $rolesUsuario);
    
    $query = static::where('estado', '!=', 'cancelado');
    
    // Si es administrador o auditor, puede ver todos los proyectos (sin aplicar filtros)
    // Esta es la misma lógica que usa el admin
    if (!$esAdmin && !$esAuditor) {
        $query->where(function ($query) use ($userId) {
            // El usuario es el creador del proyecto
            $query->where('created_by', $userId)
                  // O el usuario está explícitamente asignado como colaborador en proyecto_usuario
                  ->orWhereHas('colaboradores', function ($q) use ($userId) {
                      $q->where('users.id', $userId);
                  });
        });
    }
    
    return $query->with(['colaboradores', 'actividades.tareas'])
        ->orderBy('created_at', 'desc')
        ->get();
}

    /** Obtener proyectos visibles para un usuario */
    public static function obtenerVisiblesPorUsuario(User $usuario)
{
    // Verificar directamente en la base de datos si el usuario tiene rol Admin, TI o Auditor
    // Esto es más confiable que confiar solo en relaciones cargadas
    $rolesUsuario = DB::table('user_role')
        ->join('roles', 'user_role.rol_id', '=', 'roles.id')
        ->where('user_role.user_id', $usuario->id)
        ->pluck('roles.nombre')
        ->map(function($nombre) {
            return strtolower($nombre);
        })
        ->toArray();
    
    $esAdmin = in_array('administrador', $rolesUsuario) || in_array('admin', $rolesUsuario) || in_array('ti', $rolesUsuario);
    $esAuditor = in_array('auditor', $rolesUsuario);
    
    $query = static::with([
        'colaboradores' => function ($q) {
            $q->withPivot('rol_proyecto');
        }
    ])
    ->where('estado', '!=', 'cancelado');
    
    // Si es administrador o auditor, puede ver todos los proyectos (sin aplicar filtros)
    // Esta es la misma lógica que usa el admin - simplemente no aplicamos el where adicional
    if (!$esAdmin && !$esAuditor) {
        $query->where(function ($query) use ($usuario) {
            // El usuario es el creador del proyecto
            $query->where('created_by', $usuario->id)
                  // O el usuario está explícitamente asignado como colaborador con invitación aceptada
                  ->orWhereHas('colaboradores', function ($q) use ($usuario) {
                      $q->where('users.id', $usuario->id)
                        ->where('proyecto_usuario.estado_invitacion', 'aceptada');
                  });
        });
    }
    
    return $query->latest()->paginate(10);
}

    /** Detectar cambios en un proyecto */
    public function detectarCambios(array $data): array
    {
        $cambios = [];
        foreach (['nombre', 'descripcion', 'fecha_inicio', 'fecha_fin'] as $campo) {
            if ($this->{$campo} != $data[$campo]) {
                $cambios[$campo] = [
                    'antes' => $this->{$campo},
                    'despues' => $data[$campo],
                ];
            }
        }
        return $cambios;
    }

}
