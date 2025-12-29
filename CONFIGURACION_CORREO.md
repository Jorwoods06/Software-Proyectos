# Configuración de Correo Electrónico

## Variables de entorno para el archivo `.env`

Agrega o actualiza las siguientes variables en tu archivo `.env` ubicado en la raíz del proyecto:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=ssl
MAIL_USERNAME=no-reply@protejer.com
MAIL_PASSWORD=qjianlbvhrmoawdh
MAIL_FROM_ADDRESS=no-reply@protejer.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Nota importante sobre Gmail

⚠️ **IMPORTANTE**: Gmail requiere usar `tls` en lugar de `ssl` para el puerto 587. Si tienes problemas, cambia:

```env
MAIL_ENCRYPTION=tls
```

## Verificación

Después de agregar estas variables, ejecuta:

```bash
php artisan config:clear
php artisan config:cache
```

Esto asegurará que Laravel cargue la nueva configuración.

