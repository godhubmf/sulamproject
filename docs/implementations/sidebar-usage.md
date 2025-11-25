# Sidebar Component Usage Guide

## Overview
The sidebar component has been modernized to match the styleguide design from `styleguides/layout-template.html`. It now features a cleaner, more modern structure with improved visual hierarchy and user experience.

**Location**: `features/shared/components/sidebar.php`

## Structure

The sidebar follows a clear vertical hierarchy with these sections:

### 1. Brand Section
```html
<div class="brand">
  <div class="brand-text">
    <i class="fa-solid fa-mosque"></i> masjidkamek
  </div>
  <div class="logo-container">
    <img src="/assets/uploads/masjid_logo.jpg" alt="Masjid Logo" class="sidebar-logo">
  </div>
</div>
```

- **Brand Text**: Displays the application name with an icon
- **Logo Image**: Displays the mosque logo from `assets/uploads/masjid_logo.jpg`
- Logo is responsive and styled with rounded corners and subtle border

### 2. Navigation Section
```html
<nav class="nav">
  <a href="/dashboard" class="active">
    <i class="fas fa-home"></i> Dashboard
  </a>
  <!-- More nav items... -->
</nav>
```

- Each link includes an icon (Font Awesome) followed by text
- Active state automatically determined by current page path
- Links have hover effects and visual feedback
- Active links show a white dot indicator on the right

### 3. User Profile Section
```html
<div class="nav-divider">
  <div class="user-profile">
    <i class="fas fa-user-circle"></i>
    <div>
      <div class="user-name">Username</div>
      <div class="user-role">Admin/Resident</div>
    </div>
  </div>
</div>
```

- Displays current logged-in user information
- Shows username from `$_SESSION['username']`
- Shows role (Admin or Resident) based on user's permission level
- Visually separated from navigation with a divider line

### 4. Sidebar Footer
```html
<div class="sidebar-footer">
  <a href="/settings">
    <i class="fas fa-cog"></i> Settings
  </a>
  <a href="/logout">
    <i class="fas fa-sign-out-alt"></i> Logout
  </a>
</div>
```

- Contains global actions (Settings, Logout)
- Positioned at bottom of sidebar with auto-margin
- Separated with a top border

## Active State Handling

### How It Works
The sidebar automatically determines the active navigation item by comparing the current request path with each link's href:

```php
$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';
// ...
<a href="<?php echo $base; ?>/dashboard" 
   class="<?php echo str_starts_with($path, "$base/dashboard") ? 'active' : ''; ?>">
```

### Visual Indicators
Active links receive:
- Background color: `var(--sidebar-active-bg)` (tinted green)
- Text color: `var(--sidebar-active-text)` (white)
- White dot indicator positioned on the right (via CSS `::after` pseudo-element)

### CSS Implementation
```css
.sidebar .nav a.active {
  background: var(--sidebar-active-bg);
  color: var(--sidebar-active-text);
}

.sidebar .nav a.active::after {
  content: '';
  position: absolute;
  right: 0.6rem;
  top: 50%;
  transform: translateY(-50%);
  width: 6px;
  height: 6px;
  background-color: #ffffff;
  border-radius: 50%;
}
```

## Role-Based Access Control (RBAC)

### Implementation
The sidebar uses `isAdmin()` function to conditionally show/hide navigation items:

```php
<?php if ($isAdmin): ?>
  <a href="<?php echo $base; ?>/users">
    <i class="fas fa-users"></i> Users
  </a>
  <a href="<?php echo $base; ?>/waris">
    <i class="fas fa-user-friends"></i> Waris
  </a>
<?php endif; ?>
```

### Admin-Only Items
These navigation items are only visible to administrators:
- **Users** - User management
- **Waris** - Waris/beneficiary management  
- **Admin** - Admin panel/settings

### Common Items
Available to all authenticated users:
- **Dashboard** - User's main dashboard
- **Donations** - Donations viewing/management
- **Events** - Events calendar and announcements

## Adding New Navigation Items

### Basic Link
```php
<a href="<?php echo $base; ?>/new-feature" 
   class="<?php echo str_starts_with($path, "$base/new-feature") ? 'active' : ''; ?>">
  <i class="fas fa-icon-name"></i> Feature Name
</a>
```

### Admin-Only Link
```php
<?php if ($isAdmin): ?>
<a href="<?php echo $base; ?>/admin-feature" 
   class="<?php echo str_starts_with($path, "$base/admin-feature") ? 'active' : ''; ?>">
  <i class="fas fa-icon-name"></i> Admin Feature
</a>
<?php endif; ?>
```

### Best Practices
1. Always include an icon using Font Awesome classes
2. Keep link text concise (1-2 words)
3. Use appropriate Font Awesome icons that represent the feature
4. Maintain consistent spacing (handled by CSS)
5. Place admin-only items within `<?php if ($isAdmin): ?>` blocks
6. Ensure the active state path matches your routing structure

## Integration with Layouts

### App Layout Integration
The sidebar is included in `features/shared/components/layouts/app-layout.php`:

```php
<div class="dashboard">
    <?php include $ROOT . '/features/shared/components/sidebar.php'; ?>
    
    <main class="content">
        <?php include $ROOT . '/features/shared/components/page-header.php'; ?>
        <?php echo $content ?? ''; ?>
    </main>
</div>
```

### Requirements
Pages using the sidebar must:
1. Include or extend `app-layout.php`, OR
2. Manually include `sidebar.php` within a `.dashboard` container
3. Ensure `features/shared/lib/auth/session.php` is loaded (handled automatically by sidebar)
4. Define `APP_BASE_PATH` constant if not using default `/sulamprojectex`

## Styling and Customization

### CSS Variables
Sidebar appearance is controlled by these CSS variables (defined in `variables.css`):

```css
--sidebar-bg: #07291f;                    /* Dark forest green background */
--sidebar-border: rgba(255,255,255,0.03); /* Subtle border */
--sidebar-link: #dfeee6;                  /* Light green for links */
--sidebar-link-hover-bg: rgba(15,138,95,0.08); /* Hover background */
--sidebar-active-bg: rgba(15,138,95,0.16);     /* Active background */
--sidebar-active-text: #ffffff;           /* White text for active items */
```

### Logo Styling
The mosque logo is styled to fit within the sidebar:

```css
.sidebar .logo-container {
  width: 100%;
  height: 130px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.sidebar .sidebar-logo {
  max-width: 90%;
  max-height: 130px;
  border-radius: 8px;
  border: 2px solid rgba(255,255,255,0.1);
  object-fit: contain;
}
```

### Layout Properties
Fixed positioning on desktop:
```css
.sidebar {
  width: 240px;
  position: fixed;
  left: 0;
  top: 0;
  height: 100vh;
  overflow: auto;
}
```

### Responsive Behavior
On screens ≤960px, the sidebar:
- Switches to static positioning (flows with document)
- Full width (auto width)
- Appears above content
- Logo placeholder height reduced
- Border changes from right to bottom

## Responsive Design

### Desktop (>960px)
- Fixed sidebar on left (240px wide)
- Content area offset with `padding-left: 240px`
- Full height with vertical scroll if needed
- All sections fully visible
- Logo displays at 130px height

### Tablet/Mobile (≤960px)
- Sidebar becomes horizontal (static position)
- Stacks above main content
- Logo container reduced to 78px height
- Logo image scales proportionally
- Brand text size reduced to 1.1rem
- Maintains all functionality and navigation items

## Font Awesome Icons

### Currently Used Icons
- `fa-solid fa-mosque` - Brand/App icon
- `fas fa-home` - Dashboard
- `fas fa-users` - Users management
- `fas fa-user-friends` - Waris/beneficiary
- `fas fa-hand-holding-heart` - Donations
- `fas fa-calendar-alt` - Events
- `fas fa-cog` - Admin/Settings
- `fas fa-user-circle` - User profile
- `fas fa-sign-out-alt` - Logout

### Adding New Icons
Browse Font Awesome 6 icons at: https://fontawesome.com/icons

Use either:
- `fas` (solid style) - Most common
- `far` (regular style) - Outlined icons
- `fab` (brands) - For brand logos

## Session Requirements

The sidebar automatically:
1. Initializes secure session via `initSecureSession()`
2. Checks authentication status
3. Retrieves user role via `isAdmin()`
4. Accesses username from `$_SESSION['username']`

### Expected Session Variables
- `$_SESSION['user_id']` - User ID (required for authentication)
- `$_SESSION['username']` - Display name (defaults to "User" if not set)
- `$_SESSION['role']` or `$_SESSION['roles']` - User role (admin/resident)

## Troubleshooting

### Active State Not Working
1. Check that `APP_BASE_PATH` is correctly defined
2. Verify request path matches link href pattern
3. Ensure `str_starts_with()` comparison is correct

### Icons Not Displaying
1. Verify Font Awesome CSS is loaded in your layout
2. Check icon class names match Font Awesome 6 syntax
3. Ensure CDN link is accessible

### Logo Not Displaying
1. Verify logo file exists at `assets/uploads/masjid_logo.jpg`
2. Check file permissions allow web server to read the image
3. Verify `APP_BASE_PATH` is correctly configured
4. Check browser console for 404 errors on image path

### User Profile Not Showing Correct Info
1. Verify `$_SESSION['username']` is set after login
2. Check that `isAdmin()` correctly reads role from session
3. Confirm session is initialized before sidebar loads

### Responsive Issues
1. Check that `responsive.css` is loaded after `layout.css`
2. Verify viewport meta tag is present in HTML head
3. Test at actual device widths, not just browser resize

## Migration Notes

### Changes from Previous Version
1. **Brand section**: Now includes styled brand-text with mosque icon
2. **Logo preserved**: Mosque logo image maintained from previous version with improved styling
3. **Icons added**: All navigation links now include Font Awesome icons
4. **User profile**: New section showing username and role
5. **Footer reorganized**: Settings and Logout moved to dedicated footer section
6. **Active indicator**: White dot now appears on active links
7. **Improved spacing**: Better visual hierarchy with consistent gaps

### Backward Compatibility
- All existing links and routing remain unchanged
- RBAC logic preserved exactly as before
- Session handling unchanged
- No breaking changes to integration points
