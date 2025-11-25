<?php
/**
 * Page Header Component - Demo Page
 * 
 * This page demonstrates the page header component in action.
 * Navigate to: /features/shared/components/page-header-demo.php
 */

$ROOT = dirname(__DIR__, 3);
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();

// Example 1: Full-featured header with all options
$pageHeader = [
    'title' => 'Page Header Demo',
    'subtitle' => 'This page demonstrates the reusable page header component with breadcrumb, title, subtitle, and actions.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Components', 'url' => url('components')],
        ['label' => 'Page Header Demo', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Primary Action', 'icon' => 'fa-plus', 'url' => '#', 'class' => 'btn-primary'],
        ['label' => 'Secondary Action', 'icon' => 'fa-cog', 'url' => '#', 'class' => 'btn-secondary'],
    ]
];

// Capture content
ob_start();
?>
<div class="card">
    <h2>How It Works</h2>
    <p>The page header component is automatically rendered by <code>app-layout.php</code> when you define a <code>$pageHeader</code> array.</p>
    
    <h3>Example Usage</h3>
    <pre style="background: #f3f4f6; padding: 1rem; border-radius: 8px; overflow-x: auto;"><code>&lt;?php
$pageHeader = [
    'title' => 'My Page',
    'subtitle' => 'Optional description.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'My Page', 'url' => null],
    ],
    'actions' => [
        ['label' => 'New Item', 'icon' => 'fa-plus', 'url' => '#', 'class' => 'btn-primary'],
    ]
];
?&gt;</code></pre>

    <h3>Features</h3>
    <ul>
        <li>✅ Breadcrumb navigation with automatic active state</li>
        <li>✅ Page title and optional subtitle</li>
        <li>✅ Multiple action buttons with icons</li>
        <li>✅ Automatic XSS protection (all content escaped)</li>
        <li>✅ Accessible markup with ARIA attributes</li>
        <li>✅ Responsive design for mobile devices</li>
        <li>✅ Fallback to <code>$pageTitle</code> if <code>$pageHeader</code> not defined</li>
    </ul>

    <h3>Styling</h3>
    <p>All styles are defined in <code>features/shared/assets/css/layout.css</code> and follow the design system variables.</p>
    
    <h3>Documentation</h3>
    <p>For complete documentation, see:</p>
    <ul>
        <li><a href="<?php echo url('docs/implementations/page-header-usage.md'); ?>">Page Header Usage Guide</a></li>
        <li><a href="<?php echo url('docs/implementations/page-header-implementation-summary.md'); ?>">Implementation Summary</a></li>
    </ul>
</div>

<div class="card" style="margin-top: 2rem;">
    <h2>Component Variations</h2>
    
    <h3>Minimal Header (Title Only)</h3>
    <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
        <code>$pageHeader = ['title' => 'Simple Page'];</code>
    </div>
    
    <h3>With Subtitle</h3>
    <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
        <code>$pageHeader = ['title' => 'My Page', 'subtitle' => 'Additional context'];</code>
    </div>
    
    <h3>With Deep Breadcrumb</h3>
    <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
        <code>
            'breadcrumb' => [<br>
            &nbsp;&nbsp;['label' => 'Home', 'url' => '/'],<br>
            &nbsp;&nbsp;['label' => 'Level 1', 'url' => '/level1'],<br>
            &nbsp;&nbsp;['label' => 'Level 2', 'url' => '/level1/level2'],<br>
            &nbsp;&nbsp;['label' => 'Current', 'url' => null],<br>
            ]
        </code>
    </div>
    
    <h3>Multiple Actions</h3>
    <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
        <code>
            'actions' => [<br>
            &nbsp;&nbsp;['label' => 'Create', 'icon' => 'fa-plus', 'url' => '#', 'class' => 'btn-primary'],<br>
            &nbsp;&nbsp;['label' => 'Import', 'icon' => 'fa-upload', 'url' => '#', 'class' => 'btn-secondary'],<br>
            &nbsp;&nbsp;['label' => 'Export', 'icon' => 'fa-download', 'url' => '#', 'class' => 'btn-secondary'],<br>
            ]
        </code>
    </div>
</div>
<?php 
$content = ob_get_clean();

// Wrap with layout
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// Set title and render
$pageTitle = 'Page Header Demo';
include $ROOT . '/features/shared/components/layouts/base.php';
