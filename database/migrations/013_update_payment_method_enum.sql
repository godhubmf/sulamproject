USE masjidkamek;

-- Update existing 'cheque' values to 'bank'
UPDATE financial_deposit_accounts SET payment_method = 'bank' WHERE payment_method = 'cheque';
UPDATE financial_payment_accounts SET payment_method = 'bank' WHERE payment_method = 'cheque';

-- Modify the columns to be ENUM('cash', 'bank')
ALTER TABLE financial_deposit_accounts MODIFY COLUMN payment_method ENUM('cash', 'bank') NOT NULL DEFAULT 'cash';
ALTER TABLE financial_payment_accounts MODIFY COLUMN payment_method ENUM('cash', 'bank') NOT NULL DEFAULT 'cash';
