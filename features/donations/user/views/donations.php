<?php
// Determine webRoot for assets
$webRoot = defined('APP_BASE_PATH') ? APP_BASE_PATH : '/sulamprojectex';
$v = time();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donations — SulamProject</title>
    <link rel="stylesheet" href="<?php echo $webRoot; ?>/features/shared/assets/css/variables.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="<?php echo $webRoot; ?>/features/shared/assets/css/base.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="<?php echo $webRoot; ?>/features/shared/assets/css/layout.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="<?php echo $webRoot; ?>/features/shared/assets/css/cards.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="<?php echo $webRoot; ?>/features/shared/assets/css/footer.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="<?php echo $webRoot; ?>/features/shared/assets/css/responsive.css?v=<?php echo $v; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <div class="dashboard">
        <?php include $ROOT . '/features/shared/components/sidebar.php'; ?>
        <main class="content">
            <div class="small-card" style="max-width:980px;margin:0 auto;padding:1.2rem 1.4rem;">
                <div class="dashboard-header">
                    <h2 style="margin:0">Donations</h2>
                    <div>Hi, <strong><?php echo $username; ?></strong></div>
                </div>
                <div class="card" style="margin-top: 2rem;">
                    <h3>Scan to Donate</h3>
                    <p>Please scan the QR code below to make a donation.</p>
                    <div style="text-align: center; padding: 2rem;">
                        <div style="width: 200px; height: 200px; background: #f0f0f0; margin: 0 auto; display: flex; align-items: center; justify-content: center; border: 2px dashed #ccc;">
                            <i class="fa-solid fa-qrcode fa-4x" style="color: #ccc;"></i>
                        </div>
                        <p style="margin-top: 1rem; color: #666;">(QR Code Placeholder)</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
