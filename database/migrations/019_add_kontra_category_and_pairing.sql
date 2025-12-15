-- Migration: Add Kontra (Contra) category and pairing mechanism
-- Date: 2025-12-15
-- Description: Adds kontra column to both deposit and payment accounts for internal transfers (cash <-> bank)
--              Also adds contra_pair_id for hard coupling between paired transactions

-- Add kontra column to financial_deposit_accounts
ALTER TABLE `financial_deposit_accounts`
ADD COLUMN `kontra` DECIMAL(12,2) UNSIGNED DEFAULT 0.00 AFTER `lain_lain_terimaan`,
ADD COLUMN `contra_pair_id` INT UNSIGNED NULL AFTER `payment_reference`,
ADD COLUMN `is_contra_transaction` TINYINT(1) NOT NULL DEFAULT 0 AFTER `contra_pair_id`,
ADD INDEX `idx_contra_pair` (`contra_pair_id`, `is_contra_transaction`);

-- Add kontra column to financial_payment_accounts
ALTER TABLE `financial_payment_accounts`
ADD COLUMN `kontra` DECIMAL(12,2) UNSIGNED DEFAULT 0.00 AFTER `lain_lain_perbelanjaan`,
ADD COLUMN `contra_pair_id` INT UNSIGNED NULL AFTER `payment_reference`,
ADD COLUMN `is_contra_transaction` TINYINT(1) NOT NULL DEFAULT 0 AFTER `contra_pair_id`,
ADD INDEX `idx_contra_pair` (`contra_pair_id`, `is_contra_transaction`);

-- Note: contra_pair_id stores the ID of the paired transaction in the opposite table
-- For deposits: contra_pair_id references financial_payment_accounts.id
-- For payments: contra_pair_id references financial_deposit_accounts.id
-- is_contra_transaction flag helps quickly identify contra entries for exclusion in reports
