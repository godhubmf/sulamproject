<?php
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { http_response_code(403); echo 'Forbidden'; exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { http_response_code(400); echo 'Bad Request'; exit; }

$message = '';
$messageClass = 'notice';

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $roles = isset($_POST['roles']) ? trim($_POST['roles']) : 'user';
  $no_telefon = isset($_POST['no_telefon']) ? trim($_POST['no_telefon']) : null;
  $alamat = isset($_POST['alamat']) ? trim($_POST['alamat']) : null;
  $status = isset($_POST['status_perkahwinan']) ? trim($_POST['status_perkahwinan']) : null;
  $pendapatan = isset($_POST['pendapatan']) && $_POST['pendapatan'] !== '' ? $_POST['pendapatan'] : null;
  $is_meninggal = isset($_POST['is_meninggal']) ? 1 : 0;

  $stmt = $mysqli->prepare('UPDATE users SET roles=?, no_telefon=?, alamat=?, status_perkahwinan=?, pendapatan=?, is_meninggal=? WHERE id=?');
  if ($stmt) {
    $stmt->bind_param('sssssii', $roles, $no_telefon, $alamat, $status, $pendapatan, $is_meninggal, $id);
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
  if ($is_meninggal && (isset($_POST['tarikh']) || isset($_POST['time']) || isset($_POST['tarikh_islam']))) {
    $tarikh = isset($_POST['tarikh']) && $_POST['tarikh'] !== '' ? $_POST['tarikh'] : null;
    $time = isset($_POST['time']) && $_POST['time'] !== '' ? $_POST['time'] : null;
    $tarikh_islam = isset($_POST['tarikh_islam']) && $_POST['tarikh_islam'] !== '' ? $_POST['tarikh_islam'] : null;
    $stmt2 = $mysqli->prepare('INSERT INTO deaths (user_id, time, tarikh, tarikh_islam) VALUES (?, ?, ?, ?)');
    if ($stmt2) {
      $stmt2->bind_param('isss', $id, $time, $tarikh, $tarikh_islam);
      $stmt2->execute();
      $stmt2->close();
    }
  }
}

// Load user
$stmt = $mysqli->prepare('SELECT id, name, username, email, roles, no_telefon, alamat, status_perkahwinan, pendapatan, is_meninggal FROM users WHERE id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();
if (!$user) { http_response_code(404); echo 'User not found'; exit; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit User #<?php echo (int)$user['id']; ?></title>
  <link rel="stylesheet" href="assets/css/style.css?v=<?php echo filemtime(__DIR__ . '/assets/css/style.css'); ?>">
</head>
<body>
  <div class="dashboard">
    <?php $currentPage='user_edit.php'; include __DIR__ . '/includes/sidebar.php'; ?>
    <main class="content">
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
          <label>No Telefon
            <input type="text" name="no_telefon" value="<?php echo htmlspecialchars((string)$user['no_telefon']); ?>">
          </label>
          <label>Alamat
            <textarea name="alamat" rows="3"><?php echo htmlspecialchars((string)$user['alamat']); ?></textarea>
          </label>
          <label>Status Perkahwinan
            <select name="status_perkahwinan">
              <?php $opts=['','bujang','berkahwin','bercerai','duda','janda','lain-lain']; foreach($opts as $o): ?>
                <option value="<?php echo $o; ?>" <?php echo ($user['status_perkahwinan']===$o? 'selected':'' ); ?>><?php echo $o===''?'(none)':$o; ?></option>
              <?php endforeach; ?>
            </select>
          </label>
          <label>Pendapatan (MYR)
            <input type="number" step="0.01" name="pendapatan" value="<?php echo htmlspecialchars((string)$user['pendapatan']); ?>">
          </label>
          <label>
            <input type="checkbox" name="is_meninggal" <?php echo $user['is_meninggal']? 'checked':''; ?>>
            Deceased
          </label>

          <fieldset>
            <legend>Death Record (optional)</legend>
            <div class="grid-3">
              <label>Tarikh (Date)
                <input type="date" name="tarikh">
              </label>
              <label>Time
                <input type="time" name="time">
              </label>
              <label>Tarikh Islam
                <input type="text" name="tarikh_islam" placeholder="e.g., 10 Rabiulawal 1447H">
              </label>
            </div>
          </fieldset>

          <div class="actions">
            <button class="btn" type="submit">Save</button>
            <a class="btn outline" href="admin.php">Back</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
