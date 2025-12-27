<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleTiOrAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user(); // Si usas sesión manual, puedes hacer session('user_id')

        // Si tu sistema usa session('user_id') en lugar de auth():
        if (!$user && session()->has('user_id')) {
            $user = \App\Models\User::with('roles')->find(session('user_id'));
        }

        if (!$user || (! $user->hasRole('TI') && ! $user->hasRole('Administrador'))) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
