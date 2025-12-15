<?php
/**
 * PaymentAccountRepository - Data access layer for financial_payment_accounts
 * 
 * Handles CRUD operations for payment records using mysqli prepared statements.
 */

class PaymentAccountRepository
{
    private mysqli $mysqli;

    /**
     * Category columns in the payments table
     */
    public const CATEGORY_COLUMNS = [
        'perayaan_islam',
        'pengimarahan_aktiviti_masjid',
        'penyelenggaraan_masjid',
        'keperluan_kelengkapan_masjid',
        'gaji_upah_saguhati_elaun',
        'sumbangan_derma',
        'mesyuarat_jamuan',
        'utiliti',
        'alat_tulis_percetakan',
        'pengangkutan_perjalanan',
        'caj_bank',
        'lain_lain_perbelanjaan',
        'kontra',
    ];

    /**
     * Category labels (display names)
     */
    public const CATEGORY_LABELS = [
        'perayaan_islam' => 'Perayaan Islam',
        'pengimarahan_aktiviti_masjid' => 'Pengimarahan & Aktiviti Masjid',
        'penyelenggaraan_masjid' => 'Penyelenggaraan Masjid',
        'keperluan_kelengkapan_masjid' => 'Keperluan & Kelengkapan Masjid',
        'gaji_upah_saguhati_elaun' => 'Gaji/Upah/Saguhati/Elaun',
        'sumbangan_derma' => 'Sumbangan/Derma',
        'mesyuarat_jamuan' => 'Mesyuarat & Jamuan',
        'utiliti' => 'Utiliti',
        'alat_tulis_percetakan' => 'Alat Tulis & Percetakan',
        'pengangkutan_perjalanan' => 'Pengangkutan & Perjalanan',
        'caj_bank' => 'Caj Bank',
        'lain_lain_perbelanjaan' => 'Lain-lain Perbelanjaan',
        'kontra' => 'Kontra',
    ];

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Get all payment records, ordered by tx_date descending
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM financial_payment_accounts ORDER BY tx_date DESC, id DESC";
        $result = $this->mysqli->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Find payments with filters
     * 
     * @param array $filters Associative array with optional keys: date_from, date_to, payment_method, search, categories
     * @return array
     */
    public function findWithFilters(array $filters = []): array
    {
        $conditions = [];
        $params = [];
        $types = '';

        // Date range filter
        if (!empty($filters['date_from'])) {
            $conditions[] = "tx_date >= ?";
            $params[] = $filters['date_from'];
            $types .= 's';
        }
        if (!empty($filters['date_to'])) {
            $conditions[] = "tx_date <= ?";
            $params[] = $filters['date_to'];
            $types .= 's';
        }

        // Payment method filter
        if (!empty($filters['payment_method'])) {
            $conditions[] = "payment_method = ?";
            $params[] = $filters['payment_method'];
            $types .= 's';
        }

        // Search filter (voucher_number, description, paid_to)
        if (!empty($filters['search'])) {
            $conditions[] = "(voucher_number LIKE ? OR description LIKE ? OR paid_to LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= 'sss';
        }

        // Category filter (show records with non-zero values in selected categories)
        if (!empty($filters['categories']) && is_array($filters['categories'])) {
            $categoryConditions = [];
            foreach ($filters['categories'] as $category) {
                if (in_array($category, self::CATEGORY_COLUMNS)) {
                    $categoryConditions[] = "`$category` > 0";
                }
            }
            if (!empty($categoryConditions)) {
                $conditions[] = "(" . implode(' OR ', $categoryConditions) . ")";
            }
        }

        // Build SQL query
        $sql = "SELECT * FROM financial_payment_accounts";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        $sql .= " ORDER BY tx_date DESC, id DESC";

        // Execute query
        if (!empty($params)) {
            $stmt = $this->mysqli->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();
            return $rows;
        } else {
            $result = $this->mysqli->query($sql);
            $rows = [];
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            return $rows;
        }
    }

    /**
     * Find a single payment record by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM financial_payment_accounts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }

    /**
     * Generate next voucher number in format MADU/YEAR/COUNT
     * 
     * @return string
     */
    public function generateVoucherNumber(): string
    {
        $year = date('Y');
        $prefix = "MADU/{$year}/";
        
        // Find the highest count for this year
        $sql = "SELECT voucher_number FROM financial_payment_accounts 
                WHERE voucher_number LIKE ? 
                ORDER BY voucher_number DESC 
                LIMIT 1";
        
        $stmt = $this->mysqli->prepare($sql);
        $pattern = "MADU/{$year}/%";
        $stmt->bind_param('s', $pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row && preg_match('/MADU\/\d{4}\/(\d+)/', $row['voucher_number'], $matches)) {
            $nextCount = intval($matches[1]) + 1;
        } else {
            $nextCount = 1;
        }
        
        // Format with leading zeros (4 digits)
        return $prefix . str_pad($nextCount, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new payment record
     *
     * @param array $data Associative array with tx_date, description, and category amounts
     * @return int The inserted ID
     */
    public function create(array $data): int
    {
        // Auto-generate voucher number if not provided or empty
        if (empty($data['voucher_number'])) {
            $data['voucher_number'] = $this->generateVoucherNumber();
        }

        $columns = [
            'tx_date', 
            'description', 
            'voucher_number', 
            'paid_to', 
            'payee_ic', 
            'payee_bank_name', 
            'payee_bank_account', 
            'payment_method', 
            'payment_reference'
        ];
        $placeholders = ['?', '?', '?', '?', '?', '?', '?', '?', '?'];
        $types = 'sssssssss';
        $values = [
            $data['tx_date'],
            $data['description'],
            $data['voucher_number'],
            $data['paid_to'] ?? null,
            $data['payee_ic'] ?? null,
            $data['payee_bank_name'] ?? null,
            $data['payee_bank_account'] ?? null,
            $data['payment_method'] ?? 'cash',
            $data['payment_reference'] ?? null,
        ];

        foreach (self::CATEGORY_COLUMNS as $col) {
            $columns[] = $col;
            $placeholders[] = '?';
            $types .= 'd';
            $values[] = $this->sanitizeAmount($data[$col] ?? 0);
        }

        $sql = sprintf(
            "INSERT INTO financial_payment_accounts (%s) VALUES (%s)",
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $insertedId = $stmt->insert_id;
        $stmt->close();

        return $insertedId;
    }

    /**
     * Update an existing payment record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $setClauses = [
            'tx_date = ?', 
            'description = ?',
            'voucher_number = ?',
            'paid_to = ?',
            'payee_ic = ?',
            'payee_bank_name = ?',
            'payee_bank_account = ?',
            'payment_method = ?',
            'payment_reference = ?'
        ];
        $types = 'sssssssss';
        $values = [
            $data['tx_date'],
            $data['description'],
            $data['voucher_number'] ?? null,
            $data['paid_to'] ?? null,
            $data['payee_ic'] ?? null,
            $data['payee_bank_name'] ?? null,
            $data['payee_bank_account'] ?? null,
            $data['payment_method'] ?? 'cash',
            $data['payment_reference'] ?? null,
        ];

        foreach (self::CATEGORY_COLUMNS as $col) {
            $setClauses[] = "$col = ?";
            $types .= 'd';
            $values[] = $this->sanitizeAmount($data[$col] ?? 0);
        }

        $types .= 'i';
        $values[] = $id;

        $sql = sprintf(
            "UPDATE financial_payment_accounts SET %s WHERE id = ?",
            implode(', ', $setClauses)
        );

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Delete a payment record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->mysqli->prepare("DELETE FROM financial_payment_accounts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Sanitize amount to a float, defaulting to 0.00 for invalid values
     *
     * @param mixed $value
     * @return float
     */
    private function sanitizeAmount($value): float
    {
        if (is_numeric($value) && $value > 0) {
            return (float) $value;
        }
        return 0.00;
    }

    /**
     * Calculate row total for a payment record
     *
     * @param array $row
     * @return float
     */
    public function calculateRowTotal(array $row): float
    {
        $total = 0.0;
        foreach (self::CATEGORY_COLUMNS as $col) {
            $total += (float) ($row[$col] ?? 0);
        }
        return $total;
    }

    /**
     * Update contra_pair_id and is_contra_transaction flag for a payment
     *
     * @param int $id Payment ID
     * @param int $contraPairId Deposit ID that this payment is paired with
     * @return bool
     */
    public function updateContraPair(int $id, int $contraPairId): bool
    {
        $stmt = $this->mysqli->prepare(
            "UPDATE financial_payment_accounts 
             SET contra_pair_id = ?, is_contra_transaction = 1 
             WHERE id = ?"
        );
        $stmt->bind_param('ii', $contraPairId, $id);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Check if a payment has a kontra amount
     *
     * @param array $data Payment data
     * @return bool
     */
    public function hasKontraAmount(array $data): bool
    {
        return isset($data['kontra']) && $this->sanitizeAmount($data['kontra']) > 0;
    }

    /**
     * Get the contra pair deposit ID for a payment
     *
     * @param int $id Payment ID
     * @return int|null Deposit ID or null if no pair
     */
    public function getContraPairId(int $id): ?int
    {
        $stmt = $this->mysqli->prepare(
            "SELECT contra_pair_id FROM financial_payment_accounts WHERE id = ?"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row && $row['contra_pair_id'] ? (int)$row['contra_pair_id'] : null;
    }
}
