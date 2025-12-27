<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class AttachUserFromSession
{
    /**
     * Inyecta el usuario autenticado en la request y en el contenedor global
     * usando session('user_id'), sin sobrescribir otros campos del request.
     */
    public function handle(Request $request, Closure $next)
    {
        $id = session('user_id');

        if ($id) {
            $user = User::find($id);

            if ($user) {
                // Solo lo guardamos como atributo de la request (NO sobrescribe inputs)
                // Acceso en controlador: $request->attributes->get('session_user')
                // $request->attributes->set('session_user', $user);

                // También lo dejamos disponible de forma global (no lo use por que inserta el usurio en las funciones de edicion)
                // Acceso en Blade/controladores: app('session_user')
              app()->instance('session_user', $user);
            }
        }

        return $next($request);
    }
}
