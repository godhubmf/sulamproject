<?php
/**
 * DepositAccountRepository - Data access layer for financial_deposit_accounts
 * 
 * Handles CRUD operations for deposit records using mysqli prepared statements.
 */

class DepositAccountRepository
{
    private mysqli $mysqli;

    /**
     * Category columns in the deposits table
     */
    public const CATEGORY_COLUMNS = [
        'geran_kerajaan',
        'sumbangan_derma',
        'tabung_masjid',
        'kutipan_jumaat_sadak',
        'kutipan_aidilfitri_aidiladha',
        'sewa_peralatan_masjid',
        'hibah_faedah_bank',
        'faedah_simpanan_tetap',
        'sewa_rumah_kedai_tadika_menara',
        'lain_lain_terimaan',
    ];

    /**
     * Category labels (display names)
     */
    public const CATEGORY_LABELS = [
        'geran_kerajaan' => 'Geran Kerajaan',
        'sumbangan_derma' => 'Sumbangan/Derma',
        'tabung_masjid' => 'Tabung Masjid',
        'kutipan_jumaat_sadak' => 'Kutipan Jumaat (Sadak)',
        'kutipan_aidilfitri_aidiladha' => 'Kutipan Aidilfitri/Aidiladha',
        'sewa_peralatan_masjid' => 'Sewa Peralatan Masjid',
        'hibah_faedah_bank' => 'Hibah/Faedah Bank',
        'faedah_simpanan_tetap' => 'Faedah Simpanan Tetap',
        'sewa_rumah_kedai_tadika_menara' => 'Sewa (Rumah/Kedai/Tadika/Menara)',
        'lain_lain_terimaan' => 'Lain-lain Terimaan',
    ];

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Get all deposit records, ordered by tx_date descending
     *
     * @return array
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM financial_deposit_accounts ORDER BY tx_date DESC, id DESC";
        $result = $this->mysqli->query($sql);

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Find deposits with filters
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

        // Search filter (receipt_number, description, received_from)
        if (!empty($filters['search'])) {
            $conditions[] = "(receipt_number LIKE ? OR description LIKE ? OR received_from LIKE ?)";
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
        $sql = "SELECT * FROM financial_deposit_accounts";
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
     * Find a single deposit record by ID
     *
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM financial_deposit_accounts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return $row ?: null;
    }
    
    /**
     * Generate next receipt number in format RR/YEAR/COUNT
     *
     * @return string
     */
    public function generateReceiptNumber(): string
    {
        $year = date('Y');
        $prefix = "RR/{$year}/";

        // Find the highest count for this year
        $sql = "SELECT receipt_number FROM financial_deposit_accounts
                WHERE receipt_number LIKE ?
                ORDER BY receipt_number DESC
                LIMIT 1";

        $stmt = $this->mysqli->prepare($sql);
        $pattern = "RR/{$year}/%";
        $stmt->bind_param('s', $pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && !empty($row['receipt_number']) && preg_match('/RR\/\d{4}\/(\d+)/', $row['receipt_number'], $matches)) {
            $nextCount = intval($matches[1]) + 1;
        } else {
            $nextCount = 1;
        }

        // Format with leading zeros (4 digits)
        return $prefix . str_pad($nextCount, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new deposit record
     *
     * @param array $data Associative array with tx_date, description, and category amounts
     * @return int The inserted ID
     */
    public function create(array $data): int
    {
        // Auto-generate receipt number if not provided or empty
        if (empty($data['receipt_number'])) {
            $data['receipt_number'] = $this->generateReceiptNumber();
        }

        $columns = [
            'tx_date', 
            'description', 
            'receipt_number', 
            'received_from', 
            'payment_method', 
            'payment_reference'
        ];
        $placeholders = ['?', '?', '?', '?', '?', '?'];
        $types = 'ssssss';
        $values = [
            $data['tx_date'],
            $data['description'],
            $data['receipt_number'] ?? null,
            $data['received_from'] ?? null,
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
            "INSERT INTO financial_deposit_accounts (%s) VALUES (%s)",
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
     * Update an existing deposit record
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
            'receipt_number = ?',
            'received_from = ?',
            'payment_method = ?',
            'payment_reference = ?'
        ];
        $types = 'ssssss';
        $values = [
            $data['tx_date'],
            $data['description'],
            $data['receipt_number'] ?? null,
            $data['received_from'] ?? null,
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
            "UPDATE financial_deposit_accounts SET %s WHERE id = ?",
            implode(', ', $setClauses)
        );

        $stmt = $this->mysqli->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $success = $stmt->execute();
        $stmt->close();

        return $success;
    }

    /**
     * Delete a deposit record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $stmt = $this->mysqli->prepare("DELETE FROM financial_deposit_accounts WHERE id = ?");
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
     * Calculate row total for a deposit record
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
}
