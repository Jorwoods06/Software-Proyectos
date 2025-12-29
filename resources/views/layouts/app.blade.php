<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Gestión')</title>

    <!-- Google Fonts - Poppins (Professional Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap css y Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        /* ============================================
           BASE STYLES - Mobile First
           ============================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #F2F3F5;
            overflow-x: hidden;
        }

        /* Main Content Area - Mobile */
        .main-wrapper {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            max-width: 100%;
            overflow-x: hidden;
        }

        .content {
            flex: 1;
            padding: 1rem;
            background-color: #F2F3F5;
            overflow-y: auto;
        }

        /* Breadcrumb - Mobile */
        .breadcrumb {
            margin-bottom: 1rem;
            font-size: 0.8125rem;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        .breadcrumb-item {
            font-size: 0.8125rem;
            display: inline-block;
        }

        .breadcrumb-item a {
            text-decoration: none;
        }

        /* ============================================
           TABLET STYLES (768px and up)
           ============================================ */
        @media (min-width: 768px) {
            .content {
                padding: 1.5rem;
            }

            .breadcrumb {
                margin-bottom: 1.5rem;
                font-size: 0.875rem;
            }

            .breadcrumb-item {
                font-size: 0.875rem;
            }
        }

        /* ============================================
           DESKTOP STYLES (992px and up)
           ============================================ */
        @media (min-width: 992px) {
          

            .content {
                padding: 2rem;
                max-width: 100%;
            }
        }

        /* ============================================
           HIGH RESOLUTION DISPLAYS (1440px and up)
           ============================================ */
        @media (min-width: 1440px) {
            .content {
                padding: 1.75rem;
            }

            .breadcrumb {
                margin-bottom: 1.25rem;
                font-size: 0.8125rem;
            }

            .breadcrumb-item {
                font-size: 0.8125rem;
            }
        }

        /* ============================================
           ULTRA HIGH RESOLUTION DISPLAYS (1920px and up)
           ============================================ */
        @media (min-width: 1920px) {
            .content {
                padding: 1.5rem;
            }

            .breadcrumb {
                margin-bottom: 1rem;
                font-size: 0.75rem;
            }

            .breadcrumb-item {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content Wrapper --}}
        <div class="main-wrapper">
            {{-- Header --}}
            @include('partials.header')

        {{-- Contenido dinámico --}}
            <main class="content">
    @yield('content')
</main>

            {{-- Footer --}}
            @include('partials.footer')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @stack('scripts')
    
    <script>
        // Sidebar Toggle Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.querySelector('.sidebar');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            const overlay = document.querySelector('.sidebar-overlay');
            const isMobile = window.innerWidth < 768;
            
            // Desktop: Check localStorage for saved state
            if (!isMobile) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('collapsed');
                }
            }
            
            // Desktop toggle button
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('collapsed');
                    // Save state to localStorage
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                });
            }
            
            // Mobile menu button
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('open');
                    overlay.classList.toggle('active');
                });
            }
            
            // Close sidebar when clicking overlay
            if (overlay) {
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                });
            }
            
            // Handle window resize
            window.addEventListener('resize', function() {
                const isMobileNow = window.innerWidth < 768;
                if (isMobileNow) {
                    sidebar.classList.remove('collapsed', 'open');
                    overlay.classList.remove('active');
                } else {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                    if (isCollapsed) {
                        sidebar.classList.add('collapsed');
                    }
                }
            });
        });
    </script>

</body>
</html>
