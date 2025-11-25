## Dashboard - Admin Page Documentation

This document explains every part of the Admin Dashboard implementation so a junior developer can confidently read, modify, and extend it. It describes the PHP controller, the view, the CSS, and the shared layout and helper files that the page uses. It also explains where to add styles or change behavior.

Files involved
- `features/dashboard/admin/controllers/DashboardController.php` — controller that prepares data and renders the admin dashboard.
- `features/dashboard/admin/views/admin-overview.php` — the admin dashboard view (HTML/PHP snippet shown in the dashboard content area).
- `features/dashboard/admin/assets/admin-dashboard.css` — admin-specific CSS (overrides / additions).
- `features/shared/components/layouts/dashboard-layout.php` — site dashboard layout (sidebar + main content wrapper).
- `features/shared/components/layouts/base.php` — base HTML shell that includes core CSS and prints `$content`.
- `features/shared/lib/utilities/functions.php` — helpful utilities used throughout (e, url, redirect, etc.).
- `features/shared/lib/auth/session.php` — session and auth helpers used to secure pages.

How the pieces fit together (high level)
- A request to the admin dashboard should be routed to `DashboardController::showAdminDashboard()`.
- The controller enforces admin access, prepares variables, captures the view HTML via output buffering, then injects that HTML into the dashboard layout, which itself is injected into the base layout. The base layout includes global CSS and any page-specific styles.

Controller: `features/dashboard/admin/controllers/DashboardController.php`
- File purpose: orchestrates data and view composition for both admin and user dashboards.

Key points in the controller
- `require_once __DIR__ . '/../../../shared/controllers/BaseController.php';`
  - This pulls a base controller with common helpers (routing, response helpers, etc.). If you need common controller behaviour, update or extend `BaseController`.

- `public function showAdminDashboard()`
  - ` $this->requireAdmin();` — uses a helper from the shared auth/session layer to ensure the current user has admin privileges. If not, it sends a 403 and exits. See `features/shared/lib/auth/session.php`.
  - `$username = $_SESSION['username'] ?? 'Admin';` — reads the display name from session and falls back to `'Admin'`.

  - The controller uses output buffering to capture parts of the page into variables:
    - `ob_start(); include __DIR__ . '/../views/admin-overview.php'; $dashboardContent = ob_get_clean();`
      - This loads the admin view and stores its output in `$dashboardContent`.
    - `ob_start(); include __DIR__ . '/../../../shared/components/layouts/dashboard-layout.php'; $content = ob_get_clean();`
      - The dashboard layout file expects `$content` (the main page content) to be set (see details below). Because the controller captured `$dashboardContent` earlier, when `dashboard-layout.php` is included it will echo the `$content` variable into the layout's main area.

  - `$pageTitle = 'Dashboard';` — used by `base.php` for the `<title>` tag.

  - `$additionalStyles = [url('features/dashboard/admin/assets/admin-dashboard.css')];`
    - This tells `base.php` to include the admin-specific stylesheet after the site-wide styles. To add more styles, push additional paths to this array. `url()` is a helper in `features/shared/lib/utilities/functions.php`.

  - `include __DIR__ . '/../../../shared/components/layouts/base.php';`
    - Finally the base layout is included and prints the assembled page. `base.php` expects `$pageTitle`, `$additionalStyles`, and `$content` to be present.

- `public function showUserDashboard()`
  - Same flow as `showAdminDashboard()` but uses `requireAuth()` (any authenticated user) and loads the user view and styles instead. Useful to inspect as a second example.

View: `features/dashboard/admin/views/admin-overview.php`
- Purpose: the HTML snippet for the admin dashboard content area (the block that shows welcome text and dashboard cards).

Contents explained line-by-line (important parts):
- `<div class="small-card" style="max-width:980px; margin:0 auto; padding:1.2rem 1.4rem;">` — wrapper card. `small-card` and other classes are defined in shared CSS files (see `features/shared/assets/css/`), so prefer adding or overriding styles in the admin CSS file instead of inline styles.
- `<?php echo e($username); ?>` — prints the escaped username using the `e()` helper from `features/shared/lib/utilities/functions.php`. Always use `e()` to avoid XSS.
- The dashboard cards are anchor links (`<a class="dashboard-card" href="/sulamproject/residents">`) that point to app routes. If your app base is different, change the link paths or use `url()` to generate paths relative to the `APP_BASE_PATH` (see `functions.php` for details).
- Font Awesome icons are referenced (`<i class="fa-solid fa-users icon"></i>`). `base.php` already includes Font Awesome CDN, so these icons should render.

How to change the view
- To add a new dashboard card: copy an existing `<a class="dashboard-card">` block, update the `href`, icon class, heading and description.
- To change the welcome text: edit the `Welcome` header or the `Hi, <strong><?php echo e($username); ?></strong>` line.
- To change markup structure significantly, update both this file and, if needed, the CSS in `features/dashboard/admin/assets/admin-dashboard.css`.

CSS: `features/dashboard/admin/assets/admin-dashboard.css`
- Purpose: page/modular styles specific to the admin dashboard.
- Current content (placeholder):
  - The file currently contains a comment and an empty `.dashboard-card` rule. That's intentional: the project uses `features/shared/assets/css/*.css` for core styles (cards, layout, variables). Add only admin overrides here.

Where to add styles
- Use shared CSS for styling shared across multiple pages: `features/shared/assets/css/cards.css`, `layout.css`, `base.css`, etc.
- Use `features/dashboard/admin/assets/admin-dashboard.css` for admin-only overrides. Because `base.php` includes `$additionalStyles` after the shared CSS, the admin stylesheet will override shared rules when selectors match.

Shared layout: `features/shared/components/layouts/dashboard-layout.php`
- Purpose: wraps the `$content` variable with the dashboard shell (sidebar and main content area).

Important details in `dashboard-layout.php`:
- `require_once __DIR__ . '/../../lib/utilities/functions.php';` — brings in helpers like `e()` and `url()` used by layouts.
- `$ROOT` detection: sets a root path used for `require_once` includes when code executes from different directories.
- `require_once $ROOT . '/features/shared/lib/auth/session.php'; initSecureSession();` — initializes a secure session and ensures session helpers are available.
- Sidebar building:
  - `$base = '/sulamproject';` — the app base. If you deploy the app in a different base subfolder, change this base string or better, define `APP_BASE_PATH` in a centralized bootstrap and use `url()` helper instead.
  - `$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?: '';` then `str_starts_with` checks add `active` classes to the nav items.
  - `if (isAdmin()):` — menu shows an Admin link only when the user is admin. `isAdmin()` is defined in `features/shared/lib/auth/session.php`.
- The layout prints `<?php echo $content ?? ''; ?>` inside the `<main class="content">` element — this is where the captured `$dashboardContent` appears.

Base layout: `features/shared/components/layouts/base.php`
- Purpose: the HTML document shell. It includes site-wide CSS files, Font Awesome, and any `$additionalStyles` and `$additionalScripts` provided by controllers.

Key slots the controller sets
- `$pageTitle` — used for the `<title>` tag.
- `$additionalStyles` — an array of stylesheet URLs. The controller sets this to include page-specific CSS like `features/dashboard/admin/assets/admin-dashboard.css`.
- `$additionalScripts` — similar to styles, but for `<script>` tags.

Utility helpers: `features/shared/lib/utilities/functions.php`
- `e($string)` — shorthand for `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. Use for any user-generated content.
- `url($path)` — helper that prepends `APP_BASE_PATH` if defined. Use this when generating links that should respect your app base path.
- `redirect($url)` — wrapper for HTTP redirects.
- `jsonResponse($data, $statusCode)` — returns a JSON response with proper headers.

Session & Auth helpers: `features/shared/lib/auth/session.php`
- `initSecureSession()` — initializes session with recommended flags (`httponly`, `samesite`, cookie settings) and regenerates session IDs periodically. Important for session security.
- `isAuthenticated()` — true when `$_SESSION['user_id']` exists.
- `requireAuth()` — redirects to `/sulamproject/login` when not authenticated.
- `getUserId()` / `getUserRole()` — helpers to obtain user identity and normalized role.
- `isAdmin()` — convenience wrapper that returns `getUserRole() === 'admin'`.
- `requireAdmin()` — calls `requireAuth()` and also checks `isAdmin()`, otherwise returns 403.
- `destroySession()` — clears and destroys the session.

Practical editing tasks and where to make the change
- Add a new card to the Admin dashboard view:
  - Edit `features/dashboard/admin/views/admin-overview.php` — copy an existing `<a class="dashboard-card">` block and change `href`, icon, and text.
  - If you need new card styling, add it to `features/dashboard/admin/assets/admin-dashboard.css`.

- Change sidebar menu items or order:
  - Edit `features/shared/components/layouts/dashboard-layout.php` — modify or add `<a href="...">` entries.
  - Use `isAdmin()` to conditionally show admin-only links.

- Change the app base path (useful if you deploy under a different folder):
  - Set a constant `APP_BASE_PATH` in a central bootstrap (for example in `db.php` or a new `bootstrap.php`) and use `url()` helper everywhere instead of hard-coded `/sulamproject` strings.

- Add page-specific JS:
  - In the controller (e.g. `DashboardController::showAdminDashboard()`), define `$additionalScripts = [ url('features/dashboard/admin/assets/admin-dashboard.js') ];` before including `base.php`.
  - Create the JS file at `features/dashboard/admin/assets/admin-dashboard.js`.

Security notes & best practices
- Always escape output: use `e()` (in `functions.php`) when echoing user-provided values like `$username`.
- Protect routes: controllers call `requireAdmin()` / `requireAuth()` to check access.
- Avoid inline styles where possible. Prefer shared CSS or page-specific assets so caching and overrides work correctly.

Troubleshooting
- Icons not showing: check that Font Awesome CDN is reachable (included in `base.php`). If your environment blocks external CDNs, download Font Awesome locally and change the `<link>` in `base.php`.
- CSS changes not taking effect: clear browser cache or ensure query string cache-busting is present (the project appends `?v=<?php echo time(); ?>` to shared CSS links in `base.php`). If you modify admin CSS and it's not loaded, verify `additionalStyles` path is correct and `url()` returns the expected prefix.

Related files and quick links
- Controller: `features/dashboard/admin/controllers/DashboardController.php`
- Admin view: `features/dashboard/admin/views/admin-overview.php`
- Admin CSS: `features/dashboard/admin/assets/admin-dashboard.css`
- Dashboard layout: `features/shared/components/layouts/dashboard-layout.php`
- Base layout: `features/shared/components/layouts/base.php`
- Utilities: `features/shared/lib/utilities/functions.php`
- Session/auth: `features/shared/lib/auth/session.php`

If you want, I can:
- Add a small example override in `admin-dashboard.css` to demonstrate customizing a `.dashboard-card`.
- Add an `admin-dashboard.js` stub and show how to include it from the controller.

End of doc.
