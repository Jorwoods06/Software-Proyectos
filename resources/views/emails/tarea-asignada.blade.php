<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Tarea Asignada</title>
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
        .task-card {
            margin: 0 24px 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .task-title {
            font-size: 18px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 4px 0;
            flex: 1;
        }
        .task-id {
            font-size: 11px;
            color: #6c757d;
            margin-top: 2px;
        }
        .priority-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
            margin-left: 12px;
        }
        .priority-badge.alta {
            background-color: #dc3545;
            color: #ffffff;
        }
        .priority-badge.media {
            background-color: #ffc107;
            color: #000000;
        }
        .priority-badge.baja {
            background-color: #6c757d;
            color: #ffffff;
        }
        .task-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 0;
        }
        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .detail-icon {
            width: 24px;
            height: 24px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .detail-icon svg {
            width: 100%;
            height: 100%;
            fill: #26303C;
        }
        .detail-content {
            flex: 1;
        }
        .detail-label {
            font-size: 11px;
            font-weight: 600;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: #212529;
            margin: 0;
        }
        .detail-subvalue {
            font-size: 12px;
            color: #6c757d;
            margin-top: 2px;
        }
        .phase-item {
            grid-column: 1 / -1;
            margin-top: 12px;
            padding-top: 16px;
            border-top: 1px solid #dee2e6;
        }
        .phase-icon svg {
            fill: #198754;
        }
        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 8px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background-color: #198754;
            border-radius: 3px;
            width: 60%;
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
        .cta-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            background-color: #26303C;
            color: #FFFFFF !important;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.2s;
            box-shadow: 0 2px 8px rgba(38, 48, 60, 0.3);
        }
        .cta-button:hover {
            background-color: #1e252e;
            color: #FFFFFF !important;
        }
        .cta-button:visited {
            color: #FFFFFF !important;
        }
        .cta-button:link {
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
            .task-details {
                grid-template-columns: 1fr;
            }
            .task-header {
                flex-direction: column;
            }
            .priority-badge {
                margin-left: 0;
                margin-top: 8px;
            }
            .greeting, .task-card, .cta-section {
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
            <p>Se te ha asignado una nueva tarea que requiere tu atención:</p>
        </div>

        <!-- Task Card -->
        <div class="task-card">
            <div class="task-header">
                <div style="flex: 1;">
                    <h3 class="task-title">{{ $tarea->nombre }}</h3>
                 
                </div>
            </div>

            <div class="task-details">
                @if($tarea->actividad && $tarea->actividad->proyecto)
                <div class="detail-item">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M10 4H4c-1.11 0-2 .89-2 2v12c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2h-8l-2-2z"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <p class="detail-label">Proyecto</p>
                        <p class="detail-value">{{ $tarea->actividad->proyecto->nombre }}</p>
                    </div>
                </div>
                @endif

                <div class="detail-item">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <p class="detail-label">Prioridad</p>
                        <p class="detail-value">
                            <span class="priority-badge {{ $tarea->prioridad }}" style="margin-left: 0;">
                                {{ ucfirst($tarea->prioridad) }}
                            </span>
                        </p>
                    </div>
                </div>

                @if($tarea->actividad && $tarea->actividad->proyecto)
                <div class="detail-item phase-item">
                    <div class="detail-icon phase-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="detail-content" style="flex: 1;">
                        <p class="detail-label">Fase Actual</p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <p class="detail-value" style="margin: 0;">{{ $tarea->actividad->nombre }}</p>
                           
                        </div>
                    </div>
                </div>
                @endif

                @if($tarea->fecha_fin)
                <div class="detail-item">
                    <div class="detail-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z"/>
                        </svg>
                    </div>
                    <div class="detail-content">
                        <p class="detail-label">Fecha Límite</p>
                        <p class="detail-value">{{ $tarea->fecha_fin->format('d/m/Y') }}</p>
                        <p class="detail-subvalue">a las {{ $tarea->fecha_fin->format('H:i') }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Call to Action -->
        <div class="cta-section">
            <p class="cta-text">
                Para revisar todos los detalles, adjuntar archivos o actualizar el estado, accede directamente a la plataforma.
            </p>
            <a href="{{ $tareaUrl }}" class="cta-button">
                Acceder al aplicativo
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
          
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
