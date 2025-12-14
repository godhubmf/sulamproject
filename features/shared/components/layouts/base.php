<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title><?php echo e($pageTitle ?? 'SulamProject'); ?> — SulamProject</title>
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/variables.css?v=' . time()); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/base.css?v=' . time()); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (isset($additionalStyles)): ?>
        <?php foreach ($additionalStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo e($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php echo $content ?? ''; ?>
    
    <script src="<?php echo url('features/shared/assets/js/mobile-menu.js'); ?>"></script>
    <script src="<?php echo url('features/shared/assets/js/double-scrollbar.js'); ?>"></script>
    <script src="<?php echo url('features/shared/assets/js/sticky-header.js'); ?>"></script>
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo e($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
