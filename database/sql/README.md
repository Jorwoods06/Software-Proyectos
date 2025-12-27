# Scripts SQL para el Sistema de Gestión

## Script: add_color_to_proyectos.sql

### Descripción
Este script agrega el campo `color` a la tabla `proyectos` y asigna colores automáticamente a los proyectos existentes.

### Cómo ejecutar

#### Opción 1: Desde la línea de comandos MySQL
```bash
mysql -u tu_usuario -p tu_base_de_datos < database/sql/add_color_to_proyectos.sql
```

#### Opción 2: Desde phpMyAdmin
1. Abre phpMyAdmin
2. Selecciona tu base de datos
3. Ve a la pestaña "SQL"
4. Copia y pega el contenido del archivo `add_color_to_proyectos.sql`
5. Haz clic en "Continuar"

#### Opción 3: Desde MySQL Workbench o cliente SQL
1. Abre el archivo `add_color_to_proyectos.sql`
2. Conéctate a tu base de datos
3. Ejecuta el script completo

### Qué hace el script

1. **Agrega la columna `color`**: Crea el campo `color` de tipo VARCHAR(7) en la tabla `proyectos`
2. **Asigna colores automáticamente**: Asigna un color único a cada proyecto existente basándose en su ID

### Verificación

Después de ejecutar el script, puedes verificar que funcionó correctamente con:

```sql
SELECT id, nombre, color FROM proyectos ORDER BY id;
```

Todos los proyectos deberían tener un color asignado.

### Revertir cambios (si es necesario)

Si necesitas eliminar el campo color:

```sql
ALTER TABLE `proyectos` DROP COLUMN `color`;
```

