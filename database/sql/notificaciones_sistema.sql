-- ============================================
-- SCRIPT SQL PARA SISTEMA DE NOTIFICACIONES
-- ============================================
-- Este script agrega las tablas y campos necesarios
-- para el sistema de notificaciones por correo electrónico
-- ============================================

-- 1. Agregar campos de invitación a la tabla proyecto_usuario
-- ============================================
ALTER TABLE `proyecto_usuario` 
ADD COLUMN `estado_invitacion` ENUM('pendiente', 'aceptada', 'rechazada') NOT NULL DEFAULT 'aceptada' AFTER `rol_proyecto`,
ADD COLUMN `token_invitacion` VARCHAR(64) NULL UNIQUE AFTER `estado_invitacion`,
ADD COLUMN `fecha_invitacion` TIMESTAMP NULL AFTER `token_invitacion`;

-- Actualizar registros existentes para que tengan estado 'aceptada' por defecto
UPDATE `proyecto_usuario` 
SET `estado_invitacion` = 'aceptada' 
WHERE `estado_invitacion` IS NULL OR `estado_invitacion` = '';

-- 2. Crear tabla de configuración de notificaciones
-- ============================================
CREATE TABLE IF NOT EXISTS `notificacion_config` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `clave` VARCHAR(100) NOT NULL UNIQUE,
  `valor` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clave_unique` (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Insertar valores por defecto en notificacion_config
-- ============================================
INSERT INTO `notificacion_config` (`clave`, `valor`, `descripcion`, `created_at`, `updated_at`) 
VALUES 
(
  'horas_antes_vencimiento',
  '24',
  'Horas antes de la fecha de vencimiento para enviar notificación de tarea próxima a vencer',
  NOW(),
  NOW()
),
(
  'notificar_admin_tareas_vencidas',
  '1',
  'Notificar a administradores cuando hay tareas vencidas (1 = sí, 0 = no)',
  NOW(),
  NOW()
)
ON DUPLICATE KEY UPDATE 
  `valor` = VALUES(`valor`),
  `descripcion` = VALUES(`descripcion`),
  `updated_at` = NOW();

-- ============================================
-- VERIFICACIÓN DE CAMBIOS
-- ============================================
-- Ejecutar estas consultas para verificar que los cambios se aplicaron correctamente:

-- Verificar estructura de proyecto_usuario
-- DESCRIBE `proyecto_usuario`;

-- Verificar que la tabla notificacion_config existe
-- SELECT * FROM `notificacion_config`;

-- Verificar registros en proyecto_usuario con nuevos campos
-- SELECT `id`, `proyecto_id`, `user_id`, `rol_proyecto`, `estado_invitacion`, `token_invitacion`, `fecha_invitacion` 
-- FROM `proyecto_usuario` 
-- LIMIT 5;

-- ============================================
-- NOTAS IMPORTANTES
-- ============================================
-- 1. Los registros existentes en proyecto_usuario se marcarán como 'aceptada' automáticamente
-- 2. Los nuevos registros creados mediante invitación tendrán estado 'pendiente'
-- 3. El token_invitacion se genera automáticamente cuando se crea una invitación pendiente
-- 4. La configuración de notificaciones se puede modificar desde la aplicación o directamente en la BD

