<?php

namespace App\Mail;

use App\Models\Tarea;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TareaVencida extends Mailable
{
    use Queueable, SerializesModels;

    public $tarea;
    public $usuario;
    public $esAdmin;

    /**
     * Create a new message instance.
     */
    public function __construct(Tarea $tarea, User $usuario, bool $esAdmin = false)
    {
        // Cargar relaciones necesarias
        $this->tarea = $tarea->load(['actividad.proyecto']);
        $this->usuario = $usuario;
        $this->esAdmin = $esAdmin;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $tareaUrl = $this->tarea->actividad_id 
            ? url("/actividades/{$this->tarea->actividad->proyecto_id}")
            : url("/inicio");

        $subject = $this->esAdmin 
            ? "Tarea vencida - Requiere atención: {$this->tarea->nombre}"
            : "Tarea vencida: {$this->tarea->nombre}";

        return $this->subject($subject)
                    ->view('emails.tarea-vencida')
                    ->with([
                        'tarea' => $this->tarea,
                        'usuario' => $this->usuario,
                        'esAdmin' => $this->esAdmin,
                        'tareaUrl' => $tareaUrl,
                    ]);
    }
}

