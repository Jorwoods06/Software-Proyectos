<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login'); // resources/views/login.blade.php
    }

    private function generateTokens($userId)
    {
        $payload = [
            'iss' => "gestion_proyectos",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + env('JWT_TTL', 3600) // 1h
        ];

        $refreshPayload = [
            'iss' => "gestion_proyectos",
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + env('JWT_REFRESH_TTL', 604800) // 7 días
        ];

        $token = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
        $refreshToken = JWT::encode($refreshPayload, env('JWT_REFRESH_SECRET'), 'HS256');

        return [
            'token' => $token,
            'refresh_token' => $refreshToken
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        // Buscar usuario
        $auth_user = User::where('email', $request->email)->first();

        if (!$auth_user) {
            return back()->with('error', 'Usuario no encontrado');
        }

        // Verificar estado (si existe la columna)
        if (isset($auth_user->estado) && $auth_user->estado !== 'activo') {
            return back()->with('error', 'Usuario inactivo, contacte al Administrador');
        }

        // Verificar contraseña (SHA512 como mencionaste)
        if ($auth_user->password !== hash('sha512', $request->password)) {
            return back()->with('error', 'Credenciales inválidas');
        }

        // Generar tokens
        $tokens = $this->generateTokens($auth_user->id);

        // Respuesta según sea API o web
        if ($request->expectsJson()) {
            return response()->json([
                'access_token'  => $tokens['token'],
                'refresh_token' => $tokens['refresh_token'],
                'user'          => [
                    'id'     => $auth_user->id,
                    'nombre' => $auth_user->nombre,
                    'email'  => $auth_user->email
                ]
            ]);
        }

        // Guardar en sesión si es acceso desde Blade
        session([
            'jwt_token' => $tokens['token'],
            'user_id'   => $auth_user->id,
            'nombre'    => $auth_user->nombre,
            'color'     => $auth_user->color ?? '#0D6EFD' // Color del usuario o azul por defecto
        ]);

        return redirect()->route('dashboard');
    }

    public function refresh(Request $request)
    {
        $userId = $request->get('auth_user_id');
        $tokens = $this->generateTokens($userId);

        return response()->json([
            'access_token'  => $tokens['token'],
            'refresh_token' => $tokens['refresh_token']
        ]);
    }

    public function me(Request $request)
    {
        $auth_user = User::find($request->get('auth_user_id'));
        return response()->json($auth_user);
    }

    public function logout(Request $request)
    {
        // Limpiar sesión Laravel
        session()->flush();

        // Opcional: invalidar cookies
        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        // Redirigir al login
        return redirect()->route('login');
    }
}
