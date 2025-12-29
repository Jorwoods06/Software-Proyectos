<?php

namespace App\Services;

use App\Mail\InvitacionProyecto;
use App\Mail\TareaAsignada;
use App\Mail\TareaProximaVencer;
use App\Mail\TareaVencida;
use App\Models\NotificacionConfig;
use App\Models\Proyecto;
use App\Models\Tarea;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Enviar invitación a proyecto
     */
    public function enviarInvitacionProyecto(Proyecto $proyecto, User $usuario, string $token, string $rol): void
    {
        try {
            Mail::to($usuario->email)->send(new InvitacionProyecto($proyecto, $usuario, $token, $rol));
            Log::info("Invitación enviada a {$usuario->email} para el proyecto {$proyecto->nombre}");
        } catch (\Exception $e) {
            Log::error("Error al enviar invitación: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Enviar notificación de tarea asignada
     */
    public function enviarTareaAsignada(Tarea $tarea, User $usuario): void
    {
        try {
            Mail::to($usuario->email)->send(new TareaAsignada($tarea, $usuario));
            Log::info("Notificación de tarea asignada enviada a {$usuario->email} para la tarea {$tarea->nombre}");
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de tarea asignada: " . $e->getMessage());
            // No lanzar excepción para no interrumpir el flujo principal
        }
    }

    /**
     * Enviar notificación de tarea próxima a vencer
     */
    public function enviarTareaProximaVencer(Tarea $tarea, User $usuario, int $horasRestantes): void
    {
        try {
            Mail::to($usuario->email)->send(new TareaProximaVencer($tarea, $usuario, $horasRestantes));
            Log::info("Notificación de tarea próxima a vencer enviada a {$usuario->email} para la tarea {$tarea->nombre}");
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de tarea próxima a vencer: " . $e->getMessage());
        }
    }

    /**
     * Enviar notificación de tarea vencida
     */
    public function enviarTareaVencida(Tarea $tarea, User $usuario, bool $esAdmin = false): void
    {
        try {
            Mail::to($usuario->email)->send(new TareaVencida($tarea, $usuario, $esAdmin));
            Log::info("Notificación de tarea vencida enviada a {$usuario->email} para la tarea {$tarea->nombre}");
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación de tarea vencida: " . $e->getMessage());
        }
    }

    /**
     * Notificar a todos los usuarios asignados a una tarea
     */
    public function notificarUsuariosAsignados(Tarea $tarea, string $tipoNotificacion): void
    {
        $usuarios = $tarea->usuariosAsignados;

        // Si es tarea independiente, notificar al usuario asignado directamente
        if ($tarea->user_id) {
            $usuario = User::find($tarea->user_id);
            if ($usuario && !$usuarios->contains('id', $usuario->id)) {
                $usuarios->push($usuario);
            }
        }

        foreach ($usuarios as $usuario) {
            switch ($tipoNotificacion) {
                case 'asignada':
                    $this->enviarTareaAsignada($tarea, $usuario);
                    break;
                case 'proxima_vencer':
                    $horasRestantes = $this->calcularHorasRestantes($tarea);
                    if ($horasRestantes !== null) {
                        $this->enviarTareaProximaVencer($tarea, $usuario, $horasRestantes);
                    }
                    break;
                case 'vencida':
                    $this->enviarTareaVencida($tarea, $usuario);
                    break;
            }
        }
    }

    /**
     * Calcular horas restantes hasta la fecha de vencimiento
     */
    private function calcularHorasRestantes(Tarea $tarea): ?int
    {
        if (!$tarea->fecha_fin) {
            return null;
        }

        $ahora = now();
        $vencimiento = $tarea->fecha_fin;
        
        if ($vencimiento <= $ahora) {
            return null; // Ya venció
        }

        return (int) $ahora->diffInHours($vencimiento);
    }

    /**
     * Notificar a administradores sobre tareas vencidas
     */
    public function notificarAdminsTareasVencidas(Tarea $tarea): void
    {
        if (!NotificacionConfig::notificarAdminTareasVencidas()) {
            return;
        }

        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('nombre', ['Administrador', 'admin', 'TI']);
        })->get();

        foreach ($admins as $admin) {
            $this->enviarTareaVencida($tarea, $admin, true);
        }
    }
}

