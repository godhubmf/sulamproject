<?php
/**
 * FinancialStatementController
 * 
 * Handles logic for generating the Financial Statement (Penyata Terimaan dan Bayaran).
 */

require_once __DIR__ . '/DepositAccountRepository.php';
require_once __DIR__ . '/PaymentAccountRepository.php';

class FinancialStatementController
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Get financial statement data for a specific date range.
     *
     * @param string $startDate YYYY-MM-DD
     * @param string $endDate YYYY-MM-DD
     * @return array
     */
    public function getStatementData(string $startDate, string $endDate): array
    {
        // 1. Calculate Opening Balance (Before Start Date)
        $openingBalance = $this->calculateBalance($startDate);

        // 2. Get Current Period Receipts (Grouped by Category)
        $receipts = $this->getReceiptsByCategory($startDate, $endDate);
        $totalReceipts = array_sum(array_column($receipts, 'amount'));

        // 3. Get Current Period Payments (Grouped by Category)
        $payments = $this->getPaymentsByCategory($startDate, $endDate);
        $totalPayments = array_sum(array_column($payments, 'amount'));

        // 4. Calculate Cash Flow for Current Period (to determine Closing Balance)
        $currentFlow = $this->calculateCashFlow($startDate, $endDate);

        // 5. Calculate Closing Balance
        $closingBalance = [
            'cash' => $openingBalance['cash'] + $currentFlow['cash_in'] - $currentFlow['cash_out'],
            'bank' => $openingBalance['bank'] + $currentFlow['bank_in'] - $currentFlow['bank_out'],
        ];

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'opening_balance' => $openingBalance,
            'receipts' => $receipts,
            'total_receipts' => $totalReceipts,
            'payments' => $payments,
            'total_payments' => $totalPayments,
            'closing_balance' => $closingBalance,
            'surplus_deficit' => $totalReceipts - $totalPayments
        ];
    }

    /**
     * Calculate balance (Cash & Bank) up to a specific date (exclusive).
     */
    private function calculateBalance(string $date): array
    {
        // Cash In (Receipts)
        $cashIn = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', DepositAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_deposit_accounts 
            WHERE tx_date < ? AND payment_method = 'cash'
        ) as t", $date);

        // Bank In (Receipts)
        $bankIn = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', DepositAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_deposit_accounts 
            WHERE tx_date < ? AND payment_method != 'cash'
        ) as t", $date);

        // Cash Out (Payments)
        $cashOut = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', PaymentAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_payment_accounts 
            WHERE tx_date < ? AND payment_method = 'cash'
        ) as t", $date);

        // Bank Out (Payments)
        $bankOut = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', PaymentAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_payment_accounts 
            WHERE tx_date < ? AND payment_method != 'cash'
        ) as t", $date);

        return [
            'cash' => $cashIn - $cashOut,
            'bank' => $bankIn - $bankOut
        ];
    }

    /**
     * Calculate cash flow (In/Out) for Cash and Bank within a date range.
     */
    private function calculateCashFlow(string $startDate, string $endDate): array
    {
        // Cash In
        $cashIn = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', DepositAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_deposit_accounts 
            WHERE tx_date BETWEEN ? AND ? AND payment_method = 'cash'
        ) as t", $startDate, $endDate);

        // Bank In
        $bankIn = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', DepositAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_deposit_accounts 
            WHERE tx_date BETWEEN ? AND ? AND payment_method != 'cash'
        ) as t", $startDate, $endDate);

        // Cash Out
        $cashOut = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', PaymentAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_payment_accounts 
            WHERE tx_date BETWEEN ? AND ? AND payment_method = 'cash'
        ) as t", $startDate, $endDate);

        // Bank Out
        $bankOut = $this->getSum("SELECT SUM(amount) FROM (
            SELECT (" . implode(' + ', PaymentAccountRepository::CATEGORY_COLUMNS) . ") as amount 
            FROM financial_payment_accounts 
            WHERE tx_date BETWEEN ? AND ? AND payment_method != 'cash'
        ) as t", $startDate, $endDate);

        return [
            'cash_in' => $cashIn,
            'bank_in' => $bankIn,
            'cash_out' => $cashOut,
            'bank_out' => $bankOut
        ];
    }

    /**
     * Get receipts grouped by category.
     */
    private function getReceiptsByCategory(string $startDate, string $endDate): array
    {
        $results = [];
        foreach (DepositAccountRepository::CATEGORY_COLUMNS as $col) {
            // Skip kontra category - it's for internal transfers only
            if ($col === 'kontra') {
                continue;
            }
            
            $sum = $this->getSum("SELECT SUM($col) FROM financial_deposit_accounts WHERE tx_date BETWEEN ? AND ?", $startDate, $endDate);
            if ($sum > 0) {
                $results[] = [
                    'label' => DepositAccountRepository::CATEGORY_LABELS[$col],
                    'amount' => $sum
                ];
            }
        }
        return $results;
    }

    /**
     * Get payments grouped by category.
     */
    private function getPaymentsByCategory(string $startDate, string $endDate): array
    {
        $results = [];
        foreach (PaymentAccountRepository::CATEGORY_COLUMNS as $col) {
            // Skip kontra category - it's for internal transfers only
            if ($col === 'kontra') {
                continue;
            }
            
            $sum = $this->getSum("SELECT SUM($col) FROM financial_payment_accounts WHERE tx_date BETWEEN ? AND ?", $startDate, $endDate);
            if ($sum > 0) {
                $results[] = [
                    'label' => PaymentAccountRepository::CATEGORY_LABELS[$col],
                    'amount' => $sum
                ];
            }
        }
        return $results;
    }

    /**
     * Helper to execute a query and return a single scalar value (sum).
     */
    private function getSum(string $sql, ...$params): float
    {
        $stmt = $this->mysqli->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        $stmt->close();
        return (float) ($row[0] ?? 0);
    }
}
