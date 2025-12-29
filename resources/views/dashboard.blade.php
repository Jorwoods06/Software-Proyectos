@extends('layouts.app')

@section('title', 'Dashboard Administrador')

@section('content')
<style>
    /* Mobile First - Base Styles */
    .dashboard-container {
        padding: 1rem;
        max-width: 100%;
    }

    .dashboard-header {
        margin-bottom: 1.5rem;
    }

    .dashboard-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
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

    .chart-wrapper {
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 1rem;
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

    .ranking-item {
        display: flex;
        align-items: center;
        padding: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }

    .ranking-item:last-child {
        border-bottom: none;
    }

    .ranking-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #0D6EFD;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .ranking-content {
        flex: 1;
    }

    .ranking-departamento {
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .ranking-valor {
        font-size: 1.25rem;
        font-weight: 700;
        color: #0D6EFD;
    }

    .progress-bar-container {
        width: 100%;
        height: 24px;
        background: #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 0.5rem;
    }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #198754 0%, #20c997 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.75rem;
        font-weight: 600;
        transition: width 0.3s ease;
    }

    /* Tablet - 768px and up */
    @media (min-width: 768px) {
        .dashboard-container {
            padding: 1.5rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
        }

        .section-title {
            font-size: 1.125rem;
        }

        .chart-container {
            height: 350px;
        }
    }

    /* Desktop - 992px and up */
    @media (min-width: 992px) {
        .dashboard-container {
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .chart-container {
            height: 400px;
        }
    }
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard Administrador</h1>
        <p class="dashboard-subtitle">Métricas clave por departamento</p>
    </div>

    {{-- 1. Ranking de Departamentos con Mayor Número de Proyectos --}}
    <div class="chart-wrapper">
        <div class="section-title">Ranking: Departamentos con Mayor Número de Proyectos</div>
        <div class="chart-container">
            <canvas id="chartRankingDepartamentos"></canvas>
        </div>
    </div>

    @if(count($metricas['ranking_departamentos']) > 0)
    <div class="metricas-table">
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">#</th>
                    <th>Departamento</th>
                    <th style="text-align: right;">Total Proyectos</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metricas['ranking_departamentos'] as $index => $dept)
                    <tr>
                        <td>
                            <div class="ranking-number">{{ $index + 1 }}</div>
                        </td>
                        <td><strong>{{ $dept->nombre }}</strong></td>
                        <td style="text-align: right;">
                            <span class="badge-metrica badge-info">{{ $dept->total_proyectos }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- 2. Tareas Creadas por Mes Segmentadas por Departamento --}}
    <div class="chart-wrapper">
        <div class="section-title">Tareas Creadas por Mes (Últimos 12 Meses)</div>
        <div class="chart-container">
            <canvas id="chartTareasPorMes"></canvas>
        </div>
    </div>

    {{-- 3. Productividad por Departamento --}}
    <div class="chart-wrapper">
        <div class="section-title">Productividad por Departamento</div>
        <div class="chart-container">
            <canvas id="chartProductividad"></canvas>
        </div>
    </div>

    @if(count($metricas['productividad']) > 0)
    <div class="metricas-table">
        <table>
            <thead>
                <tr>
                    <th>Departamento</th>
                    <th style="text-align: right;">Total Tareas</th>
                    <th style="text-align: right;">Completadas</th>
                    <th style="text-align: right;">Pendientes</th>
                    <th style="text-align: right;">% Completado</th>
                    <th>Progreso</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metricas['productividad'] as $item)
                    <tr>
                        <td><strong>{{ $item['departamento_nombre'] }}</strong></td>
                        <td style="text-align: right;">{{ $item['total_tareas'] }}</td>
                        <td style="text-align: right;">
                            <span class="badge-metrica badge-success">{{ $item['tareas_completadas'] }}</span>
                        </td>
                        <td style="text-align: right;">
                            <span class="badge-metrica badge-warning">{{ $item['tareas_pendientes'] }}</span>
                        </td>
                        <td style="text-align: right;">
                            <strong>{{ number_format($item['porcentaje_completado'], 1) }}%</strong>
                        </td>
                        <td>
                            <div class="progress-bar-container">
                                <div class="progress-bar-fill" style="width: {{ $item['porcentaje_completado'] }}%;">
                                    @if($item['porcentaje_completado'] >= 15)
                                        {{ number_format($item['porcentaje_completado'], 1) }}%
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- 4. Proyectos Finalizados por Departamento --}}
    <div class="chart-wrapper">
        <div class="section-title">Proyectos Finalizados por Departamento (Total)</div>
        <div class="chart-container">
            <canvas id="chartProyectosFinalizados"></canvas>
        </div>
    </div>

    @if(count($metricas['proyectos_finalizados']['totales']) > 0)
    <div class="metricas-table">
        <table>
            <thead>
                <tr>
                    <th>Departamento</th>
                    <th style="text-align: right;">Total Finalizados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($metricas['proyectos_finalizados']['totales'] as $item)
                    <tr>
                        <td><strong>{{ $item->nombre }}</strong></td>
                        <td style="text-align: right;">
                            <span class="badge-metrica badge-success">{{ $item->total_finalizados }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="chart-wrapper">
        <div class="section-title">Evolución de Proyectos Finalizados (Últimos 12 Meses)</div>
        <div class="chart-container">
            <canvas id="chartEvolucionFinalizados"></canvas>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
window.addEventListener('load', function() {
    if (typeof Chart === 'undefined') {
        console.error('Chart.js no está disponible');
        return;
    }

    // Colores para gráficos
    const colors = [
        '#0D6EFD', '#198754', '#FFC107', '#DC3545', '#6F42C1',
        '#20C997', '#FD7E14', '#E83E8C', '#6C757D', '#0DCAF0'
    ];

    // 1. Gráfico de Ranking de Departamentos
    const ctxRanking = document.getElementById('chartRankingDepartamentos')?.getContext('2d');
    if (ctxRanking) {
        const datos = @json($metricas['ranking_departamentos']);
        new Chart(ctxRanking, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.nombre),
                datasets: [{
                    label: 'Total Proyectos',
                    data: datos.map(d => d.total_proyectos),
                    backgroundColor: colors[0],
                    borderColor: '#0B5ED7',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Tareas por Mes
    const ctxTareasMes = document.getElementById('chartTareasPorMes')?.getContext('2d');
    if (ctxTareasMes) {
        const datos = @json($metricas['tareas_por_mes']);
        const datasets = datos.series.map((serie, index) => ({
            label: serie.nombre,
            data: serie.datos,
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length] + '40',
            tension: 0.4,
            fill: false
        }));

        new Chart(ctxTareasMes, {
            type: 'line',
            data: {
                labels: datos.periodos,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        display: datos.series.length <= 10
                    },
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

    // 3. Gráfico de Productividad
    const ctxProductividad = document.getElementById('chartProductividad')?.getContext('2d');
    if (ctxProductividad) {
        const datos = @json($metricas['productividad']);
        new Chart(ctxProductividad, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.departamento_nombre),
                datasets: [{
                    label: '% Completado',
                    data: datos.map(d => d.porcentaje_completado),
                    backgroundColor: datos.map(d => {
                        if (d.porcentaje_completado >= 80) return '#198754';
                        if (d.porcentaje_completado >= 50) return '#FFC107';
                        return '#DC3545';
                    }),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Productividad: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // 4. Gráfico de Proyectos Finalizados (Total)
    const ctxFinalizados = document.getElementById('chartProyectosFinalizados')?.getContext('2d');
    if (ctxFinalizados) {
        const datos = @json($metricas['proyectos_finalizados']['totales']);
        new Chart(ctxFinalizados, {
            type: 'bar',
            data: {
                labels: datos.map(d => d.nombre),
                datasets: [{
                    label: 'Total Finalizados',
                    data: datos.map(d => d.total_finalizados),
                    backgroundColor: colors[1],
                    borderColor: '#157347',
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

    // 5. Gráfico de Evolución de Proyectos Finalizados
    const ctxEvolucion = document.getElementById('chartEvolucionFinalizados')?.getContext('2d');
    if (ctxEvolucion) {
        const datos = @json($metricas['proyectos_finalizados']['evolucion']);
        const datasets = datos.series.map((serie, index) => ({
            label: serie.nombre,
            data: serie.datos,
            borderColor: colors[index % colors.length],
            backgroundColor: colors[index % colors.length] + '40',
            tension: 0.4,
            fill: false
        }));

        new Chart(ctxEvolucion, {
            type: 'line',
            data: {
                labels: datos.periodos,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        display: datos.series.length <= 10
                    },
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
});
</script>
@endsection
