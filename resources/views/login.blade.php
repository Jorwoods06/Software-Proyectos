<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Google Fonts - Poppins (Professional Font) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* Mobile First - Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f2f3f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        .login-card {
            background-color: #ffffff;
            border-radius: 1rem;
            width: 100%;
            max-width: 100%;
            padding: 2rem 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            font-size: 0.875rem;
            color: #6c757d;
            margin: 0;
        }

        /* Floating Label Container */
        .floating-label-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .floating-label-group:last-of-type {
            margin-bottom: 1rem;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1rem;
            color: #6c757d;
            z-index: 1;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .floating-label-group input {
            width: 100%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 1px solid #dee2e6;
            border-radius: 18px;
            font-size: 1rem;
            background-color: #ffffff;
            transition: all 0.3s ease;
            outline: none;
        }

        .floating-label-group input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
        }

        .floating-label-group input:focus + .input-icon {
            color: #0d6efd;
        }

        .floating-label-group label {
            position: absolute;
            left: 2.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s ease;
            background-color: transparent;
        }

        /* Floating Label Animation */
        .floating-label-group input:focus ~ label,
        .floating-label-group input:not(:placeholder-shown) ~ label {
            top: -0.5rem;
            left: 0.75rem;
            font-size: 0.75rem;
            color: #0d6efd;
            background-color: #ffffff;
            padding: 0 0.25rem;
        }

        .floating-label-group input::placeholder {
            color: transparent;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 1.5rem;
        }

        .forgot-password a {
            color: #0d6efd;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: #0b5ed7;
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            background-color: #0d6efd;
            color: #ffffff;
            border: none;
            border-radius: 18px;
            padding: 0.875rem 1rem;
            font-size: 1rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #0b5ed7;
        }

        .login-button:active {
            background-color: #0a58ca;
        }

        .login-button i {
            font-size: 1.125rem;
        }

        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            font-size: 0.8125rem;
            color: #888888;
        }

        /* Error Messages */
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 0.875rem;
        }

        /* Tablet Styles */
        @media (min-width: 768px) {
            body {
                padding: 2rem;
            }

            .login-card {
                max-width: 28rem;
                padding: 2.5rem 2rem;
            }

            .login-title {
                font-size: 1.75rem;
            }

            .login-subtitle {
                font-size: 0.9375rem;
            }
        }

        /* Desktop Styles */
        @media (min-width: 992px) {
            .login-card {
                max-width: 32rem;
                padding: 3rem 2.5rem;
            }

            .login-title {
                font-size: 2rem;
            }

            .login-subtitle {
                font-size: 1rem;
            }

            .floating-label-group input {
                padding: 1rem 1.25rem 1rem 3rem;
            }

            .input-icon {
                left: 1.25rem;
            }

            .floating-label-group label {
                left: 3rem;
            }

            .floating-label-group input:focus ~ label,
            .floating-label-group input:not(:placeholder-shown) ~ label {
                left: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h1 class="login-title">Sistema de Gestión</h1>
            <p class="login-subtitle">Acceso para usuarios autorizados</p>
        </div>

        {{-- Mensajes de error --}}
        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="floating-label-group">
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" placeholder="Correo electrónico" required>
                    <i class="bi bi-envelope input-icon"></i>
                    <label for="email">Correo electrónico</label>
                </div>
            </div>

            <div class="floating-label-group">
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" placeholder="Contraseña" required minlength="8">
                    <i class="bi bi-lock input-icon"></i>
                    <label for="password">Contraseña</label>
                </div>
            </div>

            <!-- <div class="forgot-password">
                <a href="#">¿Olvidaste tu contraseña?</a>
            </div> -->

            <button type="submit" class="login-button">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Iniciar sesión</span>
            </button>
        </form>

        <div class="login-footer">
            © {{ date('Y') }} Plataforma de Proyectos
        </div>
    </div>
</body>
</html>

