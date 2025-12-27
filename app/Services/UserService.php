<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UserService
{
    /** 🟢 Crear usuario con roles y heredar permisos */
    public function crearUsuarioConRolYPermisos(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'nombre'       => $data['nombre'],
                'email'        => $data['email'],
                'password'     => bcrypt($data['password']),
                'departamento' => $data['departamento'],
                'estado'       => $data['estado'] ?? 'activo',
            ]);

            if (!empty($data['roles'])) {
                $user->roles()->sync($data['roles']);

                // Heredar permisos de los roles
                $roles = Role::whereIn('id', $data['roles'])->with('permisos')->get();
                foreach ($roles as $role) {
                    foreach ($role->permisos as $permiso) {
                        $user->permisosDirectos()->syncWithoutDetaching([$permiso->id => ['tipo' => 'allow']]);
                    }
                }
            }

            return $user;
        });
    }

    /** 🟢 Actualizar datos básicos del usuario */
    public function actualizarDatosBasicos(User $user, array $data)
    {
        $user->fill([
            'nombre' => $data['nombre'],
            'email' => $data['email'],
            'estado' => $data['estado'],
            'departamento' => $data['departamento'],
        ]);

        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }

        $user->save();

        // Si hay roles en la actualización básica
        if (isset($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        return $user;
    }

    /** 🟢 Actualizar roles y permisos */
    public function actualizarRolesYPermisos(User $user, array $roles = [], array $permisos = [], array $tipos = [])
    {
        DB::transaction(function () use ($user, $roles, $permisos, $tipos) {

            // Actualizar roles
            $user->roles()->sync($roles ?? []);

            // Construir array pivot permisos
            $permisosData = [];
            foreach ($permisos as $permisoId) {
                $tipo = $tipos[$permisoId] ?? 'allow';
                $permisosData[$permisoId] = ['tipo' => $tipo];
            }

            // Sincronizar permisos directos
            $user->permisosDirectos()->sync($permisosData);
        });
    }

    /** 🟢 Duplicar roles y permisos entre usuarios */
    public function duplicarRolesYPermisos(User $from, User $to)
    {
        DB::transaction(function () use ($from, $to) {
            $to->roles()->sync($from->roles->pluck('id'));

            $pivotData = $from->permisosDirectos->mapWithKeys(function ($permiso) {
                return [$permiso->id => ['tipo' => $permiso->pivot->tipo]];
            });

            $to->permisosDirectos()->sync($pivotData);
        });
    }

    /** 🟢 Eliminar usuario de forma segura */
    public function eliminarUsuario(User $user)
    {
        DB::transaction(function () use ($user) {
            $user->roles()->detach();
            $user->permisosDirectos()->detach();
            $user->delete();
        });
    }
}
