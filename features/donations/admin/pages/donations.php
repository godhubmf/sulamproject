<?php
// Moved from /donations.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
initSecureSession();
requireAuth();
$isAdmin = isAdmin();
// Initialize Controller
require_once $ROOT . '/features/donations/admin/controllers/DonationsController.php';
$controller = new DonationsController($mysqli, $ROOT);

// Handle Actions
$message = '';
$messageClass = '';

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';
    if ($action === 'create') {
        $result = $controller->handleCreate();
    } elseif ($action === 'update' && isset($_POST['id'])) {
        $result = $controller->handleUpdate((int)$_POST['id']);
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $result = $controller->handleDelete((int)$_POST['id']);
    } else {
        $result = ['message' => 'Invalid action.', 'messageClass' => 'notice error'];
    }
    $message = $result['message'];
    $messageClass = $result['messageClass'];
}

// Fetch Data
$items = $controller->getAllDonations();

// Define page header
$pageHeader = [
    'title' => 'Donations Management',
    'subtitle' => 'Create and manage donation causes for the community.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Donations', 'url' => null],
    ],
    'actions' => [
        ['label' => 'View Reports', 'icon' => 'fa-chart-line', 'url' => url('features/reports/admin/pages/donations-report.php'), 'class' => 'btn-secondary'],
    ]
];

// 1. Capture the Split Content
/* --- LEFT COLUMN: Message & Create Form --- */
ob_start();
?>
  <?php if ($message): ?>
    <div class="<?php echo $messageClass; ?>" style="margin-bottom: 1rem; padding: 1rem; border-radius: 8px; background: <?php echo strpos($messageClass, 'success') !== false ? '#d1fae5' : '#fee2e2'; ?>; color: <?php echo strpos($messageClass, 'success') !== false ? '#065f46' : '#991b1b'; ?>;">
        <?php echo $message; ?>
    </div>
  <?php endif; ?>

  <?php if ($isAdmin): ?>
  <div class="card create-card">
    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; color: #374151;">Create New Donation Cause</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" value="create">
      <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-input" required placeholder="e.g. Mosque Fund, Orphanage Support">
        </div>
        
        <div class="form-group">
            <label class="form-label">Status</label>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="is_active" value="1" checked id="isActive">
                <label for="isActive" style="font-size: 0.95rem; color: #374151;">Active (Visible to public)</label>
            </div>
        </div>

        <div class="form-group full-width">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" placeholder="Describe what this donation is for..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">QR Code Image</label>
            <div class="file-upload-wrapper">
                <input type="file" name="gamba" accept="image/*" style="width: 100%;">
                <small style="display: block; margin-top: 0.5rem; color: #6b7280;">Recommended: Square PNG/JPG</small>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Or Image URL</label>
            <input type="url" name="gamba_url" class="form-input" placeholder="https://example.com/qr-code.png">
        </div>
      </div>

      <div class="actions" style="text-align: right;">
        <button class="btn-primary" type="submit">Publish Donation</button>
      </div>
    </form>
  </div>
  <?php endif; ?>
<?php
$splitLayoutLeft = ob_get_clean();

/* --- RIGHT COLUMN: Existing Donations List --- */
ob_start();
?>
  <div class="section-header" style="margin-top: 0;">
    <h3 class="section-title">Existing Donations</h3>
  </div>

  <?php if (empty($items)): ?>
    <div class="empty-state">
        <p>No donation causes have been created yet.</p>
    </div>
  <?php else: ?>
    <div class="donations-grid" style="grid-template-columns: 1fr;"> <!-- Force single column in split view -->
      <?php foreach ($items as $d): ?>
        <div class="card donation-card">
          <div class="donation-image-container">
            <?php if (!empty($d['image_path'])): ?>
                <?php 
                    $imgSrc = $d['image_path'];
                    if (!str_starts_with($imgSrc, 'http')) {
                        $imgSrc = url($imgSrc);
                    }
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="QR Code" class="donation-qr">
            <?php else: ?>
                <span style="color: #9ca3af; font-size: 0.9rem;">No QR Code</span>
            <?php endif; ?>
          </div>
          
          <div class="donation-content">
            <div class="donation-header">
                <h4 class="donation-title"><?php echo htmlspecialchars($d['title']); ?></h4>
                <span class="status-badge <?php echo $d['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $d['is_active'] ? 'Active' : 'Inactive'; ?>
                </span>
            </div>
            
            <?php if (!empty($d['description'])): ?>
                <p class="donation-desc"><?php echo nl2br(htmlspecialchars($d['description'])); ?></p>
            <?php endif; ?>
            
            <div class="donation-meta">
                Created: <?php echo date('M j, Y', strtotime($d['created_at'])); ?>
            </div>

            <?php if ($isAdmin): ?>
            <div class="donation-actions" style="margin-top: .75rem; display:flex; gap:.5rem;">
                <button class="btn-secondary" type="button" onclick="toggleEditForm(<?php echo (int)$d['id']; ?>)">Edit</button>
                <form method="post" onsubmit="return confirm('Delete this donation cause?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int)$d['id']; ?>">
                    <button class="btn-secondary" type="submit">Delete</button>
                </form>
            </div>

            <form method="post" enctype="multipart/form-data" id="edit-form-<?php echo (int)$d['id']; ?>" style="display:none; margin-top: .75rem;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo (int)$d['id']; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-input" value="<?php echo htmlspecialchars($d['title']); ?>" required>
                    </div>
                    
                    <div class="form-group full-width">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea"><?php echo htmlspecialchars($d['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Replace QR Code</label>
                        <input type="file" name="gamba" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Or Image URL</label>
                        <input type="url" name="gamba_url" class="form-input" placeholder="https://...">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Status</label>
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="is_active" value="1" id="isActive-<?php echo (int)$d['id']; ?>" <?php echo $d['is_active'] ? 'checked' : ''; ?>>
                            <label for="isActive-<?php echo (int)$d['id']; ?>">Active</label>
                        </div>
                    </div>
                </div>
                <div class="actions" style="text-align:right;">
                    <button class="btn-primary" type="submit">Save Changes</button>
                </div>
            </form>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php
$splitLayoutRight = ob_get_clean();

// 2. Wrap with split layout
ob_start();
include $ROOT . '/features/shared/components/layouts/split-content-layout.php';
$content = ob_get_clean();

// 3. Wrap with dashboard layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 4. Render with base layout
$pageTitle = 'Donations';
$additionalStyles = [
    url('features/shared/assets/css/split-content-layout.css'),
    url('features/donations/admin/assets/donations-admin.css')
];
include $ROOT . '/features/shared/components/layouts/base.php';
?>
<script>
function toggleEditForm(id){
    var f = document.getElementById('edit-form-' + id);
    if(!f) return;
    f.style.display = (f.style.display === 'none' || f.style.display === '') ? 'block' : 'none';
}
</script>
