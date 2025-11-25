<?php
// Moved from /user_edit.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
initSecureSession();
requireAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo 'Bad Request'; exit; }

$message = '';
$messageClass = 'notice';

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $roles = isset($_POST['roles']) ? trim($_POST['roles']) : 'user';
  $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
  $address = isset($_POST['address']) ? trim($_POST['address']) : null;
  $marital_status = isset($_POST['marital_status']) ? trim($_POST['marital_status']) : null;
  $income = isset($_POST['income']) && $_POST['income'] !== '' ? $_POST['income'] : null;
  $is_deceased = isset($_POST['is_deceased']) ? 1 : 0;

  $stmt = $mysqli->prepare('UPDATE users SET roles=?, phone_number=?, address=?, marital_status=?, income=?, is_deceased=? WHERE id=?');
  if ($stmt) {
    $stmt->bind_param('sssssdi', $roles, $phone_number, $address, $marital_status, $income, $is_deceased, $id);
    if ($stmt->execute()) {
      $message = 'User updated successfully';
      $messageClass = 'notice success';
    } else {
      $message = 'Update failed: ' . htmlspecialchars($stmt->error ?: $mysqli->error);
    }
    $stmt->close();
  } else {
    $message = 'Failed to prepare update: ' . htmlspecialchars($mysqli->error);
  }

  // Optional: create death record if provided
  if ($is_deceased && (isset($_POST['date']) || isset($_POST['time']) || isset($_POST['islamic_date']))) {
    $date = isset($_POST['date']) && $_POST['date'] !== '' ? $_POST['date'] : null;
    $time = isset($_POST['time']) && $_POST['time'] !== '' ? $_POST['time'] : null;
    $islamic_date = isset($_POST['islamic_date']) && $_POST['islamic_date'] !== '' ? $_POST['islamic_date'] : null;
    $stmt2 = $mysqli->prepare('INSERT INTO deaths (user_id, time, date, islamic_date) VALUES (?, ?, ?, ?)');
    if ($stmt2) {
      $stmt2->bind_param('isss', $id, $time, $date, $islamic_date);
      $stmt2->execute();
      $stmt2->close();
    }
  }
}

// Load user
$stmt = $mysqli->prepare('SELECT id, name, username, email, roles, phone_number, address, marital_status, income, is_deceased FROM users WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();
if (!$user) { http_response_code(404); echo 'User not found'; exit; }

// 1. Capture the inner content
ob_start();
?>
<div class="small-card" style="max-width:800px;margin:0 auto;">
  <h2>Edit User: <?php echo htmlspecialchars($user['name']); ?></h2>
  <?php if ($message): ?><div class="<?php echo $messageClass; ?>"><?php echo $message; ?></div><?php endif; ?>

  <form method="post">
    <label>Role
      <select name="roles">
        <option value="user" <?php echo $user['roles']==='user'?'selected':''; ?>>user</option>
        <option value="admin" <?php echo $user['roles']==='admin'?'selected':''; ?>>admin</option>
      </select>
    </label>
    <label>Phone Number
      <input type="text" name="phone_number" value="<?php echo htmlspecialchars((string)$user['phone_number']); ?>">
    </label>
    <label>Address
      <textarea name="address" rows="3"><?php echo htmlspecialchars((string)$user['address']); ?></textarea>
    </label>
    <label>Marital Status
      <select name="marital_status">
        <?php $opts=['','single','married','divorced','widowed','others']; foreach($opts as $o): ?>
          <option value="<?php echo $o; ?>" <?php echo ($user['marital_status']===$o? 'selected':'' ); ?>><?php echo $o===''?'(none)':ucfirst($o); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label>Income (MYR)
      <input type="number" step="0.01" name="income" value="<?php echo htmlspecialchars((string)$user['income']); ?>">
    </label>
    <label>
      <input type="checkbox" name="is_deceased" <?php echo $user['is_deceased']? 'checked':''; ?>>
      Deceased
    </label>

    <fieldset>
      <legend>Death Record (optional)</legend>
      <div class="grid-3">
        <label>Date
          <input type="date" name="date">
        </label>
        <label>Time
          <input type="time" name="time">
        </label>
        <label>Islamic Date
          <input type="text" name="islamic_date" placeholder="e.g., 10 Rabiulawal 1447H">
        </label>
      </div>
    </fieldset>

    <div class="actions">
      <button class="btn" type="submit">Save</button>
      <a class="btn outline" href="<?php echo url('admin'); ?>">Back</a>
    </div>
  </form>
</div>
<?php
$content = ob_get_clean();

// 2. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Render with base layout
$pageTitle = 'Edit User #' . $user['id'];
include $ROOT . '/features/shared/components/layouts/base.php';
?>
