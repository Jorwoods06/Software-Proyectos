-- ============================================
-- Script SQL para configurar permisos del rol Lector
-- Permite participar en proyectos pero NO recibir tareas ni comentar
-- ============================================

-- 1. Crear nuevos permisos si no existen
-- Permiso para recibir/asignar tareas
INSERT INTO `permisos` (`nombre`, `descripcion`, `created_at`, `updated_at`)
SELECT 'recibir tarea', 'Puede recibir y ser asignado a tareas', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `nombre` = 'recibir tarea'
);

-- Permiso para comentar en tareas
INSERT INTO `permisos` (`nombre`, `descripcion`, `created_at`, `updated_at`)
SELECT 'comentar tarea', 'Puede agregar comentarios en tareas', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `permisos` WHERE `nombre` = 'comentar tarea'
);

-- 2. Obtener IDs de los permisos creados (o existentes)
SET @permiso_recibir_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'recibir tarea' LIMIT 1);
SET @permiso_comentar_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'comentar tarea' LIMIT 1);
SET @rol_lector_id = (SELECT `id` FROM `roles` WHERE `nombre` = 'Lector' LIMIT 1);

-- 3. Asegurar que el rol Lector NO tenga estos permisos en rol_permiso
-- (Eliminar si existen)
DELETE FROM `rol_permiso` 
WHERE `rol_id` = @rol_lector_id 
AND (`permiso_id` = @permiso_recibir_tarea_id OR `permiso_id` = @permiso_comentar_tarea_id);

-- 4. Crear permisos directos DENY para todos los usuarios con rol Lector
-- Esto asegura que aunque tengan otros roles, el permiso Lector prevalece
-- Primero, obtener todos los usuarios con rol Lector
INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_recibir_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_lector_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_recibir_tarea_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_comentar_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_lector_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_comentar_tarea_id
);

-- 5. Verificar que el rol Lector tenga permisos de visualización
-- (Asegurar que tiene 'ver proyecto', 'ver actividades', 'ver tarea')
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT @rol_lector_id, p.`id`
FROM `permisos` p
WHERE p.`nombre` IN ('ver proyecto', 'ver actividades', 'ver tarea')
AND NOT EXISTS (
    SELECT 1 FROM `rol_permiso` rp 
    WHERE rp.`rol_id` = @rol_lector_id 
    AND rp.`permiso_id` = p.`id`
);

-- 6. Mostrar resumen de la configuración
SELECT 
    'Configuración completada' AS mensaje,
    @rol_lector_id AS rol_lector_id,
    @permiso_recibir_tarea_id AS permiso_recibir_tarea_id,
    @permiso_comentar_tarea_id AS permiso_comentar_tarea_id,
    (SELECT COUNT(*) FROM `user_role` WHERE `rol_id` = @rol_lector_id) AS usuarios_con_rol_lector,
    (SELECT COUNT(*) FROM `user_permiso` WHERE `permiso_id` = @permiso_recibir_tarea_id AND `tipo` = 'deny') AS denegaciones_recibir_tarea,
    (SELECT COUNT(*) FROM `user_permiso` WHERE `permiso_id` = @permiso_comentar_tarea_id AND `tipo` = 'deny') AS denegaciones_comentar_tarea;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
-- 
-- NOTAS IMPORTANTES:
-- 1. Este script crea dos nuevos permisos: "recibir tarea" y "comentar tarea"
-- 2. Asigna permisos DENY directamente a todos los usuarios con rol Lector
-- 3. El sistema debe validar estos permisos en:
--    - Asignación de tareas (TareaController, método store/update)
--    - Creación de comentarios (TareaController, método agregarComentario)
-- 4. Los usuarios con rol Lector pueden participar en proyectos (ver proyecto, actividades, tareas)
--    pero NO pueden recibir tareas ni comentar
-- ============================================

