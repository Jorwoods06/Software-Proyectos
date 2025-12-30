-- ============================================
-- Script SQL para configurar el rol Auditor
-- Rol con permisos estrictamente de solo lectura
-- ============================================

-- 1. Crear el rol Auditor si no existe
INSERT INTO `roles` (`nombre`, `descripcion`, `created_at`, `updated_at`)
SELECT 'Auditor', 'Rol con permisos de solo lectura. Puede ver todos los proyectos, fases, tareas, comentarios y evidencias sin necesidad de ser colaborador.', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM `roles` WHERE `nombre` = 'Auditor'
);

-- 2. Obtener el ID del rol Auditor
SET @rol_auditor_id = (SELECT `id` FROM `roles` WHERE `nombre` = 'Auditor' LIMIT 1);

-- 3. Obtener IDs de los permisos de solo lectura (ver)
SET @permiso_ver_proyecto_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'ver proyecto' LIMIT 1);
SET @permiso_ver_actividades_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'ver actividades' LIMIT 1);
SET @permiso_ver_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'ver tarea' LIMIT 1);

-- 4. Asignar permisos de solo lectura al rol Auditor
-- Ver proyectos
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT @rol_auditor_id, @permiso_ver_proyecto_id
WHERE NOT EXISTS (
    SELECT 1 FROM `rol_permiso` 
    WHERE `rol_id` = @rol_auditor_id 
    AND `permiso_id` = @permiso_ver_proyecto_id
);

-- Ver actividades
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT @rol_auditor_id, @permiso_ver_actividades_id
WHERE NOT EXISTS (
    SELECT 1 FROM `rol_permiso` 
    WHERE `rol_id` = @rol_auditor_id 
    AND `permiso_id` = @permiso_ver_actividades_id
);

-- Ver tareas
INSERT INTO `rol_permiso` (`rol_id`, `permiso_id`)
SELECT @rol_auditor_id, @permiso_ver_tarea_id
WHERE NOT EXISTS (
    SELECT 1 FROM `rol_permiso` 
    WHERE `rol_id` = @rol_auditor_id 
    AND `permiso_id` = @permiso_ver_tarea_id
);

-- 5. Obtener IDs de permisos de escritura que deben ser denegados
SET @permiso_crear_proyecto_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'crear proyecto' LIMIT 1);
SET @permiso_editar_proyecto_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'editar proyecto' LIMIT 1);
SET @permiso_eliminar_proyecto_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'eliminar proyecto' LIMIT 1);
SET @permiso_crear_actividades_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'crear actividades' LIMIT 1);
SET @permiso_editar_actividades_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'editar actividades' LIMIT 1);
SET @permiso_eliminar_actividades_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'eliminar actividades' LIMIT 1);
SET @permiso_crear_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'crear tarea' LIMIT 1);
SET @permiso_editar_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'editar tarea' LIMIT 1);
SET @permiso_eliminar_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'eliminar tarea' LIMIT 1);
SET @permiso_recibir_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'recibir tarea' LIMIT 1);
SET @permiso_comentar_tarea_id = (SELECT `id` FROM `permisos` WHERE `nombre` = 'comentar tarea' LIMIT 1);

-- 6. Eliminar permisos de escritura del rol Auditor (si existen en rol_permiso)
DELETE FROM `rol_permiso` 
WHERE `rol_id` = @rol_auditor_id 
AND `permiso_id` IN (
    @permiso_crear_proyecto_id,
    @permiso_editar_proyecto_id,
    @permiso_eliminar_proyecto_id,
    @permiso_crear_actividades_id,
    @permiso_editar_actividades_id,
    @permiso_eliminar_actividades_id,
    @permiso_crear_tarea_id,
    @permiso_editar_tarea_id,
    @permiso_eliminar_tarea_id,
    @permiso_recibir_tarea_id,
    @permiso_comentar_tarea_id
);

-- 7. Crear permisos directos DENY para todos los usuarios con rol Auditor
-- Esto asegura que aunque tengan otros roles, el permiso Auditor prevalece
-- Primero, obtener todos los usuarios con rol Auditor
INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_crear_proyecto_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_crear_proyecto_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_editar_proyecto_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_editar_proyecto_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_eliminar_proyecto_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_eliminar_proyecto_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_crear_actividades_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_crear_actividades_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_editar_actividades_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_editar_actividades_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_eliminar_actividades_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_eliminar_actividades_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_crear_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_crear_tarea_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_editar_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_editar_tarea_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_eliminar_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_eliminar_tarea_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_recibir_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_recibir_tarea_id
);

INSERT INTO `user_permiso` (`user_id`, `permiso_id`, `tipo`)
SELECT DISTINCT ur.`user_id`, @permiso_comentar_tarea_id, 'deny'
FROM `user_role` ur
WHERE ur.`rol_id` = @rol_auditor_id
AND NOT EXISTS (
    SELECT 1 FROM `user_permiso` up 
    WHERE up.`user_id` = ur.`user_id` 
    AND up.`permiso_id` = @permiso_comentar_tarea_id
);

-- 8. Mostrar resumen de la configuración
SELECT 
    'Configuración del rol Auditor completada' AS mensaje,
    @rol_auditor_id AS rol_auditor_id,
    (SELECT COUNT(*) FROM `user_role` WHERE `rol_id` = @rol_auditor_id) AS usuarios_con_rol_auditor,
    (SELECT COUNT(*) FROM `rol_permiso` WHERE `rol_id` = @rol_auditor_id) AS permisos_allow_asignados,
    (SELECT COUNT(*) FROM `user_permiso` up 
     INNER JOIN `user_role` ur ON up.`user_id` = ur.`user_id` 
     WHERE ur.`rol_id` = @rol_auditor_id AND up.`tipo` = 'deny') AS permisos_deny_asignados;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
-- 
-- NOTAS IMPORTANTES:
-- 1. Este script crea el rol "Auditor" con permisos de solo lectura
-- 2. Asigna permisos ALLOW para: ver proyecto, ver actividades, ver tarea
-- 3. Asigna permisos DENY directamente a todos los usuarios con rol Auditor para:
--    - Crear, editar, eliminar proyectos
--    - Crear, editar, eliminar actividades
--    - Crear, editar, eliminar tareas
--    - Recibir tareas
--    - Comentar en tareas
-- 4. El sistema debe validar estos permisos en los controladores correspondientes
-- 5. Los usuarios con rol Auditor pueden ver TODOS los proyectos sin necesidad de ser colaboradores
-- ============================================

