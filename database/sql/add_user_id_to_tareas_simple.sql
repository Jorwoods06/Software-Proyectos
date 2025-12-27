-- ============================================
-- Script SQL SIMPLE para agregar user_id a tareas
-- Ejecutar este script directamente
-- ============================================

-- Agregar columna user_id (si ya existe, dará error pero no afecta)
ALTER TABLE `tareas` 
ADD COLUMN `user_id` int(11) NULL AFTER `responsable_id`;

-- Agregar índice
ALTER TABLE `tareas` 
ADD INDEX `idx_user_id` (`user_id`);

-- Agregar foreign key (eliminar primero si existe)
ALTER TABLE `tareas` 
DROP FOREIGN KEY IF EXISTS `tareas_ibfk_user`;

ALTER TABLE `tareas` 
ADD CONSTRAINT `tareas_ibfk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

