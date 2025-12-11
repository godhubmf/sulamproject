# Dashboard - User Page Documentation

This document explains every part of the User Dashboard implementation so a junior developer can confidently read, modify, and extend it. It describes the PHP controller, the view, the CSS, the layout files used by the page, and shared utilities. It also explains where to add styles or change behavior and lists related files you may need to edit.

> Location: `context-docs/contexts/dashboard-user-doc.md`

---

**Quick overview**
- **Purpose**: the user dashboard presents user-specific quick actions (profile, donations preview, upcoming events) and uses shared layout components so it matches the app shell.
- **Main entry point (controller)**: `features/dashboard/admin/controllers/DashboardController.php` → method `showUserDashboard()`.
- **View (content block)**: `features/dashboard/user/views/user-overview.php` — this file contains the HTML/PHP markup for the content that appears inside the dashboard shell.
- **User CSS (page-specific)**: `features/dashboard/user/assets/user-dashboard.css` — page-specific style overrides.
- **Layout wrappers**: `features/shared/components/layouts/app-layout.php` (shell with sidebar + header) and `features/shared/components/layouts/base.php` (HTML head + site assets).
- **Helpers & auth**: `features/shared/lib/utilities/functions.php` and `features/shared/lib/auth/session.php`.

---

**Files involved (paths)**
- `features/dashboard/admin/controllers/DashboardController.php` — controller class with `showUserDashboard()`
- `features/dashboard/user/views/user-overview.php` — user dashboard content
- `features/dashboard/user/assets/user-dashboard.css` — user-specific CSS
- `features/shared/components/layouts/app-layout.php` — dashboard shell (sidebar, header, main)
- `features/shared/components/layouts/base.php` — base HTML document (head, global CSS, scripts)
- `features/shared/lib/utilities/functions.php` — helpers like `url()`, `e()`, etc.
- `features/shared/lib/auth/session.php` — session and auth helpers (`requireAuth()`, `isAuthenticated()`, `initSecureSession()`, etc.)

---

**Controller: `features/dashboard/admin/controllers/DashboardController.php`**

This controller file contains both admin and user methods. The user dashboard flow is in `showUserDashboard()`; here's a step-by-step explanation of that method.

- require / extend:
  - `require_once __DIR__ . '/../../../shared/controllers/BaseController.php';`
    - This pulls in `BaseController` with shared controller helpers. The `DashboardController` extends `BaseController` so it inherits common helpers like response composition.

- Access check and session data:
  - `$this->requireAuth();`
    - Calls a shared helper that ensures the request is authenticated. If not authenticated it redirects to login (see `features/shared/lib/auth/session.php`).
  - `$username = $_SESSION['username'] ?? 'User';`
    - Reads the display name from the active session; if absent it uses the fallback `'User'`.

- `pageHeader` array (UI metadata):
  - `$pageHeader = [ 'title' => 'Dashboard', 'subtitle' => 'Hi, ' . $username, 'breadcrumb' => [['label' => 'Home', 'url' => null]] ];`
  - This array is conventionally used by shared header components to display page title and breadcrumb; `app-layout.php` or header snippet may read this.

- Render the view content into a variable:
  - `ob_start(); include __DIR__ . '/../../user/views/user-overview.php'; $content = ob_get_clean();`
  - This captures the output of `user-overview.php` into `$content` so the layout wrappers can insert it into the shell. Avoid echoing directly from the controller — this pattern keeps layout composition predictable.

- Wrap content in the app layout shell:
  - `ob_start(); include __DIR__ . '/../../../shared/components/layouts/app-layout.php'; $content = ob_get_clean();`
  - `app-layout.php` expects `$content` to contain the page content and will include sidebar/header and place `$content` in the `<main>` area.

- Prepare page-level assets and include the base layout:
  - `$pageTitle = 'Dashboard';`
  - `$additionalStyles = [ url('features/dashboard/user/assets/user-dashboard.css') ];`
    - `url()` is a helper that resolves paths relative to the app base; use it so paths work if the app is installed under a subfolder.
  - `include __DIR__ . '/../../../shared/components/layouts/base.php';`
    - `base.php` prints the full HTML document, reads `$pageTitle`, `$additionalStyles`, `$additionalScripts`, and echoes `$content` inside the `<body>`.

Notes and things to edit safely:
- To change who can see the page: modify `requireAuth()` call (not recommended). Instead, update session role logic in `features/shared/lib/auth/session.php`.
- To change the included CSS: edit `$additionalStyles` in the controller to point to a different file or add additional entries.

---

**View: `features/dashboard/user/views/user-overview.php` — line-by-line explanation**

This file contains the markup that becomes the dashboard page body for regular users. It's included inside the app shell, so it should only contain content-level markup (no `<html>`, `<head>`, or `body` tags).

Key blocks:

1) Top quick action card

```php
<div class="card page-card">
    <section class="dashboard-cards">
        <a class="card dashboard-card card--elevated" href="<?php echo url('profile'); ?>">
            <i class="fa-solid fa-user-edit icon" aria-hidden="true"></i>
            <h3>Edit Profile</h3>
            <p>Update your personal info.</p>
        </a>
    </section>
</div>
```
- `card` and `dashboard-card` are CSS utility/class names defined by shared CSS files (base layout). Add or override rules in `features/dashboard/user/assets/user-dashboard.css`.
- `href="<?php echo url('profile'); ?>"` uses `url()` helper — always prefer `url()` over hard-coded strings so the path respects the app base path.
- The icon uses Font Awesome (available via CDN in `base.php`). Use `aria-hidden` for decorative icons and ensure important elements have accessible text.

2) Donations preview block

- This block shows a featured donation item. It uses a combination of shared CSS (`card`) and inline styles for layout. Inline styles are fine for small prototypes but for maintainability, move them to `user-dashboard.css`.
- The "Show more details →" link uses `<?php echo url('donations'); ?>` — again, use `url()` for routes.

3) Events preview block

- Similar to donations preview; displays a date box and text. Move inline styles to CSS when you want consistent styling across pages.

How to change the view safely
- Add a new quick action: copy the `<a class="card dashboard-card">` block and change the `href` to `<?php echo url('your-target'); ?>`.
- Replace inline styles by creating a specific selector in `features/dashboard/user/assets/user-dashboard.css` and moving rules there. That helps with caching and keeps markup tidy.

Accessibility note
- Ensure interactive elements (links/buttons) have clear, descriptive text. If you add dynamic content, ensure keyboard focus order and ARIA roles are correct.

---

**CSS: `features/dashboard/user/assets/user-dashboard.css`**

Current file contains:

```css
/* Dashboard User Styles */

/* Dashboard-specific card coloring */
.page-card {
  background: #eef6ec; /* soft green tint for dashboard */
}
.dashboard-card {
  /* Inherits from base.css, add user-specific overrides here */
}
```

- `page-card` is used to tint the top card — change the color here.
- Add any user-specific overrides in this file. Because `base.php` includes `$additionalStyles` after the global CSS, rules here will override shared rules when selectors have equal specificity.

Suggested quick edits
- Move the inline layout styles from `user-overview.php` into this file with descriptive selectors (e.g., `.featured-donation`, `.featured-event`).
- Add responsive rules (media queries) here if the previews need to stack on mobile.

---

**Layouts (how composition works)**

- `features/shared/components/layouts/app-layout.php` — This file renders the sidebar, header and prints the variable `$content` where page content goes. It also handles active menu highlighting, user info in sidebar and checks like `isAuthenticated()` or `isAdmin()` when deciding which menu items to show.

- `features/shared/components/layouts/base.php` — Full HTML template. It:
  - Prints `<title><?php echo e($pageTitle ?? 'App'); ?></title>`
  - Includes site-wide CSS (base, variables, components)
  - Iterates `$additionalStyles` and prints them as `<link rel="stylesheet" href="...">`
  - Prints `$content` within the body (which is usually the output of `app-layout.php` wrapping the page view)
  - Optionally includes `$additionalScripts`

When editing layouts, be careful — changes will affect all pages that use them.

---

**Shared helpers used by the user dashboard**

- `features/shared/lib/utilities/functions.php`
  - `url($path)` — builds a URL relative to the app base. Use this everywhere instead of hard-coded `/sulamproject/...` paths.
  - `e($string)` — shortcut to `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. Always escape user-controlled output.
  - `redirect($url)` — helper for redirects.

- `features/shared/lib/auth/session.php`
  - `initSecureSession()` — sets session cookie parameters and starts session securely.
  - `isAuthenticated()` — checks if a session user exists.
  - `requireAuth()` — redirect to login when not authenticated (used by `showUserDashboard()`)
  - `getUserRole()` / `isAdmin()` — helpers for role checks.

---

**Related files you may need to edit**
- Add or change the dashboard route (where the controller is invoked): wherever routing is handled (top-level pages or simple index routing). Search for `DashboardController::showUserDashboard` usage or the route that maps `/dashboard`.
- Shared CSS: `features/shared/assets/css/base.css`, `features/shared/assets/css/cards.css` — these define common components used by `.card` and `.dashboard-card`.
- Font assets or CDN: `features/shared/components/layouts/base.php` — includes Font Awesome and other HEAD resources.

---

**Practical tasks & examples**

1) Move inline styles from `user-overview.php` to CSS
- Edit `features/dashboard/user/views/user-overview.php` and remove inline `style="..."` attributes.
- Add the corresponding selectors to `features/dashboard/user/assets/user-dashboard.css`:

```css
.featured-donation { max-width: 980px; margin: 2rem auto; }
.featured-donation .thumbnail { width:120px; height:120px; background: #f3f4f6; ... }
```

2) Add a new quick action card
- Duplicate the `<a class="card dashboard-card ...">` block and set `href="<?php echo url('new-route'); ?>"`.
- Add icon class and text.

3) Add page JS
- Create `features/dashboard/user/assets/user-dashboard.js` and then in the controller set:
  - `$additionalScripts = [ url('features/dashboard/user/assets/user-dashboard.js') ];`
  - `base.php` will print script tags for `$additionalScripts`.

---

**Security & best practices**
- Always use `url()` and `e()` helpers for building links and escaping text.
- Do not trust client-side input. Validate or sanitize server-side in relevant controllers or shared request handling code.
- Prefer CSS files over inline styles for maintainability.
- Keep layout changes minimal; if you need to change the shell (sidebar/header) make small, well-tested edits.

---

**Where to look when something breaks**
- Blank page or PHP error: check webserver logs and enable error display in development. Look for include path issues (incorrect `url()` results or wrong relative `require_once` paths).
- CSS not applied: verify `user-dashboard.css` path in `$additionalStyles` and ensure `base.php` prints it; clear browser cache.
- Links not working: inspect generated HTML and ensure `url()` returns expected path (if paths are wrong, define an `APP_BASE_PATH` constant in a shared bootstrap or update `url()` implementation).

---

If you'd like, I can also:
- Move the inline styles in `user-overview.php` into `user-dashboard.css` now and commit the change.
- Add a `user-dashboard.js` stub and show how to include it.

End of Dashboard (User) documentation.
