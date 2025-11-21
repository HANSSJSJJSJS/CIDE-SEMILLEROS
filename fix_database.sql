-- Script para alinear la base de datos con el esquema Fusionmain
-- Ejecútalo sobre tu BD viva (ajusta el nombre si aplica)

-- 0) USE opcional
-- USE `cide_sena`;

-- 1) aprendices: asegurar columna estado
ALTER TABLE `aprendices`
  ADD COLUMN `estado` ENUM('Activo','Inactivo') NOT NULL DEFAULT 'Activo' AFTER `id_usuario`;

-- 2) documentos: asegurar columna estado
ALTER TABLE `documentos`
  ADD COLUMN `estado` ENUM('pendiente','completado') NOT NULL DEFAULT 'pendiente' AFTER `documento`;

-- 3) Índices o auto_increment (ajustar si fuese necesario)
-- Nota: estos cambios son opcionales y dependen de tu estado actual.
-- ALTER TABLE `archivos` MODIFY `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT;
-- ALTER TABLE `documentos` MODIFY `id_documento` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT;

-- 4) Normalizar datos derivados (opcional)
-- UPDATE `aprendices` SET `estado` = IFNULL(`estado`, 'Activo');

-- 5) Verificaciones rápidas
-- SHOW COLUMNS FROM `aprendices` LIKE 'estado';
-- SHOW COLUMNS FROM `documentos` LIKE 'estado';
