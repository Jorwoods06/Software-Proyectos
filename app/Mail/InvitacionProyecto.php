<?php

namespace App\Mail;

use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitacionProyecto extends Mailable
{
    use Queueable, SerializesModels;

    public $proyecto;
    public $usuario;
    public $token;
    public $rol;

    /**
     * Create a new message instance.
     */
    public function __construct(Proyecto $proyecto, User $usuario, string $token, string $rol)
    {
        $this->proyecto = $proyecto;
        $this->usuario = $usuario;
        $this->token = $token;
        $this->rol = $rol;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $aceptarUrl = url("/proyectos/invitaciones/aceptar/{$this->token}");
        $rechazarUrl = url("/proyectos/invitaciones/rechazar/{$this->token}");

        return $this->subject("Invitación al proyecto: {$this->proyecto->nombre}")
                    ->view('emails.invitacion-proyecto')
                    ->with([
                        'proyecto' => $this->proyecto,
                        'usuario' => $this->usuario,
                        'rol' => $this->rol,
                        'aceptarUrl' => $aceptarUrl,
                        'rechazarUrl' => $rechazarUrl,
                    ]);
    }
}

