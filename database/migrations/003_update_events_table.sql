ALTER TABLE `events`
ADD COLUMN `title` VARCHAR(255) NOT NULL DEFAULT 'New Event' AFTER `id`,
ADD COLUMN `event_date` DATE NULL AFTER `description`,
ADD COLUMN `event_time` TIME NULL AFTER `event_date`,
ADD COLUMN `location` VARCHAR(255) NULL AFTER `event_time`,
ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `image_path`;
