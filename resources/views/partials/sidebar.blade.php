<style>
    /* ============================================
       SIDEBAR STYLES - Mobile First
       ============================================ */

    /* Sidebar - Mobile: Hidden by default, drawer style */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 280px;
        height: 100vh;
        background-color: #212B36;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar.open {
        transform: translateX(0);
    }

    /* Reset básico para listas dentro del sidebar */
    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-nav>li {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }

    .sidebar-header {
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logo-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 3px;
        width: 28px;
        height: 28px;
        flex-shrink: 0;
    }

    .logo-square {
        background-color: #0D6EFD;
        border-radius: 4px;
    }

    .sidebar-title {
        font-size: 0.9375rem;
        font-weight: 600;
        color: #ffffff;
        white-space: nowrap;
    }

    .sidebar-nav {
        flex: 1;
        padding: 0.75rem 0;
        overflow-y: auto;
    }

    .nav-item {
        list-style: none;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        color: #ffffff;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.8rem;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        background-color: #0D6EFD;
        color: #ffffff;
    }

    .nav-link i {
        font-size: 0.8rem;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
    }

    .nav-link span {
        white-space: nowrap;
    }

    /* Submenu Styles */
    .nav-item.has-submenu {
        position: relative;
    }

    .nav-link.has-submenu {
        cursor: pointer;
    }

    .nav-link.has-submenu::after {
        content: '\f285';
        font-family: 'bootstrap-icons';
        margin-left: auto;
        transition: transform 0.3s ease;
        font-size: 0.8rem;
    }

    .nav-item.has-submenu.open .nav-link.has-submenu::after {
        transform: rotate(90deg);
    }

    .submenu {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        background-color: rgba(0, 0, 0, 0.2);
        list-style: none;
        margin: 0;
        padding-left: 0 !important;
    }

    .nav-item.has-submenu.open .submenu {
        max-height: 1000px;
    }

    .submenu-item {
        list-style: none;
        margin: 0;
    }

    .submenu-link {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem 1.25rem 0.5rem 2.5rem;
        color: rgba(255, 255, 255, 0.86);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.8rem;
    }

    .submenu-link:hover {
        background-color: rgba(15, 23, 42, 0.85);
        color: #ffffff;
    }

    .submenu-link.active {
        background-color: #0D6EFD;
        color: #ffffff;
    }

    .submenu-create-item {
        margin-top: 0.5rem;
        padding: 0 1rem 0.6rem 1rem;
    }

    .submenu-create-link {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.55rem 0.9rem;
        border-radius: 0.75rem;
        border: 1px dashed rgba(148, 163, 184, 0.85);
        background-color: rgba(15, 23, 42, 0.6);
        color: rgba(226, 232, 240, 0.95);
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 500;
        transition: background-color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, color 0.2s ease;
    }

    .submenu-create-link i {
        font-size: 0.8rem;
    }

    .submenu-create-link:hover {
        background-color: #0D6EFD;
        border-color: #0D6EFD;
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.4);
    }

    .project-color-indicator {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        flex-shrink: 0;
        display: inline-block;
    }

    .sidebar.collapsed .submenu {
        display: none;
    }

    .sidebar.collapsed .nav-link.has-submenu::after {
        display: none;
    }

    .sidebar-toggle {
        display: none;
    }

    .mobile-menu-btn {
        position: fixed;
        top: 1rem;
        left: 1rem;
        width: 40px;
        height: 40px;
        background-color: #0D6EFD;
        border: none;
        border-radius: 0.5rem;
        color: #ffffff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1001;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .mobile-menu-btn:hover {
        background-color: #0b5ed7;
    }

    /* ============================================
       TABLET STYLES (768px and up)
       ============================================ */
    @media (min-width: 768px) {
        .sidebar {
            height: auto;
        }

        .mobile-menu-btn {
            display: none;
        }

        .sidebar {
            position: relative;
            transform: translateX(0);
            width: 280px;
            box-shadow: none;
        }

        .sidebar-overlay {
            display: none;
        }

        .sidebar-toggle {
            display: flex;
            position: absolute;
            top: 1.5rem;
            right: -15px;
            width: 30px;
            height: 30px;
            background-color: #0D6EFD;
            border: none;
            border-radius: 50%;
            color: #ffffff;
            border: 5px solid #212B36;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 10;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }

        .sidebar-toggle:hover {
            background-color: #0b5ed7;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar.collapsed .sidebar-toggle {
            transform: rotate(180deg);
        }

        .sidebar.collapsed .sidebar-header {
            padding: 1.5rem 0.5rem;
            justify-content: center;
        }

        .sidebar.collapsed .logo-grid {
            width: 24px;
            height: 24px;
        }

        .sidebar.collapsed .sidebar-title {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 0.875rem 0.5rem;
        }

        .sidebar.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar.collapsed .submenu {
            display: none !important;
        }

        .sidebar.collapsed .nav-link.has-submenu::after {
            display: none;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
        }
    }

    /* ============================================
       MEDIA QUERY PARA PORTÁTIL 1422x650
       ============================================ */
    @media (min-width: 1400px) and (max-width: 1444px) and (min-height: 600px) and (max-height: 700px) {
        .sidebar {
            width: 270px;
        }

        .sidebar.collapsed {
            width: 78px;
        }

        .sidebar-header {
            padding: 1.3rem 1.15rem;
        }

        .logo-grid {
            width: 26px;
            height: 26px;
            gap: 2.5px;
        }

        .sidebar.collapsed .logo-grid {
            width: 22px;
            height: 22px;
        }

        .sidebar-title {
            font-size: 0.9rem;
        }

        .sidebar.collapsed .sidebar-title {
            opacity: 0;
            width: 0;
            overflow: hidden;
        }

        .sidebar-nav {
            padding: 0.7rem 0;
        }

        .nav-link {
            padding: 0.8rem 0.95rem;
            font-size: 0.8rem;
            gap: 0.7rem;
        }

        .nav-link i {
            font-size: 1.05rem;
            width: 19px;
        }

        .sidebar.collapsed .nav-link {
            padding: 0.8rem 0.45rem;
        }

        .nav-link.has-submenu::after {
            font-size: 0.8rem;
        }

        .submenu-link {
            padding: 0.7rem 0.95rem 0.7rem 2.4rem;
            font-size: 0.8rem;
            gap: 0.7rem;
        }

        .project-color-indicator {
            width: 11px;
            height: 11px;
        }

        .sidebar-toggle {
            width: 29px;
            height: 29px;
            top: 1.4rem;
            right: -14px;
        }

        .sidebar.collapsed .sidebar-header {
            padding: 1.4rem 0.45rem;
        }
    }

    /* ============================================
       HIGH RESOLUTION DISPLAYS (1440px and up)
       ============================================ */
    @media (min-width: 1440px) {
        .sidebar {
            width: 260px;
        }

        .sidebar.collapsed {
            width: 75px;
        }

        .sidebar-header {
            padding: 1.25rem 1rem;
        }

        .sidebar-title {
            font-size: 0.9375rem;
        }

        .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
        }

        .nav-link i {
            font-size: 1rem;
        }
    }

    /* ============================================
       ULTRA HIGH RESOLUTION DISPLAYS (1920px and up)
       ============================================ */
    @media (min-width: 1920px) {
        .sidebar {
            width: 240px;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 1rem 0.875rem;
        }

        .sidebar-title {
            font-size: 0.875rem;
        }

        .logo-grid {
            width: 26px;
            height: 26px;
        }

        .sidebar.collapsed .logo-grid {
            width: 22px;
            height: 22px;
        }

        .nav-link {
            padding: 0.6875rem 0.875rem;
            font-size: 0.8rem;
        }

        .nav-link i {
            font-size: 0.9375rem;
        }

        .sidebar-toggle {
            width: 28px;
            height: 28px;
            top: 1.25rem;
        }
    }
</style>

@php
$auth_user = \App\Models\User::with('roles')
->find(session('user_id'));

// Obtener proyectos del usuario autenticado
$proyectos = collect([]);
if ($auth_user) {
$proyectos = \App\Models\Proyecto::where('estado', '!=', 'cancelado')
->where(function ($query) use ($auth_user) {
$query
->where('departamento_id', $auth_user->departamento)
->orWhereHas('usuarios', function ($q) use ($auth_user) {
$q->where('users.id', $auth_user->id);
});
})
->orderBy('nombre')
->get();
}

// Determinar si el submenú de proyectos debe estar abierto
$proyectosMenuOpen = request()->routeIs('actividades.*') || request()->routeIs('tareas.*');

// Obtener el ID del proyecto actual si estamos en actividades o tareas
$proyectoActualId = null;
if (request()->route('proyecto')) {
$proyectoActualId = (int) request()->route('proyecto');
} elseif (request()->route('actividad')) {
$actividad = \App\Models\Actividad::find(request()->route('actividad'));
$proyectoActualId = $actividad ? (int) $actividad->proyecto_id : null;
}
@endphp

{{-- Mobile Menu Button --}}
<button class="mobile-menu-btn" aria-label="Toggle menu">
    <i class="bi bi-list"></i>
</button>

{{-- Sidebar Overlay --}}
<div class="sidebar-overlay"></div>

<nav class="sidebar">
    <button class="sidebar-toggle" aria-label="Toggle sidebar">
        <i class="bi bi-chevron-left"></i>
    </button>

    <div class="sidebar-header">
        <div class="logo-grid">
            <div class="logo-square"></div>
            <div class="logo-square"></div>
            <div class="logo-square"></div>
            <div class="logo-square"></div>
        </div>
        <div class="sidebar-title">Sistema de Gestión</div>
    </div>

    <ul class="sidebar-nav">
        @if(isset($auth_user) && $auth_user && ($auth_user->hasRole('TI') || $auth_user->hasRole('Administrador')))
        <li class="nav-item">
            <a href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('inicio') }}"
                class="nav-link {{ request()->routeIs('inicio') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i>
                <span>Inicio</span>
            </a>
        </li>

        <li class="nav-item has-submenu {{ $proyectosMenuOpen ? 'open' : '' }}">
            <a href="#"
                class="nav-link has-submenu {{ request()->routeIs('proyectos.*') || request()->routeIs('actividades.*') || request()->routeIs('tareas.*') ? 'active' : '' }}"
                onclick="event.preventDefault(); this.closest('.nav-item').classList.toggle('open');">
                <i class="bi bi-folder"></i>
                <span>Proyectos</span>
            </a>
            <ul class="submenu">
                @forelse($proyectos as $proyecto)
                <li class="submenu-item">
                    <a href="{{ route('actividades.index', $proyecto->id) }}"
                        class="submenu-link {{ $proyectoActualId == $proyecto->id ? 'active' : '' }}">
                        <span class="project-color-indicator" style="background-color: {{ $proyecto->color }};"></span>
                        <span>{{ $proyecto->nombre }}</span>
                    </a>
                </li>
                @empty
                <li class="submenu-item">
                    <span class="submenu-link" style="color: rgba(255, 255, 255, 0.5); font-size: 0.75rem; cursor: default;">
                        No hay proyectos
                    </span>
                </li>
                @endforelse

                <li class="submenu-item submenu-create-item">
                    <a href="{{ route('proyectos.create') }}" class="submenu-create-link">
                        <i class="bi bi-plus-circle"></i>
                        <span>Crear Nuevo Proyecto</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->routeIs('equipos') ? 'active' : '' }}">
                <i class="bi bi-people"></i>
                <span>Equipos</span>
            </a>
        </li>

        @if(isset($auth_user) && $auth_user && ($auth_user->hasRole('TI') || $auth_user->hasRole('Administrador')))
        <li class="nav-item">
            <a href="{{ route('users.index') }}"
                class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i>
                <span>Gestión de Usuarios</span>
            </a>
        </li>
        @endif

        <li class="nav-item">
            <a href="{{ route('calendario.calendario') }}"
                class="nav-link {{ request()->routeIs('calendario.*') ? 'active' : '' }}">
                <i class="bi bi-calendar"></i>
                <span>Calendario</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->routeIs('reportes') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i>
                <span>Reportes</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="#"
                class="nav-link {{ request()->routeIs('configuracion') ? 'active' : '' }}">
                <i class="bi bi-gear"></i>
                <span>Configuración</span>
            </a>
        </li>
    </ul>
</nav>