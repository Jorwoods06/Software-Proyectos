-- ============================================
-- Script SQL para REVERTIR cambios de permisos y fechas/horarios
-- Fecha: 15 de Diciembre 2025
-- Sistema de Gestión de Proyectos
-- USAR SOLO SI ES NECESARIO REVERTIR LOS CAMBIOS
-- ============================================

-- ============================================
-- 1. REVERTIR TABLA PROYECTOS
-- ============================================

-- 1.1. Eliminar foreign key
ALTER TABLE `proyectos` 
DROP FOREIGN KEY `fk_proyectos_created_by`;

-- 1.2. Eliminar columna created_by
ALTER TABLE `proyectos` 
DROP COLUMN `created_by`;

-- ============================================
-- 2. REVERTIR TABLA TAREAS
-- ============================================

-- 2.1. Convertir fecha_inicio y fecha_fin de DATETIME a DATE
-- Primero crear columnas temporales
ALTER TABLE `tareas` 
ADD COLUMN `fecha_inicio_temp` DATE NULL AFTER `descripcion`,
ADD COLUMN `fecha_fin_temp` DATE NULL AFTER `fecha_inicio_temp`;

-- 2.2. Migrar datos: convertir datetime a date (solo la fecha)
UPDATE `tareas` 
SET `fecha_inicio_temp` = DATE(`fecha_inicio`) 
WHERE `fecha_inicio` IS NOT NULL;

UPDATE `tareas` 
SET `fecha_fin_temp` = DATE(`fecha_fin`) 
WHERE `fecha_fin` IS NOT NULL;

-- 2.3. Eliminar columnas antiguas y renombrar las nuevas
ALTER TABLE `tareas` 
DROP COLUMN `fecha_inicio`,
DROP COLUMN `fecha_fin`,
CHANGE COLUMN `fecha_inicio_temp` `fecha_inicio` DATE NULL,
CHANGE COLUMN `fecha_fin_temp` `fecha_fin` DATE NULL;

-- 2.4. Revertir ENUM de estado (eliminar 'eliminado')
-- Primero actualizar registros eliminados a otro estado
UPDATE `tareas` 
SET `estado` = 'pendiente' 
WHERE `estado` = 'eliminado';

ALTER TABLE `tareas` 
MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','completado') DEFAULT 'pendiente';

-- ============================================
-- 3. REVERTIR TABLA ACTIVIDADES
-- ============================================

-- 3.1. Convertir fecha_inicio y fecha_fin de DATETIME a DATE
-- Primero crear columnas temporales
ALTER TABLE `actividades` 
ADD COLUMN `fecha_inicio_temp` DATE NULL AFTER `descripcion`,
ADD COLUMN `fecha_fin_temp` DATE NULL AFTER `fecha_inicio_temp`;

-- 3.2. Migrar datos: convertir datetime a date (solo la fecha)
UPDATE `actividades` 
SET `fecha_inicio_temp` = DATE(`fecha_inicio`) 
WHERE `fecha_inicio` IS NOT NULL;

UPDATE `actividades` 
SET `fecha_fin_temp` = DATE(`fecha_fin`) 
WHERE `fecha_fin` IS NOT NULL;

-- 3.3. Eliminar columnas antiguas y renombrar las nuevas
ALTER TABLE `actividades` 
DROP COLUMN `fecha_inicio`,
DROP COLUMN `fecha_fin`,
CHANGE COLUMN `fecha_inicio_temp` `fecha_inicio` DATE NULL,
CHANGE COLUMN `fecha_fin_temp` `fecha_fin` DATE NULL;

-- 3.4. Revertir ENUM de estado (eliminar 'eliminado')
-- Primero actualizar registros eliminados a otro estado
UPDATE `actividades` 
SET `estado` = 'pendiente' 
WHERE `estado` = 'eliminado';

ALTER TABLE `actividades` 
MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','finalizado') DEFAULT 'pendiente';

-- ============================================
-- FIN DEL SCRIPT DE ROLLBACK
-- ============================================

