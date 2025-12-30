<?php

namespace App\Mail;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TareaProximaVencer extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;
    public $usuario;
    public $horasRestantes;

    /**
     * Create a new message instance.
     */
    public function __construct(Tarea $tarea, User $usuario, int $horasRestantes)
    {
        // Cargar relaciones necesarias
        $this->tarea = $tarea->load(['actividad.proyecto']);
        $this->usuario = $usuario;
        $this->horasRestantes = $horasRestantes;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $tareaUrl = $this->tarea->actividad_id 
            ? url("/actividades/{$this->tarea->actividad->proyecto_id}")
            : url("/inicio");

        return $this->subject("Tarea próxima a vencer: {$this->tarea->nombre}")
                    ->view('emails.tarea-proxima-vencer')
                    ->with([
                        'tarea' => $this->tarea,
                        'usuario' => $this->usuario,
                        'horasRestantes' => $this->horasRestantes,
                        'tareaUrl' => $tareaUrl,
                    ]);
    }
}

