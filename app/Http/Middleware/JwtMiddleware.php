<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Exception;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Buscar primero en header, luego en sesión
        $token = $request->bearerToken() ?? session('jwt_token');

        if (!$token) {
            // No hay token
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'No se encontró un token de autenticación o tu sesión ha caducado'
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'Tu sesión ha caducado, por favor inicia sesión nuevamente');
        }

        try {
            $credentials = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            $request->merge(['auth_user_id' => $credentials->sub]);
        } catch (Exception $e) {
            // Token inválido, manipulado o expirado
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'El token es inválido o ha expirado, por favor vuelve a iniciar sesión'
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('error', 'El token de seguridad ha expirado o no es válido, inicia sesión nuevamente');
        }

        return $next($request);
    }
}
