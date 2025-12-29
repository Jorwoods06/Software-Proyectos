<?php

namespace App\Console\Commands;

use App\Models\Tarea;
use App\Models\NotificacionConfig;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class VerificarTareasVencimiento extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tareas:verificar-vencimiento';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica tareas próximas a vencer y vencidas, enviando notificaciones correspondientes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando tareas próximas a vencer y vencidas...');
        
        $notificationService = new NotificationService();
        $horasAntesVencimiento = NotificacionConfig::obtenerHorasAntesVencimiento();
        
        // Obtener tareas próximas a vencer
        $fechaLimite = now()->addHours($horasAntesVencimiento);
        
        $tareasProximas = Tarea::where('estado', '!=', 'completado')
            ->where('estado', '!=', 'eliminado')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '>', now())
            ->where('fecha_fin', '<=', $fechaLimite)
            ->with(['usuariosAsignados', 'actividad'])
            ->get();
        
        $this->info("Encontradas {$tareasProximas->count()} tareas próximas a vencer");
        
        foreach ($tareasProximas as $tarea) {
            $horasRestantes = (int) now()->diffInHours($tarea->fecha_fin);
            $notificationService->notificarUsuariosAsignados($tarea, 'proxima_vencer');
        }
        
        // Obtener tareas vencidas
        $tareasVencidas = Tarea::where('estado', '!=', 'completado')
            ->where('estado', '!=', 'eliminado')
            ->whereNotNull('fecha_fin')
            ->where('fecha_fin', '<', now())
            ->with(['usuariosAsignados', 'actividad'])
            ->get();
        
        $this->info("Encontradas {$tareasVencidas->count()} tareas vencidas");
        
        foreach ($tareasVencidas as $tarea) {
            $notificationService->notificarUsuariosAsignados($tarea, 'vencida');
            
            // Notificar a administradores si está configurado
            if (NotificacionConfig::notificarAdminTareasVencidas()) {
                $notificationService->notificarAdminsTareasVencidas($tarea);
            }
        }
        
        $this->info('Verificación completada.');
        
        return Command::SUCCESS;
    }
}

