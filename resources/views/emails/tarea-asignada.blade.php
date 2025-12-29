<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Tarea Asignada</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #0D6EFD;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .tarea-info {
            background-color: white;
            padding: 15px;
            border-left: 4px solid #0D6EFD;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px 0;
            background-color: #0D6EFD;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-alta { background-color: #F8D7DA; color: #721C24; }
        .badge-media { background-color: #CFE2FF; color: #084298; }
        .badge-baja { background-color: #E2E3E5; color: #41464B; }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Nueva Tarea Asignada</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $usuario->nombre }}</strong>,</p>
        
        <p>Se te ha asignado una nueva tarea:</p>
        
        <div class="tarea-info">
            <h2 style="margin-top: 0;">{{ $tarea->nombre }}</h2>
            @if($tarea->descripcion)
                <p>{{ $tarea->descripcion }}</p>
            @endif
            <p><strong>Prioridad:</strong> 
                <span class="badge badge-{{ $tarea->prioridad }}">
                    {{ ucfirst($tarea->prioridad) }}
                </span>
            </p>
            @if($tarea->fecha_fin)
                <p><strong>Fecha límite:</strong> {{ $tarea->fecha_fin->format('d/m/Y H:i') }}</p>
            @endif
            @if($tarea->actividad)
                <p><strong>Proyecto:</strong> {{ $tarea->actividad->proyecto->nombre ?? 'N/A' }}</p>
                <p><strong>Actividad:</strong> {{ $tarea->actividad->nombre ?? 'N/A' }}</p>
            @endif
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $tareaUrl }}" class="button">Ver Tarea</a>
        </div>
    </div>
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas.</p>
    </div>
</body>
</html>

