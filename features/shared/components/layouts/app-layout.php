<?php
require_once __DIR__ . '/../../lib/utilities/functions.php';
?>
<?php if (!isset($ROOT)) { $ROOT = dirname(__DIR__, 4); } ?>
<?php require_once $ROOT . '/features/shared/lib/auth/session.php'; initSecureSession(); ?>
<div class="dashboard">
    <?php include $ROOT . '/features/shared/components/sidebar.php'; ?>
    
    <main class="content">
        <?php include $ROOT . '/features/shared/components/page-header.php'; ?>
        <?php echo $content ?? ''; ?>
    </main>
</div>
