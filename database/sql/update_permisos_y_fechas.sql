-- ============================================
-- Script SQL para actualizar permisos y fechas/horarios
-- Fecha: 15 de Diciembre 2025
-- Sistema de Gestión de Proyectos
-- ============================================

-- ============================================
-- 1. ACTUALIZAR TABLA ACTIVIDADES
-- ============================================

-- 1.1. Actualizar ENUM de estado para incluir 'eliminado'
ALTER TABLE `actividades` 
MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','finalizado','eliminado') DEFAULT 'pendiente';

-- 1.2. Convertir fecha_inicio y fecha_fin de DATE a DATETIME
-- Paso 1: Renombrar columnas existentes a temporales
ALTER TABLE `actividades` 
CHANGE COLUMN `fecha_inicio` `fecha_inicio_old` DATE NULL,
CHANGE COLUMN `fecha_fin` `fecha_fin_old` DATE NULL;

-- Paso 2: Crear nuevas columnas DATETIME
ALTER TABLE `actividades` 
ADD COLUMN `fecha_inicio` DATETIME NULL AFTER `descripcion`,
ADD COLUMN `fecha_fin` DATETIME NULL AFTER `fecha_inicio`;

-- Paso 3: Migrar datos: convertir fechas a datetime (añadir hora)
UPDATE `actividades` 
SET `fecha_inicio` = CONCAT(`fecha_inicio_old`, ' 00:00:00') 
WHERE `fecha_inicio_old` IS NOT NULL;

UPDATE `actividades` 
SET `fecha_fin` = CONCAT(`fecha_fin_old`, ' 23:59:59') 
WHERE `fecha_fin_old` IS NOT NULL;

-- Paso 4: Eliminar columnas temporales
ALTER TABLE `actividades` 
DROP COLUMN `fecha_inicio_old`,
DROP COLUMN `fecha_fin_old`;

-- ============================================
-- 2. ACTUALIZAR TABLA TAREAS
-- ============================================

-- 2.1. Actualizar ENUM de estado para incluir 'eliminado'
ALTER TABLE `tareas` 
MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','completado','eliminado') DEFAULT 'pendiente';

-- 2.2. Convertir fecha_inicio y fecha_fin de DATE a DATETIME
-- Paso 1: Renombrar columnas existentes a temporales
ALTER TABLE `tareas` 
CHANGE COLUMN `fecha_inicio` `fecha_inicio_old` DATE NULL,
CHANGE COLUMN `fecha_fin` `fecha_fin_old` DATE NULL;

-- Paso 2: Crear nuevas columnas DATETIME
ALTER TABLE `tareas` 
ADD COLUMN `fecha_inicio` DATETIME NULL AFTER `descripcion`,
ADD COLUMN `fecha_fin` DATETIME NULL AFTER `fecha_inicio`;

-- Paso 3: Migrar datos: convertir fechas a datetime (añadir hora)
UPDATE `tareas` 
SET `fecha_inicio` = CONCAT(`fecha_inicio_old`, ' 00:00:00') 
WHERE `fecha_inicio_old` IS NOT NULL;

UPDATE `tareas` 
SET `fecha_fin` = CONCAT(`fecha_fin_old`, ' 23:59:59') 
WHERE `fecha_fin_old` IS NOT NULL;

-- Paso 4: Eliminar columnas temporales
ALTER TABLE `tareas` 
DROP COLUMN `fecha_inicio_old`,
DROP COLUMN `fecha_fin_old`;

-- ============================================
-- 3. AGREGAR CAMPO created_by A PROYECTOS
-- ============================================

-- 3.1. Agregar columna created_by
ALTER TABLE `proyectos` 
ADD COLUMN `created_by` INT(11) NULL AFTER `departamento_id`;

-- 3.2. Agregar foreign key (verificar si ya existe primero)
-- Si el constraint ya existe, este comando fallará, puedes ignorarlo
ALTER TABLE `proyectos` 
ADD CONSTRAINT `fk_proyectos_created_by` 
FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL;

-- 3.3. Migrar datos: obtener el creador desde proyecto_usuario donde rol_proyecto = 'lider'
UPDATE `proyectos` p 
INNER JOIN `proyecto_usuario` pu ON p.id = pu.proyecto_id 
SET p.created_by = pu.user_id 
WHERE pu.rol_proyecto = 'lider';

-- ============================================
-- FIN DEL SCRIPT
-- ============================================

-- ============================================
-- VERIFICACIÓN DE CAMBIOS (OPCIONAL)
-- ============================================
-- Descomentar las siguientes líneas para verificar los cambios:

-- SELECT 'Actividades totales' AS tabla, COUNT(*) AS registros FROM actividades;
-- SELECT 'Tareas totales' AS tabla, COUNT(*) AS registros FROM tareas;
-- SELECT 'Proyectos con created_by' AS tabla, COUNT(*) AS registros FROM proyectos WHERE created_by IS NOT NULL;
-- 
-- SELECT 'Estructura actividades' AS info;
-- DESCRIBE actividades;
-- 
-- SELECT 'Estructura tareas' AS info;
-- DESCRIBE tareas;
-- 
-- SELECT 'Estructura proyectos' AS info;
-- DESCRIBE proyectos;

