-- ============================================
-- Script SQL SIMPLE para hacer actividad_id nullable
-- Ejecutar este script directamente
-- Si da error de foreign key, ejecutar primero:
-- ALTER TABLE `tareas` DROP FOREIGN KEY `tareas_ibfk_1`;
-- ============================================

-- Hacer actividad_id nullable
ALTER TABLE `tareas` 
MODIFY COLUMN `actividad_id` int(11) NULL;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

