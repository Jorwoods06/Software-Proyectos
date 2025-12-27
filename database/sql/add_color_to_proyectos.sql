-- ============================================================================
-- Script SQL: Agregar campo color a la tabla proyectos
-- ============================================================================
-- Descripción: Este script agrega el campo 'color' a la tabla 'proyectos'
--              para permitir que cada proyecto tenga un color identificador
--              en el sidebar.
--
-- Fecha: 2025-12-12
-- ============================================================================

-- Agregar columna color a la tabla proyectos
ALTER TABLE `proyectos` 
ADD COLUMN `color` VARCHAR(7) NULL 
AFTER `estado`;

-- Comentario en la columna (opcional, dependiendo de la versión de MySQL)
-- ALTER TABLE `proyectos` MODIFY COLUMN `color` VARCHAR(7) NULL COMMENT 'Color hexadecimal del proyecto para identificación visual';

-- ============================================================================
-- Script SQL: Asignar colores a proyectos existentes
-- ============================================================================
-- Descripción: Este script asigna colores automáticamente a los proyectos
--              existentes que no tengan color asignado, basándose en su ID.
-- ============================================================================

-- Actualizar proyectos existentes sin color
UPDATE `proyectos` 
SET `color` = CASE 
    WHEN (`id` - 1) % 20 = 0 THEN '#0D6EFD'
    WHEN (`id` - 1) % 20 = 1 THEN '#6F42C1'
    WHEN (`id` - 1) % 20 = 2 THEN '#D63384'
    WHEN (`id` - 1) % 20 = 3 THEN '#DC3545'
    WHEN (`id` - 1) % 20 = 4 THEN '#FD7E14'
    WHEN (`id` - 1) % 20 = 5 THEN '#FFC107'
    WHEN (`id` - 1) % 20 = 6 THEN '#20C997'
    WHEN (`id` - 1) % 20 = 7 THEN '#198754'
    WHEN (`id` - 1) % 20 = 8 THEN '#0DCAF0'
    WHEN (`id` - 1) % 20 = 9 THEN '#6610F2'
    WHEN (`id` - 1) % 20 = 10 THEN '#E83E8C'
    WHEN (`id` - 1) % 20 = 11 THEN '#6C757D'
    WHEN (`id` - 1) % 20 = 12 THEN '#0DCAF0'
    WHEN (`id` - 1) % 20 = 13 THEN '#FF6B6B'
    WHEN (`id` - 1) % 20 = 14 THEN '#4ECDC4'
    WHEN (`id` - 1) % 20 = 15 THEN '#45B7D1'
    WHEN (`id` - 1) % 20 = 16 THEN '#FFA07A'
    WHEN (`id` - 1) % 20 = 17 THEN '#98D8C8'
    WHEN (`id` - 1) % 20 = 18 THEN '#F7DC6F'
    WHEN (`id` - 1) % 20 = 19 THEN '#BB8FCE'
    ELSE '#0D6EFD'
END
WHERE `color` IS NULL OR `color` = '';

-- Verificar que los colores se asignaron correctamente
-- SELECT id, nombre, color FROM proyectos ORDER BY id;

