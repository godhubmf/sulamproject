# Dashboard User PHP Documentation

Hey junior dev! Let's break down the user dashboard page step by step. This page is located at `features/dashboard/user/pages/dashboard.php`. It's the main entry point for users after they log in. I'll explain every single part of the code, including the logic, variables, and why things are done a certain way. We'll also cover the related files that are included or used.

## Overview
This file is a PHP page that renders the HTML for the user dashboard. It starts with PHP code to handle authentication and setup, then outputs an HTML document with the dashboard content. It includes a sidebar and footer for navigation and site info.

## Code Breakdown

### PHP Section at the Top
```php
<?php
// Moved from /dashboard.php
$ROOT = dirname(__DIR__, 4);
require_once $ROOT . '/features/shared/lib/auth/session.php';
initSecureSession();
requireAuth();
$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
$stylePath = $ROOT . '/assets/css/style.css';
$styleVersion = file_exists($stylePath) ? filemtime($stylePath) : time();
?>
```

- `<?php`: This starts the PHP code block. Everything inside `<?php` and `?>` is PHP code that runs on the server before sending HTML to the browser.
- `// Moved from /dashboard.php`: This is a comment explaining that this code was moved from the root dashboard.php file. Comments like this help other devs understand the history.
- `$ROOT = dirname(__DIR__, 4);`: This sets the `$ROOT` variable to the absolute path of the project root. `dirname(__DIR__, 4)` goes up 4 directories from the current file's directory. `__DIR__` is the directory of the current file. This is used to include files with absolute paths, making the code portable.
- `require_once $ROOT . '/features/shared/lib/auth/session.php';`: This includes the session.php file once. `require_once` means if it's already included elsewhere, it won't include again. This file has authentication functions. Path: `features/shared/lib/auth/session.php`.
- `initSecureSession();`: Calls the `initSecureSession()` function from session.php. This starts a secure PHP session with settings like HTTP-only cookies for security.
- `requireAuth();`: Calls `requireAuth()` from session.php. This checks if the user is logged in. If not, it redirects to the login page and stops execution.
- `$username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';`: Sets `$username` to the username from the session, but escapes it with `htmlspecialchars()` to prevent XSS attacks. If not set, defaults to 'User'. `$_SESSION` is PHP's session array.
- `$stylePath = $ROOT . '/assets/css/style.css';`: Sets the path to the global CSS file.
- `$styleVersion = file_exists($stylePath) ? filemtime($stylePath) : time();`: Sets a version number for the CSS file based on its last modification time. This forces browsers to reload the CSS if it changes, preventing caching issues.

### HTML Section
```html
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — SulamProject</title>
  <link rel="stylesheet" href="/sulamproject/assets/css/style.css?v=<?php echo $styleVersion; ?>">
  </head>
```

- `<!doctype html>`: Declares this as an HTML5 document.
- `<html lang="en">`: The root element, language set to English.
- `<head>`: Contains meta info.
  - `<meta charset="utf-8">`: Sets character encoding to UTF-8.
  - `<meta name="viewport" content="width=device-width, initial-scale=1">`: Makes the page responsive on mobile devices.
  - `<title>Dashboard — SulamProject</title>`: Page title shown in browser tab.
  - `<link rel="stylesheet" href="/sulamproject/assets/css/style.css?v=<?php echo $styleVersion; ?>">`: Links the global CSS file with a version query string to avoid caching.

### Body Section
```html
  <body>
    <div class="dashboard">
  <?php $currentPage='dashboard.php'; include $ROOT . '/features/shared/components/sidebar.php'; ?>

      <main class="content">
        <div class="small-card" style="max-width:980px;margin:0 auto;padding:1.2rem 1.4rem;">
          <div class="dashboard-header">
            <h2 style="margin:0">Welcome</h2>
            <div>Hi, <strong><?php echo $username; ?></strong></div>
          </div>

          <section class="dashboard-cards">
            <a class="dashboard-card" href="/sulamproject/residents">
              <i class="fa-solid fa-users icon" aria-hidden="true"></i>
              <h3>Residents</h3>
              <p>Manage residents and households.</p>
            </a>
            <a class="dashboard-card" href="/sulamproject/donations">
              <i class="fa-solid fa-coins icon" aria-hidden="true"></i>
              <h3>Donations</h3>
              <p>Track donations and receipts.</p>
            </a>
            <a class="dashboard-card" href="/sulamproject/events">
              <i class="fa-solid fa-calendar-days icon" aria-hidden="true"></i>
              <h3>Events</h3>
              <p>Plan and manage events.</p>
            </a>
          </section>
        </div>
      </main>
    </div>
      <?php include $ROOT . '/features/shared/components/footer.php'; ?>
```

- `<body>`: The main content of the page.
- `<div class="dashboard">`: A container div with class 'dashboard' for styling.
- `<?php $currentPage='dashboard.php'; include $ROOT . '/features/shared/components/sidebar.php'; ?>`: Sets `$currentPage` to 'dashboard.php' (used in sidebar for active link), then includes the sidebar component. Path: `features/shared/components/sidebar.php`.
- `<main class="content">`: The main content area.
  - `<div class="small-card" ...>`: A card container with inline styles for max-width, centering, padding.
    - `<div class="dashboard-header">`: Header section.
      - `<h2>Welcome</h2>`: Heading.
      - `<div>Hi, <strong><?php echo $username; ?></strong></div>`: Greets the user with their username, safely echoed.
    - `<section class="dashboard-cards">`: Section for dashboard cards.
      - Three `<a>` links with class 'dashboard-card', each linking to a feature page (residents, donations, events).
        - Each has an icon (FontAwesome), heading, and description.
- `<?php include $ROOT . '/features/shared/components/footer.php'; ?>`: Includes the footer. Path: `features/shared/components/footer.php`.

## Related Files

### session.php (`features/shared/lib/auth/session.php`)
This file handles session security and authentication.

- `initSecureSession()`: Initializes a secure session with HTTP-only, secure cookies, etc.
- `requireAuth()`: Checks if user is authenticated; redirects to login if not.
- `isAuthenticated()`: Returns true if user_id is set in session.
- Other functions like `isAdmin()`, but not used here.

If you need to change authentication logic, edit this file.

### sidebar.php (`features/shared/components/sidebar.php`)
Renders the navigation sidebar.

- Includes session.php again (but require_once prevents double include).
- Checks if user is admin with `isAdmin()`.
- Outputs nav links, with 'active' class if current path matches.
- Uses `$base = '/sulamprojectex';` for URLs (note: might be different in production).
- Includes logo image.

To add a new nav link, add an `<a>` tag here. To change active logic, modify the `str_starts_with` checks.

### footer.php (`features/shared/components/footer.php`)
Simple footer with contact info and copyright.

- Outputs static HTML with current year via `date('Y')`.
- No PHP logic, just presentation.

To update contact info, edit the text here.

### user-overview.php (`features/dashboard/user/views/user-overview.php`)
This file exists but is not included in dashboard.php. It has similar content but different text (e.g., "View and update" instead of "Manage"). It might be an unused view or for future use. It uses `e($username)` which is not defined here (perhaps a helper function).

If you want to use this view instead of inline HTML, replace the content div with `include $ROOT . '/features/dashboard/user/views/user-overview.php';` and pass `$username`.

## How to Modify
- **Add a new dashboard card**: Add another `<a class="dashboard-card">` in the `dashboard-cards` section.
- **Change greeting**: Edit the text in `dashboard-header`.
- **Add styles**: Edit `user-dashboard.css` for user-specific styles, or the global `style.css`.
- **Change authentication**: Modify `requireAuth()` in session.php.
- **Update sidebar**: Edit sidebar.php.
- **Add JS**: Currently no JS, but if needed, add `<script>` in head or before closing body.

This covers everything about the user dashboard page!</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\dashboard-user-doc\php.md