<?php
// Reusable sidebar template. Set $currentPage before including to mark active link.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$current = isset($currentPage) ? $currentPage : basename($_SERVER['PHP_SELF']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<aside class="sidebar">
  <div class="brand">OurMasjid</div>
  <nav class="nav">
    <a href="dashboard.php" class="<?php echo $current==='dashboard.php'?'active':''; ?>">Dashboard</a>
    <a href="residents.php" class="<?php echo $current==='residents.php'?'active':''; ?>">Residents</a>
    <a href="waris.php" class="<?php echo $current==='waris.php'?'active':''; ?>">Waris</a>
    <a href="donations.php" class="<?php echo $current==='donations.php'?'active':''; ?>">Donations</a>
    <a href="events.php" class="<?php echo $current==='events.php'?'active':''; ?>">Events</a>
    <?php if ($isAdmin): ?>
      <a href="admin.php" class="<?php echo ($current==='admin.php' || $current==='user_edit.php') ? 'active' : ''; ?>">Admin</a>
    <?php endif; ?>
    <a href="logout.php">Logout</a>
  </nav>
</aside>
