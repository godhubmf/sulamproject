<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Residents — SulamProject</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  </head>
  <body>
    <div class="dashboard">
      <?php $currentPage='residents.php'; include __DIR__ . '/includes/sidebar.php'; ?>
      <main class="content">
        <div class="small-card" style="max-width:980px;margin:0 auto;">
          <h2>Residents</h2>
          <p>This section is coming soon.</p>
        </div>
      </main>
    </div>
  </body>
</html>
