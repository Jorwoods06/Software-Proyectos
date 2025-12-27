<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permiso)
    {
        // Verificar que haya un usuario en sesión
        if (!session()->has(key: 'user_id')) {
            return redirect()->route(route: 'login')->with(key: 'error', value: 'No autenticado');
        }

        // Buscar al usuario desde la DB
        $user = User::with(relations: ['roles.permisos', 'permisosDirectos'])->find(session('user_id'));


        if (!$user) {
            session()->flush();
            return redirect()->route(route: 'login')->with(key: 'error', value: 'Sesión inválida');
        }

        // Comprobar permiso o si es admin
        if (!$user->hasPermission(permisoNombre: $permiso) && !$user->hasRole(rolNombre: 'admin')) {
            abort(code: 403, message: 'No tienes permisos para realizar esta acción');
        }

        return $next($request);
    }
}
