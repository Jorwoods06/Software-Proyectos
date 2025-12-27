<?php

use App\Models\User;
use App\Models\Proyecto;
use Illuminate\Support\Facades\Session;

/**
 * Comprueba si el usuario de sesión tiene un permiso (por rol o directo).
 * - Los permisos directos (tabla user_permiso) respetan pivot.tipo ('allow'|'deny').
 *   Si existe un 'deny' directo, prevalece (deny wins).
 * - Los permisos heredados por rol (rol_permiso) se consideran 'allow' por defecto,
 *   porque tu tabla `rol_permiso` **no** tiene columna `tipo`.
 *
 * @param string $permisoNombre
 * @param array  $opciones     Opciones extra: ['proyecto' => $proyecto] etc.
 * @return bool
 */
if (! function_exists('usuarioTienePermiso')) {
    function usuarioTienePermiso(string $permisoNombre, array $opciones = []): bool
    {
        // obtener id de usuario desde la sesión
        $userId = Session::get('user_id');
        if (! $userId) {
            return false;
        }

        // cargar usuario con relaciones (roles.permisos y permisos directos)
        /** @var User|null $usuario */
        $usuario = User::with(['roles.permisos', 'permisosDirectos'])->find($userId);
        if (! $usuario) {
            return false;
        }

        // bypass para Administrador (por rol)
        if ($usuario->hasRole('Administrador') || $usuario->hasRole('admin')) {
            return true;
        }

        // 1) Permisos directos del usuario (user_permiso) -> 'deny' gana
        foreach ($usuario->permisosDirectos as $permisoDirecto) {
            if ($permisoDirecto->nombre === $permisoNombre) {
                $tipo = $permisoDirecto->pivot->tipo ?? 'allow';
                if ($tipo === 'deny') {
                    // deny directo invalida todo
                    return false;
                }
                // allow directo -> validar extras si aplica
                return empty($opciones) || validacionExtra($usuario, $opciones);
            }
        }

        // 2) Permisos por rol (rol_permiso) -> treat as allow by default
        // Nota: tu tabla rol_permiso NO tiene campo 'tipo', por eso no comprobamos pivot->tipo aquí.
        foreach ($usuario->roles as $rol) {
            foreach ($rol->permisos as $permisoRol) {
                if ($permisoRol->nombre === $permisoNombre) {
                    return empty($opciones) || validacionExtra($usuario, $opciones);
                }
            }
        }

        return false;
    }
}

/**
 * Validaciones extra (ej. ser líder del proyecto o líder del departamento).
 * Acepta tanto un objeto User como un user_id (int|string).
 *
 * @param User|int|string $usuario
 * @param array $opciones
 * @return bool
 */
if (! function_exists('validacionExtra')) {
    function validacionExtra(User $usuario, array $opciones): bool
{
    if (isset($opciones['proyecto']) && $opciones['proyecto'] instanceof Proyecto) {
        $proyecto = $opciones['proyecto'];

        // líder del proyecto
        $esLiderProyecto = $proyecto->usuarios()
            ->wherePivot('rol_proyecto', 'lider')
            ->where('users.id', $usuario->id)
            ->exists();

        // líder del departamento
        $esLiderDepartamento = $proyecto->departamento
            && $proyecto->departamento->lider_id === $usuario->id;

        // invitado/colaborador
        $esInvitado = $proyecto->usuarios()
            ->where('users.id', $usuario->id)
            ->exists();

        return $esLiderProyecto || $esLiderDepartamento || $esInvitado;
    }

    return true;
}
}
