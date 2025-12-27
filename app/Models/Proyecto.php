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
    public function colaboradores(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'proyecto_usuario',
            'proyecto_id',
            'user_id'
        )->withPivot('rol_proyecto', 'created_at');
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

            // Registrar al usuario creador como líder
            ProyectoUsuario::create([
                'proyecto_id' => $proyecto->id,
                'user_id' => $usuarioCreadorId,
                'rol_proyecto' => 'lider'
            ]);

            // Asignar colaboradores si se proporcionaron
            if (!empty($colaboradores)) {
                foreach ($colaboradores as $colaboradorData) {
                    // Evitar duplicar al creador
                    if ($colaboradorData['user_id'] == $usuarioCreadorId) {
                        continue;
                    }

                    ProyectoUsuario::create([
                        'proyecto_id' => $proyecto->id,
                        'user_id' => $colaboradorData['user_id'],
                        'rol_proyecto' => $colaboradorData['rol_proyecto'] ?? 'colaborador'
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
    return static::where('estado', '!=', 'cancelado')
        ->whereHas('colaboradores', function ($query) use ($userId) {
            $query->where('users.id', $userId);
        })
        ->with(['colaboradores', 'actividades.tareas'])
        ->orderBy('created_at', 'desc')
        ->get();
}

    /** Obtener proyectos visibles para un usuario */
    public static function obtenerVisiblesPorUsuario(User $usuario)
{
    return static::with([
        'usuarios' => function ($q) {
            $q->withPivot('rol_proyecto');
        }
    ])
    ->where('estado', '!=', 'cancelado')
    ->where(function ($query) use ($usuario) {
        $query
            ->where('departamento_id', $usuario->departamento)
            ->orWhereHas('usuarios', function ($q) use ($usuario) {
                $q->where('users.id', $usuario->id);
            })
            ->orWhereHas('usuarios', function ($q) use ($usuario) {
                $q->where('users.id', $usuario->id)
                  ->where('proyecto_usuario.rol_proyecto', 'lider');
            });
    })
    ->latest()
    ->paginate(10);
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
