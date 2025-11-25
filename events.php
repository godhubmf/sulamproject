<?php
session_start();
require_once __DIR__ . '/db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$message = '';
$messageClass = 'notice';

// Handle create (admin only)
if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $desc = trim($_POST['description'] ?? '');
  $gamba = null;
  // Handle file upload if provided
  if (!empty($_FILES['gamba']['name'])) {
    $uploadDir = __DIR__ . '/assets/uploads';
    if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }
    $ext = pathinfo($_FILES['gamba']['name'], PATHINFO_EXTENSION);
    $basename = 'event_' . time() . '_' . bin2hex(random_bytes(4)) . ($ext?'.'.preg_replace('/[^a-zA-Z0-9]+/','',$ext):'');
    $target = $uploadDir . '/' . $basename;
    if (move_uploaded_file($_FILES['gamba']['tmp_name'], $target)) {
      $gamba = 'assets/uploads/' . $basename;
    }
  } else if (isset($_POST['gamba_url']) && $_POST['gamba_url'] !== '') {
    $gamba = trim($_POST['gamba_url']);
  }
  if ($desc === '') { $message = 'Description is required.'; }
  else {
    $stmt = $mysqli->prepare('INSERT INTO events (description, gamba) VALUES (?, ?)');
    if ($stmt) { $stmt->bind_param('ss', $desc, $gamba); $stmt->execute(); $stmt->close(); $message='Event created'; $messageClass='notice success'; }
  }
}

// List events
$events = [];
$res = $mysqli->query('SELECT id, description, gamba, created_at FROM events ORDER BY id DESC');
if ($res) { while ($row = $res->fetch_assoc()) { $events[] = $row; } $res->close(); }
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Events — SulamProject</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
  </head>
  <body>
    <div class="dashboard">
      <?php $currentPage='events.php'; include __DIR__ . '/includes/sidebar.php'; ?>
      <main class="content">
        <div class="small-card" style="max-width:980px;margin:0 auto;">
          <h2>Events</h2>
          <?php if ($message): ?><div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div><?php endif; ?>

          <?php if ($isAdmin): ?>
          <h3>Create Event</h3>
          <form method="post" enctype="multipart/form-data">
            <label>Description
              <textarea name="description" rows="3" required></textarea>
            </label>
            <div class="grid-2">
              <label>Gamba (upload)
                <input type="file" name="gamba" accept="image/*">
              </label>
              <label>or Gamba URL
                <input type="url" name="gamba_url" placeholder="https://...">
              </label>
            </div>
            <div class="actions">
              <button class="btn" type="submit">Publish</button>
            </div>
          </form>
          <?php endif; ?>

          <h3 style="margin-top:1.5rem;">Latest</h3>
          <?php if (empty($events)): ?>
            <p>No events yet.</p>
          <?php else: ?>
            <div class="cards">
              <?php foreach ($events as $e): ?>
                <div class="card">
                  <?php if (!empty($e['gamba'])): ?><img src="<?php echo htmlspecialchars($e['gamba']); ?>" alt="Event image" style="max-width:100%;height:auto;"><?php endif; ?>
                  <p><?php echo nl2br(htmlspecialchars($e['description'])); ?></p>
                  <small>Posted: <?php echo htmlspecialchars($e['created_at']); ?></small>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </body>
</html>
