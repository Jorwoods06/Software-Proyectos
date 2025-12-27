-- ============================================
-- Script SQL COMPLETO para hacer actividad_id nullable
-- Ejecutar este script directamente
-- ============================================

-- Paso 1: Eliminar la foreign key constraint si existe
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
      AND TABLE_NAME = 'tareas' 
      AND COLUMN_NAME = 'actividad_id'
      AND REFERENCED_TABLE_NAME = 'actividades'
    LIMIT 1
);

SET @sql = IF(@constraint_name IS NOT NULL,
    CONCAT('ALTER TABLE `tareas` DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT "No hay foreign key para eliminar" AS resultado'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Paso 2: Hacer actividad_id nullable
ALTER TABLE `tareas` 
MODIFY COLUMN `actividad_id` int(11) NULL;

-- Paso 3: Recrear la foreign key constraint (opcional, pero recomendado)
-- Esto permite que las tareas con actividad_id tengan integridad referencial
-- pero también permite null para tareas independientes
ALTER TABLE `tareas` 
ADD CONSTRAINT `tareas_ibfk_actividad` 
FOREIGN KEY (`actividad_id`) 
REFERENCES `actividades` (`id`) 
ON DELETE CASCADE 
ON UPDATE CASCADE;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

