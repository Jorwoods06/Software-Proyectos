<?php
namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Middleware globales que se ejecutan en todas las peticiones.
     */
    protected $middleware = [
        // Aquí pueden agregar middlewares globales que se reutilizann en todas las rutas
        // Por ahora no incluimos nada porque no trabajamos con sesiones/cookies.
    ];

    /**
     * Grupos de middleware.
     */
    protected $middlewareGroups = [
         'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \App\Http\Middleware\AttachUserFromSession::class,
        // \Illuminate\Session\Middleware\AuthenticateSession::class, // opcional
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
    ],
        'api' => [
            'throttle:api', // límite de peticiones por minuto / aun no lo usamos
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        
    ];

    /**
     * Middlewares individuales que puedes asignar a rutas.
     */
    
    protected $middlewareAliases = [
        'jwt' => \App\Http\Middleware\JwtMiddleware::class,
        'permission' => \App\Http\Middleware\CheckPermission::class,
        'role_ti_or_admin' => \App\Http\Middleware\RoleTiOrAdmin::class,
        'sanitize' => \App\Http\Middleware\SanitizeInput::class,
    ];
}
