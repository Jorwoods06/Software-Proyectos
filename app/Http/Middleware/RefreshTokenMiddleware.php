<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class RefreshTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $refreshToken = $request->bearerToken() ?? session('jwt_refresh_token');

        if (!$refreshToken) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Refresh token requerido'], 401);
            }
            return redirect()->route('login')->with('error', 'Refresh token requerido');
        }

        try {
            $credentials = JWT::decode($refreshToken, new Key(env('JWT_REFRESH_SECRET'), 'HS256'));
            $request->merge(['auth_user_id' => $credentials->sub]);
        } catch (Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Refresh token inválido o expirado'], 401);
            }
            return redirect()->route('login')->with('error', 'Refresh token inválido o expirado');
        }

        return $next($request);
    }
}
