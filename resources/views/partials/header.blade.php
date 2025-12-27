<style>
    /* ============================================
       HEADER STYLES - Mobile First
       ============================================ */

    .content-header {
        background-color: #ffffff;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        border-bottom: 1px solid #e0e0e0;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        order: 1;
        flex: 1;
        min-width: 0;
    }

    .user-greeting {
        color: #212B36;
        font-size: 0.875rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #0D6EFD;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-weight: 600;
        font-size: 0.875rem;
        flex-shrink: 0;
    }

    .logout-btn {
        background-color: #212B36;
        color: #ffffff;
        border: none;
        padding: 0.625rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.8125rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s ease;
        order: 2;
        width: 100%;
    }

    .logout-btn:hover {
        background-color: #1a2329;
        color: #ffffff;
    }

    .user-div {
        display: flex;
        flex-direction: column;

        align-items: self-end;
        padding: 0;
        margin: 0;
    }

    .user-div p {
        font-size: 0.75rem;
        color: #6c757d;
        margin: 0;
        padding: 0;
    }

    .breadcrumb-container {
        order: 0;
        width: 100%;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 0.75rem;
    }

    .breadcrumb {
        margin: 0;
        padding: 0;
        background: transparent;
        font-size: 0.8125rem;
    }

    .breadcrumb-item {
        display: inline-flex;
        align-items: center;
    }

    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: #0D6EFD;
    }

    .breadcrumb-item.active {
        color: #212529;
        font-weight: 500;
    }

    .breadcrumb-item+.breadcrumb-item::before {
        content: ">";
        padding: 0 0.5rem;
        color: #6c757d;
        font-weight: 300;
    }

    /* ============================================
       TABLET STYLES (768px and up)
       ============================================ */
    @media (min-width: 768px) {
        .content-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-start;
            padding: 1.25rem 1.5rem;
            gap: 1rem;
        }

        .breadcrumb-container {
            order: 0;
            width: auto;
            flex: 1;
            padding-bottom: 0;
            border-bottom: none;
            margin-bottom: 0;
            margin-right: 1rem;
        }

        .user-info {
            flex: 0 1 auto;
            min-width: 0;
            order: 1;
        }

        .logout-btn {
            flex-shrink: 0;
            width: auto;
            font-size: 0.875rem;
            order: 2;
        }

        .user-greeting {
            font-size: 0.9375rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .breadcrumb {
            font-size: 0.875rem;
        }
    }

    /* ============================================
       DESKTOP STYLES (992px and up)
       ============================================ */
    @media (min-width: 992px) {
        .content-header {
            padding: 0.5rem 2rem;
            max-width: 100%;
            align-items: center;
        }
    }

    /* ============================================
       HIGH RESOLUTION DISPLAYS (1440px and up)
       ============================================ */
    @media (min-width: 1440px) {
        .content-header {
            padding: 1rem 1.75rem;
        }

        .user-greeting {
            font-size: 0.875rem;
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            font-size: 0.9375rem;
        }

        .logout-btn {
            padding: 0.5625rem 0.875rem;
            font-size: 0.8125rem;
        }
    }

    /* ============================================
       ULTRA HIGH RESOLUTION DISPLAYS (1920px and up)
       ============================================ */
    @media (min-width: 1920px) {
        .content-header {
            padding: 0.875rem 1.5rem;
        }

        .user-greeting {
            font-size: 0.8125rem;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            font-size: 0.875rem;
        }

        .logout-btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.75rem;
        }
    }
</style>

<header class="content-header">
    @php
    $breadcrumbTrail = \App\Helpers\BreadcrumbHelper::generate();
    @endphp

    @if(count($breadcrumbTrail) > 1)
    <div class="breadcrumb-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                @foreach($breadcrumbTrail as $index => $item)
                <li class="breadcrumb-item{{ $index === count($breadcrumbTrail) - 1 ? ' active' : '' }}">
                    @if($index < count($breadcrumbTrail) - 1)
                        @if(Route::has($item['route']))
                        <a href="{{ route($item['route']) }}" class="text-decoration-none">
                        @if($item['icon'])
                        <i class="bi {{ $item['icon'] }} me-1"></i>
                        @endif
                        {{ $item['name'] }}
                        </a>
                        @else
                        <span>
                            @if($item['icon'])
                            <i class="bi {{ $item['icon'] }} me-1"></i>
                            @endif
                            {{ $item['name'] }}
                        </span>
                        @endif
                        @else
                        @if($item['icon'])
                        <i class="bi {{ $item['icon'] }} me-1"></i>
                        @endif
                        {{ $item['name'] }}
                        @endif
                </li>
                @endforeach
            </ol>
        </nav>
    </div>
    @endif

    <div class="user-info">
        <div class="user-div">
            <p>Bienvenido</p>
            <span class="user-greeting"> {{ session('nombre') ?? 'Usuario' }}</span>
        </div>
        <div class="user-avatar">
            {{ strtoupper(substr(session('nombre') ?? 'U', 0, 1)) }}
        </div>
    </div>
    <a href="{{ route('logout') }}" class="logout-btn">
        <span>Cerrar sesión</span>
        <i class="bi bi-arrow-right"></i>
    </a>
</header>