<?php
// Moved from /waris.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
initSecureSession();
requireAuth();
$userId = (int) getUserId();
$message = '';
$messageClass = 'notice';

// Handle create/delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action']) && $_POST['action'] === 'add') {
    // Count existing
    $cntRes = $mysqli->prepare('SELECT COUNT(*) AS c FROM next_of_kin WHERE user_id=?');
    $cntRes->bind_param('i', $userId);
    $cntRes->execute();
    $count = $cntRes->get_result()->fetch_assoc()['c'] ?? 0;
    $cntRes->close();

    if ($count >= 3) {
      $message = 'You can only add up to 3 waris.';
    } else {
      $name = trim($_POST['name'] ?? '');
      $email = trim($_POST['email'] ?? '');
      $no_telefon = trim($_POST['no_telefon'] ?? '');
      $alamat = trim($_POST['alamat'] ?? '');
      if ($name === '') {
        $message = 'Name is required for waris.';
      } else {
        $stmt = $mysqli->prepare('INSERT INTO next_of_kin (user_id, name, email, phone_number, address) VALUES (?, ?, ?, ?, ?)');
        if ($stmt) {
          $stmt->bind_param('issss', $userId, $name, $email, $no_telefon, $alamat);
          if ($stmt->execute()) {
            $message = 'Waris added successfully';
            $messageClass = 'notice success';
          } else {
            $message = 'Failed to add waris: ' . htmlspecialchars($stmt->error ?: $mysqli->error);
          }
          $stmt->close();
        } else {
          $message = 'Failed to prepare statement: ' . htmlspecialchars($mysqli->error);
        }
      }
    }
  } elseif (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $mysqli->prepare('DELETE FROM next_of_kin WHERE id=? AND user_id=?');
    if ($stmt) { $stmt->bind_param('ii', $id, $userId); $stmt->execute(); $stmt->close(); $message='Deleted'; $messageClass='notice success'; }
  }
}

// Fetch waris
$waris = [];
$stmt = $mysqli->prepare('SELECT id, name, email, phone_number, address FROM next_of_kin WHERE user_id=? ORDER BY id DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
        // Map old column names to new ones for compatibility
        $waris[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'email' => $row['email'],
            'no_telefon' => $row['phone_number'], // Map phone_number to no_telefon
            'alamat' => $row['address'] // Map address to alamat
        ];
    }
$stmt->close();

// 1. Capture the inner content
ob_start();
?>
<div class="small-card" style="max-width:980px;margin:0 auto;">
  <h2>Waris (Inheritors)</h2>
  <?php if ($message): ?><div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div><?php endif; ?>

  <h3>Add Waris</h3>
  <form method="post">
    <input type="hidden" name="action" value="add">
    <div class="grid-2">
      <label>Name
        <input type="text" name="name" required>
      </label>
      <label>Email
        <input type="email" name="email">
      </label>
    </div>
    <div class="grid-2">
      <label>No Telefon
        <input type="text" name="no_telefon">
      </label>
      <label>Alamat
        <input type="text" name="alamat">
      </label>
    </div>
    <div class="actions">
      <button class="btn" type="submit">Add</button>
    </div>
  </form>

  <h3 style="margin-top:2rem;">Your Waris</h3>
  <?php if (empty($waris)): ?>
    <p>No waris yet.</p>
  <?php else: ?>
    <table class="table">
      <thead><tr><th>Name</th><th>Email</th><th>No Telefon</th><th>Alamat</th><th>Action</th></tr></thead>
      <tbody>
        <?php foreach ($waris as $w): ?>
          <tr>
            <td><?php echo htmlspecialchars($w['name']); ?></td>
            <td><?php echo htmlspecialchars((string)$w['email']); ?></td>
            <td><?php echo htmlspecialchars((string)$w['no_telefon']); ?></td>
            <td><?php echo htmlspecialchars((string)$w['alamat']); ?></td>
            <td>
              <form method="post" onsubmit="return confirm('Delete this waris?');" style="display:inline;">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo (int)$w['id']; ?>">
                <button class="btn outline" type="submit">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Waris';
include $ROOT . '/features/shared/components/layouts/base.php';
?>
