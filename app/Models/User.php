<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'email',
        'password',
        'departamento',
        'estado',
        'color'
    ];

    // Relaciones

    // Relación con roles 
    public function roles()
{
    return $this->belongsToMany(
        Role::class,
        'user_role',      // tabla pivote
        'user_id',        // FK en pivote hacia users
        'rol_id'         // FK en pivote hacia roles
    );
}


    // Relación con el departamento
    public function departamento(): BelongsTo
    {
        return $this->belongsTo(
            Departamentos::class,
            foreignKey: 'departamento',
            ownerKey: 'id');
    }

    // Relación con los Permisos directos del usuario
    public function permisosDirectos(): BelongsToMany
    {
        return $this->belongsToMany(
            Permisos::class,
            'user_permiso',
            'user_id',
            'permiso_id'
        )->withPivot('tipo');
    }


     // Permisos finales = roles + directos
    public function getPermisosAttribute()
    {
        $fromRoles = $this->roles->load('permisos')->pluck('permisos')->flatten();
        return $fromRoles->merge($this->permisosDirectos)->unique('id');
    }
    
    // comprobar rol (case-insensitive)
    public function hasRole($rolNombre)
    {
        if (!$this->relationLoaded('roles')) {
            $this->load('roles');
        }
        
        // Comparación case-insensitive
        return $this->roles->contains(function ($rol) use ($rolNombre) {
            return strtolower($rol->nombre) === strtolower($rolNombre);
        });
    }

    // obtener todos los permisos a través de roles
    public function permisos()
    {
        return $this->roles()->with('permisos')->get()->flatMap->permisos->unique('id');
    }

    // comprobar permiso por nombre
    public function hasPermission(string $permisoNombre, string $requiredType = 'allow'): bool
    {
        // 1️⃣ Permisos directos
        foreach ($this->permisosDirectos as $permiso) {
            if ($permiso->nombre === $permisoNombre) {
                // deny directo siempre gana
                if ($permiso->pivot->tipo === 'deny') {
                    return false;
                }
                return $requiredType === 'allow' || $permiso->pivot->tipo === $requiredType;
            }
        }

        // 2️⃣ Permisos heredados de roles
        foreach ($this->roles as $rol) {
            foreach ($rol->permisos as $permiso) {
                if ($permiso->nombre === $permisoNombre) {
                    // deny en rol también gana
                    if ($permiso->pivot->tipo === 'deny') {
                        return false;
                    }
                    return $requiredType === 'allow' || $permiso->pivot->tipo === $requiredType;
                }
            }
        }

        return false;
    }



    // 🔐 Setter automático para siempre guardar con SHA512
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = hash('sha512', $value);
    }

    // ✅ Accessor: permite usar $user->departamento_nombre
    public function getDepartamentoNombreAttribute()
    {
        // Usar relationLoaded para verificar si la relación ya está cargada
        if ($this->relationLoaded('departamento')) {
            return $this->getRelation('departamento') 
                ? $this->getRelation('departamento')->nombre 
                : 'Sin departamento';
        }
        
        // Si no está cargada, cargarla dinámicamente
        return $this->departamento 
            ? $this->departamento->nombre 
            : 'Sin departamento';
    }

    // función para obtener usuarios activos que no sean admin
    public static function obtenerUsuariosActivos()
    {
        return self::where('estado', 'activo')
            ->where('rol', '!=', 'administrador') // aqui se puede ajustar segun sea necesario para excluir otros roles
            ->get(['id', 'nombre', 'correo']);
    }

    /** Obtener usuarios activos ordenados por nombre */
    public static function obtenerActivosOrdenados()
    {
        return self::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();
    }

}