<?php

namespace App\Mail;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TareaAsignada extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;
    public $usuario;

    /**
     * Create a new message instance.
     */
    public function __construct(Tarea $tarea, User $usuario)
    {
        $this->tarea = $tarea;
        $this->usuario = $usuario;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $tareaUrl = $this->tarea->actividad_id 
            ? url("/actividades/{$this->tarea->actividad->proyecto_id}")
            : url("/inicio");

        return $this->subject("Nueva tarea asignada: {$this->tarea->nombre}")
                    ->view('emails.tarea-asignada')
                    ->with([
                        'tarea' => $this->tarea,
                        'usuario' => $this->usuario,
                        'tareaUrl' => $tareaUrl,
                    ]);
    }
}

