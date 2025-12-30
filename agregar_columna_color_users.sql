-- ========================================================================
-- Script SQL para agregar columna 'color' a la tabla users
-- ========================================================================
-- Este script agrega una columna 'color' para almacenar el color del icono
-- de cada usuario. El color se asigna automáticamente al crear el usuario.
-- ========================================================================

-- Agregar columna color a la tabla users
ALTER TABLE `users` 
ADD COLUMN `color` VARCHAR(7) NULL DEFAULT NULL 
AFTER `estado`;

-- Actualizar usuarios existentes con colores aleatorios
-- Paleta de colores predefinida para iconos de usuarios
UPDATE `users` SET `color` = '#0D6EFD' WHERE `id` % 8 = 0;
UPDATE `users` SET `color` = '#198754' WHERE `id` % 8 = 1;
UPDATE `users` SET `color` = '#DC3545' WHERE `id` % 8 = 2;
UPDATE `users` SET `color` = '#FFC107' WHERE `id` % 8 = 3;
UPDATE `users` SET `color` = '#6F42C1' WHERE `id` % 8 = 4;
UPDATE `users` SET `color` = '#0DCAF0' WHERE `id` % 8 = 5;
UPDATE `users` SET `color` = '#FD7E14' WHERE `id` % 8 = 6;
UPDATE `users` SET `color` = '#20C997' WHERE `id` % 8 = 7;

-- Verificar que todos los usuarios tengan un color asignado
UPDATE `users` SET `color` = '#0D6EFD' WHERE `color` IS NULL;

-- ========================================================================
-- NOTA: Los colores se asignan automáticamente al crear nuevos usuarios
-- mediante la función generarColorUsuario() en el UserService
-- ========================================================================

