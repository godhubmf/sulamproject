<?php
// Reusable sidebar template under features. Set $currentPage if needed.
$ROOT = dirname(__DIR__, 3);
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
$isAdmin = isAdmin();
$base = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/sulamprojectex';
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
$username = $_SESSION['username'] ?? 'User';
$userRole = $isAdmin ? 'Admin' : 'Resident';
?>
<aside class="sidebar">
  <!-- Brand Section -->
  <div class="brand">
    <div class="brand-text">
      <i class="fa-solid fa-mosque"></i> masjidkamek
    </div>
    <div class="logo-container">
      <img src="<?php echo $base; ?>/assets/uploads/masjid_logo.jpg" alt="Masjid Logo" class="sidebar-logo">
    </div>
  </div>
  
  <!-- Navigation -->
  <nav class="nav">
    <a href="<?php echo $base; ?>/dashboard" class="<?php echo str_starts_with($path, "$base/dashboard") ? 'active' : ''; ?>">
      <i class="fas fa-home"></i> Dashboard
    </a>
    <?php if ($isAdmin): ?>
    <a href="<?php echo $base; ?>/users" class="<?php echo str_starts_with($path, "$base/users") ? 'active' : ''; ?>">
      <i class="fas fa-users"></i> Users
    </a>
    <a href="<?php echo $base; ?>/waris" class="<?php echo str_starts_with($path, "$base/waris") ? 'active' : ''; ?>">
      <i class="fas fa-user-friends"></i> Waris
    </a>
    <?php endif; ?>
    <a href="<?php echo $base; ?>/donations" class="<?php echo str_starts_with($path, "$base/donations") ? 'active' : ''; ?>">
      <i class="fas fa-hand-holding-heart"></i> Donations
    </a>
    <a href="<?php echo $base; ?>/events" class="<?php echo str_starts_with($path, "$base/events") ? 'active' : ''; ?>">
      <i class="fas fa-calendar-alt"></i> Events
    </a>
    <?php if ($isAdmin): ?>
    <a href="<?php echo $base; ?>/admin" class="<?php echo str_starts_with($path, "$base/admin") ? 'active' : ''; ?>">
      <i class="fas fa-cog"></i> Admin
    </a>
    <?php endif; ?>
    
    <!-- User Profile Section -->
    <div class="nav-divider">
      <div class="user-profile">
        <i class="fas fa-user-circle"></i>
        <div>
          <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
          <div class="user-role"><?php echo htmlspecialchars($userRole); ?></div>
        </div>
      </div>
    </div>
  </nav>
  
  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <a href="<?php echo $base; ?>/settings">
      <i class="fas fa-cog"></i> Settings
    </a>
    <a href="<?php echo $base; ?>/logout">
      <i class="fas fa-sign-out-alt"></i> Logout
    </a>
  </div>
</aside>
