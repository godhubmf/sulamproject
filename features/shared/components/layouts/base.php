<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($pageTitle ?? 'SulamProject'); ?> — SulamProject</title>
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/variables.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/base.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/typography.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/layout.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/buttons.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/cards.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/forms.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/footer.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="<?php echo url('features/shared/assets/css/responsive.css'); ?>?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <?php if (isset($additionalStyles)): ?>
        <?php foreach ($additionalStyles as $style): ?>
            <link rel="stylesheet" href="<?php echo e($style); ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <?php echo $content ?? ''; ?>
    <?php // Footer temporarily removed ?>
    
    <?php if (isset($additionalScripts)): ?>
        <?php foreach ($additionalScripts as $script): ?>
            <script src="<?php echo e($script); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
