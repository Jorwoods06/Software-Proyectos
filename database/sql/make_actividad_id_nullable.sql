-- ============================================
-- Script SQL para hacer actividad_id nullable
-- Ejecutar este script directamente
-- ============================================

-- Hacer actividad_id nullable para permitir tareas independientes
ALTER TABLE `tareas` 
MODIFY COLUMN `actividad_id` int(11) NULL;

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

