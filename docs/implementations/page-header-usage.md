# Page Header Component Usage

## Overview
The page header component provides a consistent, styled header for all pages in the system, featuring breadcrumb navigation, page title, optional subtitle, and action buttons.

## Location
- **Component:** `features/shared/components/page-header.php`
- **Styles:** `features/shared/assets/css/layout.css` (`.dashboard-header`, `.breadcrumb`, `.page-title`, `.header-actions`)
- **Integration:** Automatically included via `features/shared/components/layouts/app-layout.php`

## How It Works
The component is automatically rendered for any page using the standard layout pattern (`app-layout.php` + `base.php`). You simply define a `$pageHeader` array before including the layout, and the component handles the rest.

## Page Header Structure

### Basic Example
```php
$pageHeader = [
    'title' => 'Donations Management',
    'subtitle' => 'Create and manage donation causes for the community.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Donations', 'url' => null], // last item becomes active
    ],
    'actions' => [
        ['label' => 'New Donation', 'icon' => 'fa-plus', 'url' => url('donations/create.php'), 'class' => 'btn-primary'],
    ]
];
```

### Array Structure

#### `$pageHeader` (array)
- **`title`** (string, optional) - Main page heading. Falls back to `$pageTitle` if not provided.
- **`subtitle`** (string, optional) - Additional context or description shown below the title.
- **`breadcrumb`** (array, optional) - Navigation breadcrumb trail.
- **`actions`** (array, optional) - Action buttons displayed in the header.

#### Breadcrumb Items (array of arrays)
Each breadcrumb item should contain:
- **`label`** (string, required) - Text to display
- **`url`** (string|null, required) - Link URL, or `null` for active (non-clickable) items

The last breadcrumb item is automatically styled as active.

#### Action Items (array of arrays)
Each action button should contain:
- **`label`** (string, required) - Button text
- **`icon`** (string, optional) - Font Awesome icon class (e.g., `'fa-plus'`, `'fa-edit'`)
- **`url`** (string, required) - Button destination URL
- **`class`** (string, optional) - Button style class. Default: `'btn-primary'`. Options: `'btn-primary'`, `'btn-secondary'`, `'btn-danger'`, etc.

## Usage Examples

### Admin Page with Multiple Actions
```php
$pageHeader = [
    'title' => 'Resident Management',
    'subtitle' => 'Manage household and individual resident records.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Residents', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Add Resident', 'icon' => 'fa-user-plus', 'url' => url('residents/add.php'), 'class' => 'btn-primary'],
        ['label' => 'Import Data', 'icon' => 'fa-file-import', 'url' => url('residents/import.php'), 'class' => 'btn-secondary'],
    ]
];
```

### User Page with Simple Header
```php
$pageHeader = [
    'title' => 'Dashboard',
    'subtitle' => 'Welcome back, ' . htmlspecialchars($username) . '.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Dashboard', 'url' => null],
    ],
];
```

### Nested Page with Deep Breadcrumb
```php
$pageHeader = [
    'title' => 'Edit Event',
    'subtitle' => 'Modify event details and schedule.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Events', 'url' => url('events')],
        ['label' => 'Edit', 'url' => null],
    ],
    'actions' => [
        ['label' => 'Save Changes', 'icon' => 'fa-save', 'url' => '#', 'class' => 'btn-primary'],
        ['label' => 'Cancel', 'icon' => 'fa-times', 'url' => url('events'), 'class' => 'btn-secondary'],
    ]
];
```

### Minimal Header (Fallback Behavior)
If `$pageHeader` is not defined, the component will render a basic header:
```php
// No $pageHeader defined - uses $pageTitle with default breadcrumb
$pageTitle = 'My Page';
// Results in: Home → My Page
```

## Integration with Layout Pattern

### Standard Layout Pattern
Most pages should follow this pattern:

```php
<?php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/utilities/functions.php';
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
requireAuth();

// Define page header BEFORE capturing content
$pageHeader = [
    'title' => 'Your Page Title',
    'subtitle' => 'Optional description.',
    'breadcrumb' => [
        ['label' => 'Home', 'url' => url('/')],
        ['label' => 'Your Page', 'url' => null],
    ],
];

// 1. Capture the inner content
ob_start();
?>
<div class="your-page-content">
    <!-- Your page-specific HTML here -->
</div>
<?php 
$content = ob_get_clean();

// 2. Wrap into app-layout (includes sidebar and page header)
ob_start();
include $ROOT . '/features/shared/components/layouts/app-layout.php';
$content = ob_get_clean();

// 3. Set page title and include base layout
$pageTitle = 'Your Page Title';
include $ROOT . '/features/shared/components/layouts/base.php';
```

## Security Considerations
- All dynamic content is automatically escaped with `htmlspecialchars()`.
- Always use the `url()` helper function for internal URLs to ensure proper path resolution.
- User-provided content in titles/subtitles should be pre-escaped before adding to `$pageHeader`.

## Accessibility
- Breadcrumb navigation uses proper `<nav>` and `aria-label="breadcrumb"`.
- The last breadcrumb item has `aria-current="page"` attribute.
- Icon-only elements should include appropriate `aria-label` attributes (handled by button classes).

## Responsive Behavior
- The header automatically adjusts for mobile devices.
- Actions may wrap to a second line on narrow screens.
- Breadcrumbs use flex-wrap for long paths.

## Styling Customization
To customize header appearance, modify these CSS classes in `features/shared/assets/css/layout.css`:
- `.dashboard-header` - Main container
- `.header-left` - Left section (breadcrumb, title, subtitle)
- `.header-actions` - Action buttons area
- `.breadcrumb` - Breadcrumb navigation
- `.page-title` - Main heading
- `.text-muted` - Subtitle styling

## Examples in the Codebase
See these files for working examples:
- `features/donations/admin/pages/donations.php`
- `features/events/admin/pages/events.php`
- `features/dashboard/user/pages/dashboard.php`
- `features/residents/admin/pages/resident-management.php`
- `features/residents/admin/pages/user-management.php`

## Troubleshooting

### Header Not Appearing
1. Ensure you're using `app-layout.php` in your page flow
2. Check that `$pageHeader` or `$pageTitle` is defined before including layouts
3. Verify no PHP errors are preventing the component from rendering

### Breadcrumb Links Not Working
1. Confirm you're using the `url()` helper function
2. Check that paths are relative to the web root
3. Verify the URL is not `null` (null URLs render as non-clickable items)

### Actions Not Displaying
1. Ensure `actions` array is properly formatted
2. Check that Font Awesome CSS is loaded (it's in `base.php`)
3. Verify button classes are valid (`btn-primary`, `btn-secondary`, etc.)

## Future Enhancements
Potential improvements for the component:
- Role-based action filtering (hide actions based on user permissions)
- Support for dropdown action menus
- Breadcrumb auto-generation from URL path
- Support for custom HTML in subtitle area
- Print-friendly version without actions
