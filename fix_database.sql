-- Script para corregir la base de datos cide_sena
-- Ejecutar este archivo para agregar la columna nombre_completo

USE cide_sena;

-- 1. Agregar columna nombre_completo a la tabla aprendices
ALTER TABLE `aprendices` 
ADD COLUMN `nombre_completo` VARCHAR(255) NULL AFTER `apellidos`;

-- 2. Poblar nombre_completo con datos existentes (concatenar nombres + apellidos)
UPDATE `aprendices` 
SET `nombre_completo` = TRIM(CONCAT(COALESCE(`nombres`, ''), ' ', COALESCE(`apellidos`, '')))
WHERE `nombre_completo` IS NULL OR `nombre_completo` = '';

-- 3. Hacer la columna NOT NULL después de poblarla
ALTER TABLE `aprendices` 
MODIFY COLUMN `nombre_completo` VARCHAR(255) NOT NULL;

-- 4. Verificar los cambios
SELECT id_aprendiz, nombres, apellidos, nombre_completo 
FROM aprendices 
LIMIT 10;
