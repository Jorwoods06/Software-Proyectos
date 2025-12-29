<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificacionConfig extends Model
{
    protected $table = 'notificacion_config';
    public $timestamps = true;

    protected $fillable = [
        'clave',
        'valor',
        'descripcion'
    ];

    /**
     * Obtener valor de configuración por clave
     */
    public static function obtenerValor(string $clave, $default = null)
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    /**
     * Establecer valor de configuración
     */
    public static function establecerValor(string $clave, string $valor, ?string $descripcion = null): void
    {
        static::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'descripcion' => $descripcion ?? static::where('clave', $clave)->value('descripcion')
            ]
        );
    }

    /**
     * Obtener horas antes de vencimiento configuradas
     */
    public static function obtenerHorasAntesVencimiento(): int
    {
        return (int) static::obtenerValor('horas_antes_vencimiento', 24);
    }

    /**
     * Verificar si se debe notificar a administradores sobre tareas vencidas
     */
    public static function notificarAdminTareasVencidas(): bool
    {
        return static::obtenerValor('notificar_admin_tareas_vencidas', '1') === '1';
    }
}

