<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tarea;
use App\Models\Proyecto;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $usuario = User::find($userId);

        if (!$usuario) {
            return redirect()->route('login');
        }

        // Dashboard simple con métricas básicas
        return view('dashboard', compact('usuario'));
    }

    public function inicio(Request $request)
    {
        $userId = session('user_id');
        $usuario = User::find($userId);

        if (!$usuario) {
            return redirect()->route('login');
        }

        $proyectos = Proyecto::obtenerPorUsuario($userId);
        $tareasIndependientes = Tarea::obtenerIndependientesPorUsuario($userId);
        
        // Obtener totales para los badges
        $totalUltimas = Tarea::obtenerTodasPorUsuario($userId)->count();
        $totalProximas = Tarea::obtenerProximasAVencer($userId, 7)->count();
        $totalVencidas = Tarea::obtenerVencidas($userId)->count();
        
        // Determinar qué tab está activo
        $tabActivo = $request->get('tab', 'ultimas');
        $perPage = 5;
        
        // Paginar según el tab activo y preservar parámetros de query
        switch ($tabActivo) {
            case 'proximas':
                $tareasProximas = Tarea::obtenerProximasAVencerPaginadas($userId, 7, $perPage)
                    ->appends($request->query());
                $ultimasTareas = null;
                $tareasVencidas = null;
                break;
            case 'vencidas':
                $tareasVencidas = Tarea::obtenerVencidasPaginadas($userId, $perPage)
                    ->appends($request->query());
                $ultimasTareas = null;
                $tareasProximas = null;
                break;
            default: // 'ultimas'
                $ultimasTareas = Tarea::obtenerUltimasPorUsuarioPaginadas($userId, $perPage)
                    ->appends($request->query());
                $tareasProximas = null;
                $tareasVencidas = null;
                break;
        }

        return view('inicio', compact(
            'usuario',
            'proyectos',
            'tareasIndependientes',
            'ultimasTareas',
            'tareasProximas',
            'tareasVencidas',
            'tabActivo',
            'totalUltimas',
            'totalProximas',
            'totalVencidas'
        ));
    }
}
