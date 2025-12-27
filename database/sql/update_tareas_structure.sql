-- ============================================
-- Script SQL para actualizar estructura de tareas
-- Fecha: 2025
-- ============================================

-- 1. Agregar campo 'prioridad' a la tabla tareas
ALTER TABLE `tareas` 
ADD COLUMN `prioridad` ENUM('baja', 'media', 'alta') DEFAULT 'media' AFTER `estado`;

-- 2. Actualizar enum de estado para incluir 'completado' en lugar de 'finalizado'
-- Primero, actualizar los registros existentes
UPDATE `tareas` SET `estado` = 'completado' WHERE `estado` = 'finalizado';

-- Modificar la columna estado
ALTER TABLE `tareas` 
MODIFY COLUMN `estado` ENUM('pendiente', 'en_progreso', 'completado') DEFAULT 'pendiente';

-- 3. Corregir inconsistencias: el modelo usa 'titulo', 'detalle', 'fecha_entrega'
-- pero la BD tiene 'nombre', 'descripcion', 'fecha_fin'
-- Vamos a mantener la estructura de BD y actualizar el modelo
-- (No se requieren cambios en BD para esto, solo en el modelo)

-- 4. Modificar tabla tareas para permitir tareas independientes (actividad_id nullable)
ALTER TABLE `tareas` 
MODIFY COLUMN `actividad_id` int(11) NULL;

-- 5. Agregar campo user_id para tareas independientes (si no existe)
-- Verificar si la columna existe antes de agregarla
SET @dbname = DATABASE();
SET @tablename = 'tareas';
SET @columnname = 'user_id';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT 1', -- Columna existe, no hacer nada
  CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` int(11) NULL AFTER `responsable_id`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar índice y constraint si no existen
ALTER TABLE `tareas` 
ADD KEY IF NOT EXISTS `user_id` (`user_id`);

-- Eliminar constraint si existe antes de agregarlo
SET @constraint_name = (SELECT CONSTRAINT_NAME 
  FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
  WHERE TABLE_SCHEMA = @dbname 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = @columnname 
    AND CONSTRAINT_NAME LIKE 'tareas_ibfk_user' 
  LIMIT 1);

SET @preparedStatement = IF(@constraint_name IS NULL,
  CONCAT('ALTER TABLE `', @tablename, '` ADD CONSTRAINT `tareas_ibfk_user` FOREIGN KEY (`', @columnname, '`) REFERENCES `users` (`id`) ON DELETE CASCADE'),
  'SELECT 1'
);
PREPARE alterIfNotExists2 FROM @preparedStatement;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;

-- 6. Crear tabla pivot para asignación múltiple de usuarios a tareas
CREATE TABLE IF NOT EXISTS `tarea_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tarea_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tarea_usuario_unique` (`tarea_id`, `user_id`),
  KEY `tarea_id` (`tarea_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tarea_usuario_ibfk_1` FOREIGN KEY (`tarea_id`) REFERENCES `tareas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tarea_usuario_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Migrar datos existentes de responsable_id a tarea_usuario (si existen)
-- Solo si hay tareas con responsable_id asignado
INSERT INTO `tarea_usuario` (`tarea_id`, `user_id`, `created_at`, `updated_at`)
SELECT `id`, `responsable_id`, `created_at`, `updated_at`
FROM `tareas`
WHERE `responsable_id` IS NOT NULL
AND NOT EXISTS (
    SELECT 1 FROM `tarea_usuario` tu 
    WHERE tu.tarea_id = tareas.id AND tu.user_id = tareas.responsable_id
);

-- 8. Agregar índice para mejorar rendimiento en consultas de tareas por actividad
ALTER TABLE `tareas` ADD INDEX `idx_actividad_estado` (`actividad_id`, `estado`);

-- 9. Agregar índice para tareas independientes
ALTER TABLE `tareas` ADD INDEX `idx_user_estado` (`user_id`, `estado`);

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

