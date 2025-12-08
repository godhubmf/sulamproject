<?php
// Cash Book Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/FinancialController.php';

initSecureSession();
requireAuth();
requireAdmin(); // Assuming only admins/treasurers access this

// Instantiate Controller
$controller = new FinancialController($mysqli);

// Get filters (default to current year and month)
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
// Default month to current month. If 'all' is passed (empty string or 0), we show all.
// Allowing 'month' to be empty/null means "All".
// If $_GET['month'] is not set, default to current month.
// If $_GET['month'] IS set (even empty), respect it.
if (isset($_GET['month'])) {
    $month = $_GET['month'] === '' || $_GET['month'] === 'all' ? null : (int)$_GET['month'];
} else {
    $month = (int)date('m');
}

$data = $controller->cashBook($year, $month);

extract($data);

// Define page header
$pageHeader = [
    'title' => 'Buku Tunai (Cash Book)',
    'subtitle' => 'View all financial transactions and running balances.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Financial', 'url' => url('financial')],
        ['label' => 'Buku Tunai', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Back', 'icon' => 'fa-arrow-left', 'url' => url('financial'), 'class' => 'btn-secondary'],
    ]
];

// Add page-specific CSS (including stat cards via financial.css)
$additionalStyles = [
    url('features/financial/admin/assets/css/financial.css'),
];

// 1. Capture the inner content
ob_start();
include __DIR__ . '/../views/cash-book.php';
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Buku Tunai';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
