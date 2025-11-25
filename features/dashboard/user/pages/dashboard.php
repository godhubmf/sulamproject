<?php
// Moved from /dashboard.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
requireAuth();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';

// Define page header
$pageHeader = [
    'title' => 'Dashboard',
    'subtitle' => 'Welcome back, ' . $username . '.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Dashboard', 'url' => null],
    ],
];

// 1. Capture the inner content
ob_start();
?>
<div class="small-card" style="max-width:980px;margin:0 auto;padding:1.2rem 1.4rem;">
  <section class="dashboard-cards">
    <a class="dashboard-card" href="<?php echo url('residents'); ?>">
      <i class="fa-solid fa-users icon" aria-hidden="true"></i>
      <h3>Residents</h3>
      <p>Manage residents and households.</p>
    </a>
    <a class="dashboard-card" href="<?php echo url('donations'); ?>">
      <i class="fa-solid fa-coins icon" aria-hidden="true"></i>
      <h3>Donations</h3>
      <p>Track donations and receipts.</p>
    </a>
    <a class="dashboard-card" href="<?php echo url('events'); ?>">
      <i class="fa-solid fa-calendar-days icon" aria-hidden="true"></i>
      <h3>Events</h3>
      <p>Plan and manage events.</p>
    </a>
  </section>
</div>
<?php 
$content = ob_get_clean();

// 2. Wrap into app-layout, which uses the sidebar
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Set page title and include base layout
$pageTitle = 'Dashboard';
include $ROOT . '/features/shared/components/layouts/base.php';
