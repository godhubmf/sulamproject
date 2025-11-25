ALTER TABLE `donations`
ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT 'General Donation' AFTER `id`,
ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `image_path`;
