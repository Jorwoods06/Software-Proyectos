-- ============================================
-- Script SQL para agregar user_id a tareas
-- Ejecutar este script si user_id no existe
-- ============================================

-- Verificar y agregar columna user_id si no existe
SET @dbname = DATABASE();
SET @tablename = 'tareas';
SET @columnname = 'user_id';

-- Agregar columna si no existe
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  'SELECT "Columna user_id ya existe" AS resultado',
  CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` int(11) NULL AFTER `responsable_id`')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Agregar índice si no existe
ALTER TABLE `tareas` 
ADD INDEX IF NOT EXISTS `idx_user_id` (`user_id`);

-- Agregar foreign key si no existe
-- Primero eliminar si existe
SET @constraint_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = @dbname 
      AND TABLE_NAME = @tablename 
      AND COLUMN_NAME = @columnname 
      AND CONSTRAINT_NAME = 'tareas_ibfk_user'
);

SET @preparedStatement = IF(@constraint_exists = 0,
  CONCAT('ALTER TABLE `', @tablename, '` ADD CONSTRAINT `tareas_ibfk_user` FOREIGN KEY (`', @columnname, '`) REFERENCES `users` (`id`) ON DELETE CASCADE'),
  'SELECT "Constraint ya existe" AS resultado'
);
PREPARE alterIfNotExists2 FROM @preparedStatement;
EXECUTE alterIfNotExists2;
DEALLOCATE PREPARE alterIfNotExists2;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

