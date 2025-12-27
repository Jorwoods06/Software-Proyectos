<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * Nombres de las cookies que no se deben cifrar.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Si necesitas excluir alguna cookie del cifrado, agrégala aquí.
        // Por ejemplo: 'cookie_no_cifrada'
    ];
}
