<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
requireAuth();

$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
$pageTitle = "Events";

// Include the view
require $ROOT . '/features/events/user/views/events.php';
