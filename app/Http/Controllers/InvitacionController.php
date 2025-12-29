<?php

namespace App\Http\Controllers;

use App\Models\ProyectoUsuario;
use App\Models\Trazabilidad;
use Illuminate\Http\Request;

class InvitacionController extends Controller
{
    /**
     * Aceptar invitación a proyecto
     */
    public function aceptar($token)
    {
        $invitacion = ProyectoUsuario::buscarPorToken($token);
        
        if (!$invitacion) {
            return redirect()->route('proyectos.index')
                ->with('error', 'Invitación no encontrada o inválida.');
        }

        if (!$invitacion->estaPendiente()) {
            $estado = $invitacion->estado_invitacion === 'aceptada' ? 'ya fue aceptada' : 'fue rechazada';
            return redirect()->route('proyectos.index')
                ->with('error', "Esta invitación {$estado}.");
        }

        // Aceptar la invitación
        $invitacion->aceptar();

        // Registrar trazabilidad
        Trazabilidad::create([
            'proyecto_id' => $invitacion->proyecto_id,
            'user_id' => $invitacion->user_id,
            'accion' => "Aceptó la invitación al proyecto: {$invitacion->proyecto->nombre}",
            'fecha' => now()
        ]);

        return redirect()->route('proyectos.index')
            ->with('success', 'Invitación aceptada correctamente. Ya tienes acceso al proyecto.');
    }

    /**
     * Rechazar invitación a proyecto
     */
    public function rechazar($token)
    {
        $invitacion = ProyectoUsuario::buscarPorToken($token);
        
        if (!$invitacion) {
            return redirect()->route('proyectos.index')
                ->with('error', 'Invitación no encontrada o inválida.');
        }

        if (!$invitacion->estaPendiente()) {
            $estado = $invitacion->estado_invitacion === 'aceptada' ? 'ya fue aceptada' : 'fue rechazada';
            return redirect()->route('proyectos.index')
                ->with('error', "Esta invitación {$estado}.");
        }

        // Rechazar la invitación
        $invitacion->rechazar();

        // Registrar trazabilidad
        Trazabilidad::create([
            'proyecto_id' => $invitacion->proyecto_id,
            'user_id' => $invitacion->user_id,
            'accion' => "Rechazó la invitación al proyecto: {$invitacion->proyecto->nombre}",
            'fecha' => now()
        ]);

        return redirect()->route('proyectos.index')
            ->with('info', 'Invitación rechazada.');
    }
}

