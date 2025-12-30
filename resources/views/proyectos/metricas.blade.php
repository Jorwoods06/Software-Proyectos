@extends('layouts.app')

@section('title', 'Métricas - ' . $infoBasica->nombre)

@section('content')
<style>
    /* Mobile First - Base Styles */
    .metricas-container {
        padding: 1rem;
        max-width: 100%;
    }

    .metricas-header {
        margin-bottom: 1.5rem;
    }

    .metricas-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .metricas-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .metricas-table {
        width: 100%;
        margin-bottom: 1.5rem;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .metricas-table table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .metricas-table thead {
        background: #f8f9fa;
    }

    .metricas-table th {
        padding: 0.75rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    .metricas-table td {
        padding: 0.75rem;
        font-size: 0.875rem;
        color: #212529;
        border-bottom: 1px solid #e9ecef;
    }

    .metricas-table tbody tr:hover {
        background: #f8f9fa;
    }

    .metricas-table tbody tr:last-child td {
        border-bottom: none;
    }

    .badge-metrica {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-success { background: #d1e7dd; color: #0f5132; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger { background: #f8d7da; color: #842029; }
    .badge-info { background: #cff4fc; color: #055160; }
    .badge-secondary { background: #e2e3e5; color: #41464b; }

    .chart-wrapper {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .chart-container {
        position: relative;
        height: 250px;
        margin-top: 1rem;
    }

    .kpi-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .kpi-item {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        text-align: center;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .kpi-label {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        background: white;
        border-radius: 8px;
        padding: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6c757d;
        margin-top: 0.25rem;
    }

    .btn-volver {
        margin-top: 1.5rem;
    }

    .fechas-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .fecha-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 6px;
        border-left: 3px solid #0D6EFD;
    }

    .fecha-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
    }

    .fecha-value {
        font-size: 0.875rem;
        color: #212529;
        font-weight: 600;
    }

    /* Tablet - 768px and up */
    @media (min-width: 768px) {
        .metricas-container {
            padding: 1.5rem;
        }

        .metricas-title {
            font-size: 1.5rem;
        }

        .section-title {
            font-size: 1.125rem;
        }

        .chart-container {
            height: 300px;
        }

        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .stats-row {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    /* Desktop - 992px and up */
    @media (min-width: 992px) {
        .metricas-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .kpi-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .chart-container {
            height: 350px;
        }
    }
</style>

<div class="metricas-container">
    <div class="metricas-header">
        <h1 class="metricas-title">{{ $infoBasica->nombre }}</h1>
        <p class="metricas-subtitle">Métricas y estadísticas del proyecto</p>

        <div class="d-flex justify-content-end">
            <a href="{{ route('proyectos.index') }}" class="btn btn-secondary btn-volver">
                <i class="bi bi-arrow-left"></i> Volver a Proyectos
            </a>
        </div>
    </div>

    {{-- Tareas por Colaborador --}}
    @if(count($metricas['tareas']['por_usuario']) > 0)
    <div class="metricas-table">
        <div class="section-title">Tareas por Colaborador</div>
        <table>
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Total</th>
                    <th>Completadas</th>
                    <th>Pendientes</th>
                    <th>En Progreso</th>
                    <th>% Completado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metricas['tareas']['por_usuario'] as $usuario)
                    @php
                        $porcentaje = $usuario->total_tareas > 0 
                            ? round(($usuario->tareas_completadas / $usuario->total_tareas) * 100, 1) 
                            : 0;
                    @endphp
                    <tr>
                        <td><strong>{{ $usuario->usuario_nombre }}</strong></td>
                        <td>{{ $usuario->total_tareas }}</td>
                        <td><span class="badge-metrica badge-success">{{ $usuario->tareas_completadas }}</span></td>
                        <td><span class="badge-metrica badge-info">{{ $usuario->tareas_pendientes }}</span></td>
                        <td><span class="badge-metrica badge-warning">{{ $usuario->tareas_en_progreso }}</span></td>
                        <td>{{ $porcentaje }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Fechas y Plazos --}}
    <div class="chart-wrapper">
        <div class="section-title">Fechas y Plazos</div>
        <div class="fechas-list">
            <div class="fecha-item">
                <span class="fecha-label">Fecha Inicio Planificada:</span>
                <span class="fecha-value">{{ $metricas['generales']['fechas_y_plazos']['fecha_inicio_planificada'] ?? 'N/A' }}</span>
            </div>
            <div class="fecha-item">
                <span class="fecha-label">Fecha Fin Planificada:</span>
                <span class="fecha-value">{{ $metricas['generales']['fechas_y_plazos']['fecha_fin_planificada'] ?? 'N/A' }}</span>
            </div>
            @if($metricas['generales']['fechas_y_plazos']['fecha_inicio_real'])
            <div class="fecha-item">
                <span class="fecha-label">Fecha Inicio Real:</span>
                <span class="fecha-value">{{ $metricas['generales']['fechas_y_plazos']['fecha_inicio_real'] }}</span>
            </div>
            @endif
            @if($metricas['generales']['fechas_y_plazos']['fecha_fin_real'])
            <div class="fecha-item">
                <span class="fecha-label">Fecha Fin Real:</span>
                <span class="fecha-value">{{ $metricas['generales']['fechas_y_plazos']['fecha_fin_real'] }}</span>
            </div>
            @endif
            <div class="fecha-item">
                <span class="fecha-label">Duración Planificada:</span>
                <span class="fecha-value">{{ $metricas['generales']['fechas_y_plazos']['duracion_planificada_dias'] ?? 'N/A' }} días</span>
            </div>
            @if($metricas['generales']['fechas_y_plazos']['duracion_real_dias'])
            <div class="fecha-item">
                <span class="fecha-label">Duración Real:</span>
                <span class="fecha-value">{{ $metricas['generales']['fechas_y_plazos']['duracion_real_dias'] }} días</span>
            </div>
            @endif
            <div class="fecha-item">
                <span class="fecha-label">Días Restantes:</span>
                <span class="fecha-value">
                    @if($metricas['generales']['fechas_y_plazos']['dias_restantes'] !== null)
                        @php
                            $diasRestantes = round($metricas['generales']['fechas_y_plazos']['dias_restantes']);
                        @endphp
                        @if($diasRestantes < 0)
                            <span class="badge-metrica badge-danger">{{ abs($diasRestantes) }} días de retraso</span>
                        @else
                            <span class="badge-metrica badge-success">{{ $diasRestantes }} días</span>
                        @endif
                    @else
                        N/A
                    @endif
                </span>
            </div>
        </div>
    </div>

    {{-- Trazabilidad --}}
    @if(count($metricas['trazabilidad']) > 0)
    <div class="metricas-table">
        <div class="section-title">Trazabilidad Reciente</div>
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metricas['trazabilidad'] as $traza)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($traza->fecha)->format('d/m/Y H:i') }}</td>
                        <td>{{ $traza->usuario_nombre }}</td>
                        <td>{{ $traza->accion }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- KPIs Principales --}}
    <div class="section-title">Indicadores Clave (KPIs)</div>
    <div class="kpi-grid">
        <div class="kpi-item">
            <div class="kpi-value">{{ number_format($metricas['kpis']['completitud_tareas'], 1) }}%</div>
            <div class="kpi-label">Completitud de Tareas</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-value">{{ number_format($metricas['kpis']['completitud_actividades'], 1) }}%</div>
            <div class="kpi-label">Completitud de Fases</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-value">{{ number_format($metricas['kpis']['avance_proyecto'], 1) }}%</div>
            <div class="kpi-label">Avance del Proyecto</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-value">{{ number_format($metricas['kpis']['calidad_evidencias'], 1) }}%</div>
            <div class="kpi-label">Calidad (Evidencias)</div>
        </div>
    </div>

    {{-- Gráfico de Fases por Estado --}}
    <div class="chart-wrapper">
        <div class="section-title">Fases por Estado</div>
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['actividades']['total'] }}</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['actividades']['pendientes'] }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['actividades']['en_progreso'] }}</div>
                <div class="stat-label">En Progreso</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['actividades']['finalizadas'] }}</div>
                <div class="stat-label">Finalizadas</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="chartActividades"></canvas>
        </div>
    </div>

    {{-- Gráfico de Tareas por Estado --}}
    <div class="chart-wrapper">
        <div class="section-title">Tareas por Estado</div>
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['tareas']['total'] }}</div>
                <div class="stat-label">Total</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['tareas']['pendientes'] }}</div>
                <div class="stat-label">Pendientes</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['tareas']['en_progreso'] }}</div>
                <div class="stat-label">En Progreso</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">{{ $metricas['tareas']['completadas'] }}</div>
                <div class="stat-label">Completadas</div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="chartTareas"></canvas>
        </div>
    </div>

    {{-- Tareas por Prioridad --}}
    <div class="chart-wrapper">
        <div class="section-title">Tareas por Prioridad</div>
        <div class="chart-container">
            <canvas id="chartPrioridad"></canvas>
        </div>
    </div>

   

    <a href="{{ route('proyectos.index') }}" class="btn btn-secondary btn-volver">
        <i class="bi bi-arrow-left"></i> Volver a Proyectos
    </a>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
window.addEventListener('load', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está disponible');
        return;
    }

    // Gráfico de Fases por Estado
    const ctxActividades = document.getElementById('chartActividades')?.getContext('2d');
    if (ctxActividades) {
        new Chart(ctxActividades, {
            type: 'bar',
            data: {
                labels: ['Pendientes', 'En Progreso', 'Finalizadas', 'Eliminadas'],
                datasets: [{
                    label: 'Cantidad',
                    data: [
                        {{ $metricas['actividades']['pendientes'] }},
                        {{ $metricas['actividades']['en_progreso'] }},
                        {{ $metricas['actividades']['finalizadas'] }},
                        {{ $metricas['actividades']['eliminadas'] }}
                    ],
                    backgroundColor: ['#6C757D', '#FFC107', '#198754', '#DC3545'],
                    borderColor: ['#5A6268', '#E0A800', '#157347', '#BB2D3B'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Gráfico de Tareas por Estado
    const ctxTareas = document.getElementById('chartTareas')?.getContext('2d');
    if (ctxTareas) {
        new Chart(ctxTareas, {
            type: 'bar',
            data: {
                labels: ['Pendientes', 'En Progreso', 'Completadas', 'Eliminadas'],
                datasets: [{
                    label: 'Cantidad',
                    data: [
                        {{ $metricas['tareas']['pendientes'] }},
                        {{ $metricas['tareas']['en_progreso'] }},
                        {{ $metricas['tareas']['completadas'] }},
                        {{ $metricas['tareas']['eliminadas'] }}
                    ],
                    backgroundColor: ['#6C757D', '#FFC107', '#198754', '#DC3545'],
                    borderColor: ['#5A6268', '#E0A800', '#157347', '#BB2D3B'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // Gráfico de Tareas por Prioridad
    const ctxPrioridad = document.getElementById('chartPrioridad')?.getContext('2d');
    if (ctxPrioridad) {
        new Chart(ctxPrioridad, {
            type: 'pie',
            data: {
                labels: ['Alta', 'Media', 'Baja'],
                datasets: [{
                    data: [
                        {{ $metricas['tareas']['alta'] }},
                        {{ $metricas['tareas']['media'] }},
                        {{ $metricas['tareas']['baja'] }}
                    ],
                    backgroundColor: ['#DC3545', '#FFC107', '#198754'],
                    borderColor: ['#BB2D3B', '#E0A800', '#157347'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: { enabled: true }
                }
            }
        });
    }
});
</script>
@endsection

