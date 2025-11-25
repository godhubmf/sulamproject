<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { http_response_code(403); echo 'Forbidden'; exit; }

// Simple list of users with edit links
$users = [];
$res = $mysqli->query("SELECT id, name, username, email, roles, is_meninggal FROM users ORDER BY id DESC");
if ($res) { while ($row = $res->fetch_assoc()) { $users[] = $row; } $res->close(); }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin — Users</title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
</head>
<body>
  <div class="dashboard">
    <?php $currentPage='admin.php'; include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="content">
      <div class="small-card" style="max-width:1100px;margin:0 auto;">
        <h2>Manage Users</h2>
        <table class="table">
          <thead>
            <tr>
              <th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Deceased?</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?php echo (int)$u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['name']); ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['roles']); ?></td>
                <td><?php echo $u['is_meninggal'] ? 'Yes' : 'No'; ?></td>
                <td><a class="btn" href="user_edit.php?id=<?php echo (int)$u['id']; ?>">Edit</a></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
