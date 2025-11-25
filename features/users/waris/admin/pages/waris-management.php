<?php
// Waris Management Page (Admin) - View Specific User's Waris
$ROOT = dirname(__DIR__, 5); // features/users/waris/admin/pages -> root
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/WarisAdminController.php';

initSecureSession();
requireAuth();
requireAdmin(); // Ensure only admins can access

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

if ($userId === 0) {
    // If no user specified, redirect back to user list or show error
    redirect('/users');
    exit;
}

// Instantiate Controller
$controller = new WarisAdminController($mysqli);
$data = $controller->showUserWaris($userId);
extract($data); // Makes $targetUser and $warisList available to the view

// 1. Capture the inner content
ob_start();
include __DIR__ . '/../views/user-waris-list.php';
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Waris Details';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
