@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
    /* ============================================
       DASHBOARD STYLES - Mobile First
       ============================================ */
    
    .dashboard-header {
        margin-bottom: 1.5rem;
    }

    .dashboard-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .dashboard-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        border-left: 4px solid var(--stat-color, #0D6EFD);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #212529;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-label i {
        color: var(--stat-color, #0D6EFD);
    }

    .welcome-card {
        background: linear-gradient(135deg, #0D6EFD 0%, #0B5ED7 100%);
        border-radius: 12px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .welcome-text {
        font-size: 0.9375rem;
        opacity: 0.9;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .action-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        text-decoration: none;
        color: #212529;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        color: #212529;
        text-decoration: none;
    }

    .action-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: var(--action-color, #0D6EFD);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .action-content {
        flex: 1;
    }

    .action-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .action-desc {
        font-size: 0.8125rem;
        color: #6c757d;
    }

    /* ============================================
       TABLET STYLES (768px and up)
       ============================================ */
    @media (min-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .quick-actions {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* ============================================
       DESKTOP STYLES (992px and up)
       ============================================ */
    @media (min-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }

        .quick-actions {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>

<div class="container">
    {{-- Header --}}
    <div class="dashboard-header">
        <h1 class="dashboard-title">Dashboard</h1>
        <p class="dashboard-subtitle">Vista general del sistema</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Welcome Card --}}
    <div class="welcome-card">
        <div class="welcome-title">Bienvenido, {{ ($usuario ?? $auth_user)->nombre ?? 'Usuario' }}</div>
        <div class="welcome-text">Gestiona tus proyectos y tareas de manera eficiente</div>
    </div>

    {{-- Quick Actions --}}
    <div class="quick-actions">
        <a href="{{ route('inicio') }}" class="action-card">
            <div class="action-icon" style="--action-color: #0D6EFD;">
                <i class="bi bi-house-door-fill"></i>
            </div>
            <div class="action-content">
                <div class="action-title">Ir a Inicio</div>
                <div class="action-desc">Ver tus proyectos y tareas</div>
            </div>
        </a>

        @permiso('crear proyecto')
        <a href="{{ route('proyectos.create') }}" class="action-card">
            <div class="action-icon" style="--action-color: #198754;">
                <i class="bi bi-plus-circle"></i>
            </div>
            <div class="action-content">
                <div class="action-title">Crear Proyecto</div>
                <div class="action-desc">Inicia un nuevo proyecto</div>
            </div>
        </a>
        @endpermiso

        <a href="{{ route('proyectos.index') }}" class="action-card">
            <div class="action-icon" style="--action-color: #6F42C1;">
                <i class="bi bi-folder"></i>
            </div>
            <div class="action-content">
                <div class="action-title">Ver Proyectos</div>
                <div class="action-desc">Gestiona todos tus proyectos</div>
            </div>
        </a>
    </div>
</div>
@endsection
