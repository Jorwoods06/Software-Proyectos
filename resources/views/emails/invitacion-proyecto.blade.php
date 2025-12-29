<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitación al Proyecto</title>
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
        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px 5px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .button-accept {
            background-color: #198754;
            color: white;
        }
        .button-reject {
            background-color: #dc3545;
            color: white;
        }
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
        <h1>Invitación al Proyecto</h1>
    </div>
    <div class="content">
        <p>Hola <strong>{{ $usuario->nombre }}</strong>,</p>
        
        <p>Has sido invitado a participar en el proyecto:</p>
        
        <div style="background-color: white; padding: 15px; border-left: 4px solid #0D6EFD; margin: 20px 0;">
            <h2 style="margin-top: 0;">{{ $proyecto->nombre }}</h2>
            @if($proyecto->descripcion)
                <p>{{ $proyecto->descripcion }}</p>
            @endif
            <p><strong>Rol asignado:</strong> {{ ucfirst($rol) }}</p>
        </div>
        
        <p>Para aceptar o rechazar esta invitación, haz clic en uno de los siguientes botones:</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $aceptarUrl }}" class="button button-accept">Aceptar Invitación</a>
            <a href="{{ $rechazarUrl }}" class="button button-reject">Rechazar Invitación</a>
        </div>
        
        <p style="font-size: 12px; color: #666;">
            Si los botones no funcionan, copia y pega estos enlaces en tu navegador:<br>
            Aceptar: {{ $aceptarUrl }}<br>
            Rechazar: {{ $rechazarUrl }}
        </p>
    </div>
    <div class="footer">
        <p>Este es un correo automático, por favor no respondas.</p>
    </div>
</body>
</html>

