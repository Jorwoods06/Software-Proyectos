<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BreadcrumbHelper
{
    public static function generate()
    {
        $currentRouteName = Route::currentRouteName();
        if (!$currentRouteName) {
            return [];
        }

        $routeParts = explode('.', $currentRouteName);
        $breadcrumbTrail = [];

        // Ruta base (Inicio)
        $breadcrumbTrail[] = [
            'name' => 'Inicio',
            'route' => 'dashboard',
            'icon' => 'bi-house-door-fill',
        ];

        // Construye el breadcrumb basado en los segmentos de la ruta
        $currentPath = '';
        foreach ($routeParts as $index => $part) {
            $currentPath .= ($index === 0) ? $part : '.' . $part;

            // Convierte el segmento en un nombre legible
            $name = self::convertToReadableName($part);

            $breadcrumbTrail[] = [
                'name' => $name,
                'route' => $currentPath,
                'icon' => null,
            ];
        }

        return $breadcrumbTrail;
    }

    private static function convertToReadableName($segment)
    {
        // Convierte segmentos como "editRolesAndPermissions" a "Editar Roles Y Permisos"
        return ucwords(Str::replace(['-', '_'], ' ', Str::snake($segment)));
    }
}
