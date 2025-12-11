<?php
// Moved from /events.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
initSecureSession();
requireAuth();
$isAdmin = isAdmin();
// Initialize Controller
require_once $ROOT . '/features/events/admin/controllers/EventsController.php';
$controller = new EventsController($mysqli, $ROOT);

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
$events = $controller->getAllEvents();

// Define page header
$pageHeader = [
    'title' => 'Events Management',
    'subtitle' => 'Create and schedule community events and announcements.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Events', 'url' => null],
    ],
    'actions' => [
        ['label' => 'View Calendar', 'icon' => 'fa-calendar', 'url' => url('features/events/admin/pages/calendar.php'), 'class' => 'btn-secondary'],
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
  <div class="create-card">
    <h3 style="margin-bottom: 1.5rem; font-size: 1.25rem; color: #374151;">Create New Event</h3>
    <form method="post" enctype="multipart/form-data">
      
      <div class="form-grid">
        <div class="form-group">
            <label class="form-label">Event Title</label>
            <input type="text" name="title" class="form-input" required placeholder="e.g. Community Iftar">
        </div>
        
        <div class="form-group">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-input" placeholder="e.g. Main Prayer Hall">
        </div>

        <div class="form-group">
            <label class="form-label">Date</label>
            <input type="date" name="event_date" class="form-input">
        </div>

        <div class="form-group">
            <label class="form-label">Time</label>
            <input type="time" name="event_time" class="form-input">
        </div>

        <div class="form-group full-width">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" placeholder="Describe the event..."></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Event Image/Poster</label>
            <div class="file-upload-wrapper">
                <input type="file" name="gamba" accept="image/*" style="width: 100%;">
                <small style="display: block; margin-top: 0.5rem; color: #6b7280;">Recommended: Landscape PNG/JPG</small>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Or Image URL</label>
            <input type="url" name="gamba_url" class="form-input" placeholder="https://example.com/poster.png">
        </div>

        <div class="form-group full-width">
            <label class="form-label">Status</label>
            <div class="checkbox-wrapper">
                <input type="checkbox" name="is_active" value="1" checked id="isActive">
                <label for="isActive" style="font-size: 0.95rem; color: #374151;">Active (Visible to public)</label>
            </div>
        </div>
      </div>

      <div class="actions" style="text-align: right;">
        <button class="btn-primary" type="submit">Publish Event</button>
      </div>
    </form>
  </div>
  <?php endif; ?>
<?php
$splitLayoutLeft = ob_get_clean();

/* --- RIGHT COLUMN: Existing Events List --- */
ob_start();
?>
  <div class="section-header" style="margin-top: 0;">
    <h3 class="section-title">Existing Events</h3>
  </div>

  <?php if (empty($events)): ?>
    <div class="empty-state">
        <p>No events have been created yet.</p>
    </div>
  <?php else: ?>
        <div class="events-grid" style="grid-template-columns: 1fr;"> <!-- Force single column in split view -->
      <?php foreach ($events as $e): ?>
        <div class="event-card">
          <div class="event-image-container">
            <?php if (!empty($e['image_path'])): ?>
                <?php 
                    $imgSrc = $e['image_path'];
                    if (!str_starts_with($imgSrc, 'http')) {
                        $imgSrc = url($imgSrc);
                    }
                ?>
                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Event Poster" class="event-image">
            <?php else: ?>
                <div class="event-placeholder">No Image</div>
            <?php endif; ?>
          </div>
          
          <div class="event-content">
            <div class="event-header">
                <h4 class="event-title"><?php echo htmlspecialchars($e['title']); ?></h4>
                <span class="status-badge <?php echo $e['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                    <?php echo $e['is_active'] ? 'Active' : 'Inactive'; ?>
                </span>
            </div>
            
            <div class="event-details">
                <?php if (!empty($e['event_date'])): ?>
                    <div class="event-detail-item">
                        <i class="fa-regular fa-calendar"></i>
                        <span><?php echo date('M j, Y', strtotime($e['event_date'])); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($e['event_time'])): ?>
                    <div class="event-detail-item">
                        <i class="fa-regular fa-clock"></i>
                        <span><?php echo date('g:i A', strtotime($e['event_time'])); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($e['location'])): ?>
                    <div class="event-detail-item">
                        <i class="fa-solid fa-location-dot"></i>
                        <span><?php echo htmlspecialchars($e['location']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($e['description'])): ?>
                <p class="event-desc"><?php echo nl2br(htmlspecialchars($e['description'])); ?></p>
            <?php endif; ?>
            
            <div class="event-meta">
                Created: <?php echo date('M j, Y', strtotime($e['created_at'])); ?>
            </div>

            <?php if ($isAdmin): ?>
            <div class="event-actions" style="margin-top: .75rem; display:flex; gap:.5rem;">
                <button class="btn-secondary" type="button" onclick="toggleEditForm(<?php echo (int)$e['id']; ?>)">Edit</button>
                <form method="post" onsubmit="return confirm('Delete this event?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>">
                    <button class="btn-secondary" type="submit">Delete</button>
                </form>
            </div>

            <form method="post" enctype="multipart/form-data" id="edit-form-<?php echo (int)$e['id']; ?>" style="display:none; margin-top: .75rem;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?php echo (int)$e['id']; ?>">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-input" value="<?php echo htmlspecialchars($e['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-input" value="<?php echo htmlspecialchars($e['location'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Date</label>
                        <input type="date" name="event_date" class="form-input" value="<?php echo htmlspecialchars($e['event_date'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Time</label>
                        <input type="time" name="event_time" class="form-input" value="<?php echo htmlspecialchars($e['event_time'] ?? ''); ?>">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea"><?php echo htmlspecialchars($e['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Replace Image</label>
                        <input type="file" name="gamba" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Or Image URL</label>
                        <input type="url" name="gamba_url" class="form-input" placeholder="https://...">
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">Status</label>
                        <div class="checkbox-wrapper">
                            <input type="checkbox" name="is_active" value="1" id="isActive-<?php echo (int)$e['id']; ?>" <?php echo $e['is_active'] ? 'checked' : ''; ?>>
                            <label for="isActive-<?php echo (int)$e['id']; ?>">Active</label>
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
$pageTitle = 'Events';
$additionalStyles = [
    url('features/shared/assets/css/split-content-layout.css'),
    url('features/events/admin/assets/events-admin.css')
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
