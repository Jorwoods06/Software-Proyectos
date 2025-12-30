<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación al Proyecto</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #212529;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .email-container {
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #26303C 0%, #1e252e 100%);
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,.05) 10px, rgba(255,255,255,.05) 20px);
            padding: 30px 20px;
            text-align: center;
            position: relative;
        }
        .header-icon {
            width: 56px;
            height: 56px;
            background-color: #ffffff;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .header-icon svg {
            width: 32px;
            height: 32px;
            fill: #26303C;
        }
        .header h1 {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }
        .header .subtitle {
            color: #ffffff;
            font-size: 13px;
            font-weight: 400;
            opacity: 0.9;
        }
        .greeting {
            padding: 24px 24px 16px;
            background-color: #ffffff;
        }
        .greeting h2 {
            font-size: 16px;
            font-weight: 600;
            color: #212529;
            margin-bottom: 6px;
        }
        .greeting p {
            font-size: 13px;
            color: #6c757d;
            margin: 0;
        }
        .project-card {
            margin: 0 24px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .project-title {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 10px 0;
        }
        .project-description {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        .role-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            background-color: #26303C;
            color: #ffffff;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .cta-section {
            margin: 0 24px 24px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .cta-text {
            font-size: 13px;
            color: #212529;
            margin-bottom: 16px;
            line-height: 1.5;
        }
        .button-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .cta-button.accept {
            background-color: #198754;
            color: #FFFFFF !important;
        }
        .cta-button.accept:hover {
            background-color: #157347;
            color: #FFFFFF !important;
        }
        .cta-button.accept:visited {
            color: #FFFFFF !important;
        }
        .cta-button.accept:link {
            color: #FFFFFF !important;
        }
        .cta-button.reject {
            background-color: #dc3545;
            color: #FFFFFF !important;
        }
        .cta-button.reject:hover {
            background-color: #bb2d3b;
            color: #FFFFFF !important;
        }
        .cta-button.reject:visited {
            color: #FFFFFF !important;
        }
        .cta-button.reject:link {
            color: #FFFFFF !important;
        }
        .cta-link {
            display: block;
            margin-top: 16px;
            font-size: 12px;
            color: #6c757d;
            word-break: break-all;
        }
        .footer {
            padding: 24px;
            background-color: #ffffff;
            background-image: 
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(0,0,0,.02) 10px, rgba(0,0,0,.02) 20px);
            text-align: center;
        }
        .footer p {
            font-size: 11px;
            color: #adb5bd;
            margin: 4px 0;
            line-height: 1.6;
        }
        .footer-links {
            margin-top: 12px;
            font-size: 11px;
            color: #adb5bd;
        }
        .footer-links a {
            color: #adb5bd;
            text-decoration: none;
        }
        @media only screen and (max-width: 600px) {
            .button-group {
                flex-direction: column;
            }
            .cta-button {
                width: 100%;
                justify-content: center;
            }
            .greeting, .project-card, .cta-section {
                margin-left: 16px;
                margin-right: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
       

        <!-- Greeting -->
        <div class="greeting">
            <h2>Hola {{ $usuario->nombre }},</h2>
            <p>Has sido invitado a participar en un proyecto:</p>
        </div>

        <!-- Project Card -->
        <div class="project-card">
            <h3 class="project-title">{{ $proyecto->nombre }}</h3>
            @if($proyecto->descripcion)
                <p class="project-description">{{ $proyecto->descripcion }}</p>
            @endif
            <span class="role-badge">Rol: {{ ucfirst($rol) }}</span>
        </div>

        <!-- Call to Action -->
        <div class="cta-section">
            <p class="cta-text">
                Para aceptar o rechazar esta invitación, haz clic en uno de los siguientes botones:
            </p>
            <div class="button-group">
                <a href="{{ $aceptarUrl }}" class="cta-button accept">
                    Aceptar Invitación
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </a>
                <a href="{{ $rechazarUrl }}" class="cta-button reject">
                    Rechazar Invitación
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12"/>
                    </svg>
                </a>
            </div>
            <p class="cta-link">
                Si los botones no funcionan, copia y pega estos enlaces en tu navegador:<br>
                Aceptar: {{ $aceptarUrl }}<br>
                Rechazar: {{ $rechazarUrl }}
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Este es un mensaje automático, por favor no respondas a este correo.</p>
            <p>© {{ date('Y') }} Centro De Formacion Integral Providencia. Todos los derechos reservados.</p>
            <div class="footer-links">
                <a href="#">Configuración de notificaciones</a> • <a href="#">Soporte</a>
            </div>
        </div>
    </div>
</body>
</html>
