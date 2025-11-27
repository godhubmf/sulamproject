-- Fix 'roles' column type to prevent "Data truncated" error
-- Correcting the schema to match the application code usage (plural 'roles')

ALTER TABLE `users` MODIFY COLUMN `roles` VARCHAR(50) NOT NULL DEFAULT 'resident';