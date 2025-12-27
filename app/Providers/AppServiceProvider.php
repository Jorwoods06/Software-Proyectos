<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registrar el singleton para el usuario autenticado en sesión para acceso global
        $this->app->singleton('session_user', function ($app) {
            return \App\Models\User::with('roles')->find(session('user_id'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // registro de directiva Blade personalizada para permisos
         
        Blade::if('permiso', function (string $permiso) {
            return usuarioTienePermiso($permiso);
        });

        View::composer('*', function ($view) {
            $view->with('auth_user', app('session_user'));   // ahora $user estará en TODAS las vistas
        });
        
    }
}
