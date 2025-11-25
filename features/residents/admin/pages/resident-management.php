<?php
// User Management Page
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once __DIR__ . '/../controllers/UsersController.php';

initSecureSession();
requireAuth();

// Instantiate Controller
$controller = new UsersController($mysqli);
$data = $controller->index();
extract($data); // Makes $users and $currentRole available to the view

// Define page header
$pageHeader = [
    'title' => 'Resident Management',
    'subtitle' => 'Manage household and individual resident records.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Residents', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Add Resident', 'icon' => 'fa-user-plus', 'url' => url('features/residents/admin/pages/resident-add.php'), 'class' => 'btn-primary'],
        ['label' => 'Import Data', 'icon' => 'fa-file-import', 'url' => url('features/residents/admin/pages/import.php'), 'class' => 'btn-secondary'],
    ]
];

// 1. Capture the inner content
ob_start();
include __DIR__ . '/../views/manage-residents.php';
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Resident Management';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
