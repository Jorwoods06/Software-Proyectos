# Cambios Implementados - Permisos y Gestión de Fechas/Horarios

## Fecha: 15 de Diciembre 2025

## Resumen de Cambios Implementados

### 1. Permisos por Rol

#### Permisos Implementados:
- **Administrador y TI**: Tienen permisos completos para editar y eliminar actividades y tareas de cualquier proyecto
- **Creador del Proyecto**: Tiene permisos completos para editar y eliminar actividades y tareas de sus proyectos creados

#### Implementación:
- Se agregó campo `created_by` a la tabla `proyectos` para identificar al creador
- Se creó método `puedeGestionarActividadesYTareas()` en el modelo `Proyecto` que verifica:
  - Si el usuario tiene rol Administrador o TI
  - Si el usuario es el creador del proyecto
- Se implementaron validaciones en los controladores `ActividadController` y `TareaController` para:
  - `update()`: Validar permisos antes de editar
  - `destroy()`: Validar permisos antes de eliminar

### 2. Soft Delete (Eliminación Lógica)

#### Cambios Realizados:
- **Actividades**: Al eliminar, cambia el estado a 'eliminado' en lugar de eliminar físicamente de la BD
- **Tareas**: Al eliminar, cambia el estado a 'eliminado' en lugar de eliminar físicamente de la BD
- Se agregó estado 'eliminado' a los ENUMs de `actividades.estado` y `tareas.estado`
- Se creó scope `sinEliminar()` en ambos modelos para filtrar automáticamente elementos eliminados
- Se crearon métodos:
  - `eliminar()`: Cambia estado a 'eliminado'
  - `estaEliminada()`: Verifica si está eliminada
  - `restaurar($nuevoEstado)`: Restaura una actividad/tarea eliminada

#### Auditoría:
- Las actividades y tareas eliminadas permanecen en la base de datos para auditoría
- Solo se ocultan en las consultas normales usando el scope `sinEliminar()`

### 3. Gestión de Fechas y Horarios

#### Cambios en Base de Datos:
- **Actividades**: 
  - `fecha_inicio` y `fecha_fin` cambiaron de `DATE` a `DATETIME`
- **Tareas**:
  - `fecha_inicio` y `fecha_fin` cambiaron de `DATE` a `DATETIME`

#### Migraciones Creadas:
1. `2025_12_15_000001_update_actividades_add_datetime_and_softdelete.php`
   - Actualiza ENUM de estado para incluir 'eliminado'
   - Convierte fechas DATE a DATETIME
   - Migra datos existentes (añade hora 00:00:00 para inicio y 23:59:59 para fin)

2. `2025_12_15_000002_update_tareas_add_datetime_and_softdelete.php`
   - Actualiza ENUM de estado para incluir 'eliminado'
   - Convierte fechas DATE a DATETIME
   - Migra datos existentes

3. `2025_12_15_000003_add_created_by_to_proyectos.php`
   - Agrega campo `created_by` a la tabla `proyectos`
   - Migra datos existentes desde `proyecto_usuario` donde `rol_proyecto = 'lider'`

#### Lógica de Validación de Estados "Tarde":
- **Antes**: Las tareas se marcaban como "tarde" si `fecha_fin < hoy` (comparación de fecha solamente)
- **Ahora**: Las tareas solo se marcan como "tarde" si `fecha_fin < now()` (comparación completa de fecha y hora)
- Se actualizó método `obtenerVencidas()` en el modelo `Tarea` para usar comparación datetime completa
- Se agregó método `estaVencida()` que verifica correctamente si una tarea está vencida considerando fecha y hora

#### Modelos Actualizados:
- **Proyecto**: 
  - Agregado campo `created_by` en fillable
  - Agregada relación `creador()`
  - Agregado método `esCreadorPor($user)`
  - Agregado método `puedeGestionarActividadesYTareas($user)`
  - Actualizado `userHasPermission()` para considerar Administrador, TI y creador

- **Actividad**:
  - Actualizado cast de `fecha_inicio` y `fecha_fin` a `datetime`
  - Agregado scope `sinEliminar()`
  - Agregados métodos: `eliminar()`, `estaEliminada()`, `restaurar()`
  - Actualizados métodos estáticos para excluir eliminadas

- **Tarea**:
  - Actualizado cast de `fecha_inicio` y `fecha_fin` a `datetime`
  - Agregado scope `sinEliminar()`
  - Agregados métodos: `eliminar()`, `estaEliminada()`, `restaurar()`, `estaVencida()`
  - Actualizados métodos estáticos para excluir eliminadas y usar comparación datetime

#### Controladores Actualizados:
- **ActividadController**:
  - `store()`: Maneja fecha/hora separadas y las combina en datetime
  - `update()`: Valida permisos y maneja fecha/hora separadas
  - `destroy()`: Usa soft delete (cambia estado) en lugar de eliminar físicamente

- **TareaController**:
  - `store()`: Valida permisos si pertenece a actividad, maneja fecha/hora separadas
  - `update()`: Valida permisos y maneja fecha/hora separadas
  - `destroy()`: Usa soft delete (cambia estado) en lugar de eliminar físicamente

#### Vistas Actualizadas:
- **actividades/index.blade.php**:
  - Formulario de crear actividad: Agregados campos de fecha/hora de inicio y fin
  - Formulario de crear tarea: Agregados campos de fecha/hora de inicio y fin

- **inicio.blade.php**:
  - Formulario de crear tarea independiente: Agregados campos de fecha/hora de inicio y fin

### 4. Validaciones y Consistencia

#### Validaciones Implementadas:
- Los controladores validan permisos antes de editar/eliminar
- Se respeta la separación entre fecha y hora en los formularios
- La combinación de fecha/hora se hace en el backend antes de guardar
- Si solo se proporciona fecha sin hora, se usa hora por defecto (00:00:00 para inicio, 23:59:59 para fin)

#### Consultas Actualizadas:
- Todas las consultas que listan actividades/tareas ahora excluyen las eliminadas usando el scope `sinEliminar()`
- Las comparaciones de fechas para determinar tareas vencidas ahora usan datetime completo

### 5. Instrucciones para Ejecutar

#### Paso 1: Ejecutar Migraciones
```bash
php artisan migrate
```

Si hay problemas con las migraciones, ejecuta el siguiente SQL manualmente:
```sql
-- Actualizar actividades
ALTER TABLE `actividades` MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','finalizado','eliminado') DEFAULT 'pendiente';
ALTER TABLE `actividades` CHANGE COLUMN `fecha_inicio` `fecha_inicio` DATETIME NULL;
ALTER TABLE `actividades` CHANGE COLUMN `fecha_fin` `fecha_fin` DATETIME NULL;

-- Actualizar tareas
ALTER TABLE `tareas` MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','completado','eliminado') DEFAULT 'pendiente';
ALTER TABLE `tareas` CHANGE COLUMN `fecha_inicio` `fecha_inicio` DATETIME NULL;
ALTER TABLE `tareas` CHANGE COLUMN `fecha_fin` `fecha_fin` DATETIME NULL;

-- Agregar created_by a proyectos
ALTER TABLE `proyectos` ADD COLUMN `created_by` INT(11) NULL AFTER `departamento_id`;
ALTER TABLE `proyectos` ADD FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;
UPDATE proyectos p 
INNER JOIN proyecto_usuario pu ON p.id = pu.proyecto_id 
SET p.created_by = pu.user_id 
WHERE pu.rol_proyecto = 'lider';
```

#### Paso 2: Limpiar Caché
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

### 6. Pruebas Recomendadas

1. **Permisos**:
   - Crear un proyecto como usuario normal
   - Intentar editar/eliminar actividad/tarea como otro usuario (debe fallar)
   - Intentar editar/eliminar como Administrador (debe funcionar)
   - Intentar editar/eliminar como creador del proyecto (debe funcionar)

2. **Soft Delete**:
   - Eliminar una actividad y verificar que sigue en BD con estado 'eliminado'
   - Verificar que no aparece en las listas normales
   - Crear actividad con mismo nombre (debe permitirse)

3. **Fechas/Horarios**:
   - Crear tarea con fecha/hora de fin hoy a las 23:59:59 (no debe estar vencida)
   - Crear tarea con fecha/hora de fin ayer (debe estar vencida)
   - Verificar que las comparaciones consideran hora completa

### 7. Notas Importantes

- Los datos existentes en BD se migran automáticamente (fechas sin hora reciben hora por defecto)
- Las actividades/tareas eliminadas permanecen en BD para auditoría
- Para restaurar una actividad/tarea eliminada, usar el método `restaurar()` directamente en el modelo
- El campo `created_by` se asigna automáticamente al crear proyectos nuevos

