<?php
/**
 * Page Header Component
 * 
 * Renders a consistent page header with breadcrumb, title, subtitle, and action buttons.
 * 
 * Expected $pageHeader structure:
 * [
 *   'title' => 'Page Title',
 *   'subtitle' => 'Optional subtitle or description',
 *   'breadcrumb' => [
 *     ['label' => 'Home', 'url' => '/'],
 *     ['label' => 'Current Page', 'url' => null], // last item becomes active
 *   ],
 *   'actions' => [
 *     ['label' => 'New Entry', 'icon' => 'fa-plus', 'url' => '...', 'class' => 'btn-primary'],
 *   ]
 * ]
 */

// Fallback to $pageTitle if $pageHeader is not defined
if (!isset($pageHeader)) {
    $pageHeader = [];
}

// Extract and sanitize components
$title = isset($pageHeader['title']) ? htmlspecialchars($pageHeader['title'], ENT_QUOTES, 'UTF-8') 
         : (isset($pageTitle) ? htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') : 'Dashboard');

$subtitle = isset($pageHeader['subtitle']) ? htmlspecialchars($pageHeader['subtitle'], ENT_QUOTES, 'UTF-8') : '';

$breadcrumb = isset($pageHeader['breadcrumb']) && is_array($pageHeader['breadcrumb']) 
              ? $pageHeader['breadcrumb'] 
              : [['label' => 'Home', 'url' => '/'], ['label' => $title, 'url' => null]];

$actions = isset($pageHeader['actions']) && is_array($pageHeader['actions']) 
           ? $pageHeader['actions'] 
           : [];
?>

<div class="dashboard-header">
    <div class="header-left">
        <?php if (!empty($breadcrumb)): ?>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <?php 
                $breadcrumbCount = count($breadcrumb);
                foreach ($breadcrumb as $index => $crumb):
                    $isLast = ($index === $breadcrumbCount - 1);
                    $label = htmlspecialchars($crumb['label'], ENT_QUOTES, 'UTF-8');
                    $hasUrl = !empty($crumb['url']);
                ?>
                    <li class="breadcrumb-item <?php echo $isLast ? 'active' : ''; ?>" 
                        <?php echo $isLast ? 'aria-current="page"' : ''; ?>>
                        <?php if (!$isLast && $hasUrl): ?>
                            <a href="<?php echo htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo $label; ?>
                            </a>
                        <?php else: ?>
                            <?php echo $label; ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
        <?php endif; ?>
        
        <h1 class="page-title"><?php echo $title; ?></h1>
        
        <?php if ($subtitle): ?>
            <p class="text-muted"><?php echo $subtitle; ?></p>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($actions)): ?>
    <div class="header-actions">
        <?php foreach ($actions as $action): 
            $actionLabel = htmlspecialchars($action['label'], ENT_QUOTES, 'UTF-8');
            $actionUrl = htmlspecialchars($action['url'] ?? '#', ENT_QUOTES, 'UTF-8');
            $actionClass = htmlspecialchars($action['class'] ?? 'btn-primary', ENT_QUOTES, 'UTF-8');
            $actionIcon = isset($action['icon']) ? htmlspecialchars($action['icon'], ENT_QUOTES, 'UTF-8') : '';
        ?>
            <a href="<?php echo $actionUrl; ?>" class="btn <?php echo $actionClass; ?>">
                <?php if ($actionIcon): ?>
                    <i class="fas <?php echo $actionIcon; ?>"></i>
                <?php endif; ?>
                <?php echo $actionLabel; ?>
            </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
