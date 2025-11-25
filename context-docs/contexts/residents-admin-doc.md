## Residents — Admin Page Documentation

Purpose
- This document explains the Admin-side Residents page so a junior developer can understand every include, variable, and the rendering flow. It also points to where to add styles, JS, and where to implement the actual residents management features which are currently placeholders.

Files covered
- `features/residents/admin/pages/residents.php` — admin page entry (routable PHP page). Primary file to edit when changing the admin residents page flow.
- `features/residents/admin/views/manage-residents.php` — HTML/PHP fragment with the management UI (currently a placeholder with "Feature coming soon").
- `features/shared/components/layouts/dashboard-layout.php` — dashboard shell (sidebar + main content) that the page uses.
- `features/shared/components/layouts/base.php` — base HTML shell that includes shared CSS and optional page-specific styles.
- `features/shared/lib/auth/session.php` — session and authentication helpers used to secure this page.
- `features/shared/lib/utilities/functions.php` — general utility helpers such as `e()` and `url()`.

How rendering works (step-by-step)
1. The file `features/residents/admin/pages/residents.php` is requested by the webserver (e.g. via `/sulamproject/residents` or a router that maps to this file).
2. At the top of `residents.php` the code sets `$ROOT` and includes session helpers:
   - `$ROOT = dirname(__DIR__, 4);` computes the project root relative to the file location.
   - `require_once $ROOT . '/features/shared/lib/auth/session.php';` loads `session.php`.
   - `initSecureSession();` sets secure session flags and starts the session (if not started).
   - `requireAuth();` ensures the user is authenticated (redirects to login if not).

3. The page captures the inner content using PHP output buffering:
   - `ob_start();` then prints a small card HTML block (a placeholder) and captures it into `$content` with `ob_get_clean()`.

4. The page then wraps the content in the dashboard layout:
   - `ob_start(); include $ROOT . '/features/shared/components/layouts/dashboard-layout.php'; $content = ob_get_clean();`
   - `dashboard-layout.php` prints the sidebar and expects `$content` to be available; the controller's captured content is inserted into the `<main class="content">` element of the layout.

5. Finally the page sets `$pageTitle = 'Residents';` and includes the base layout:
   - `include $ROOT . '/features/shared/components/layouts/base.php';`
   - `base.php` prints the full HTML document, includes shared CSS files, any `$additionalStyles` (none are set here), and outputs `$content` (the full layout plus inner content).

Line-by-line explanation of `features/residents/admin/pages/residents.php`
- `<?php` — PHP open tag.
- `$ROOT = dirname(__DIR__, 4);` — compute the project root. `__DIR__` is `features/residents/admin/pages`; `dirname(__DIR__, 4)` walks up four directories to the repo root.
- `require_once $ROOT . '/features/shared/lib/auth/session.php';` — load session helpers.
- `initSecureSession();` — start/initialize secure session settings. See `features/shared/lib/auth/session.php`.
- `requireAuth();` — ensures the user is logged in. If not, user is redirected to `/sulamproject/login`.
- `ob_start();` … HTML … `$content = ob_get_clean();` — capture the inner HTML snippet into `$content`.
- `ob_start(); include $ROOT . '/features/shared/components/layouts/dashboard-layout.php'; $content = ob_get_clean();` — wrap that snippet inside the dashboard layout; the layout will access `$content` and render it into the main area.
- `$pageTitle = 'Residents';` — title used by `base.php`.
- `include $ROOT . '/features/shared/components/layouts/base.php';` — include the outer HTML document which pulls in shared assets and echoes `$content`.

Line-by-line explanation of `features/residents/admin/views/manage-residents.php`
- This view is a presentational fragment intended to be included where the dashboard content belongs. Current content:
  - A `<div class="small-card">` wrapper with a heading `Residents Management` and a short description.
  - A `.notice` block listing planned features (Register new residents, Manage household information, etc.).
  - Inline styles are used for quick placeholder layout (`max-width`, `padding`, `color: var(--muted)`). These should be replaced by shared or module CSS later.

Shared files this page relies on
- `features/shared/components/layouts/dashboard-layout.php` — builds the sidebar and main content area, and calls `initSecureSession()` again (idempotent) and uses `isAdmin()` to show admin links.
- `features/shared/components/layouts/base.php` — includes shared CSS files located at `features/shared/assets/css/*.css` and Font Awesome CDN. It also handles `$additionalStyles` and `$additionalScripts` if the controller provides them.
- `features/shared/lib/auth/session.php` — contains `initSecureSession()`, `requireAuth()`, `isAuthenticated()`, `isAdmin()`, and other helpers. This file protects the page and manages session security.
- `features/shared/lib/utilities/functions.php` — contains helpers like `e()` (escape), `url()` (build URLs), `redirect()`, etc. Use `e()` whenever outputting user-provided content.

Where to implement Residents functionality (suggested structure)
The current page is a placeholder. Implementing a real Residents admin area requires:

- Data layer (shared):
  - Create a residents model under `features/residents/shared/lib/` (e.g. `ResidentsModel.php`) to encapsulate DB queries using prepared statements and the existing DB helper at `features/shared/lib/database/mysqli-db.php` or `features/residents/shared/lib/database.php` if present.

- Business logic (controllers):
  - Add admin controller(s) under `features/residents/admin/controllers/`, e.g. `ResidentsController.php`, with methods for listing, creating, updating, and deleting residents. Use session helpers to require admin where appropriate.

- Views & Pages:
  - Replace the placeholder in `features/residents/admin/views/manage-residents.php` with actual UI components (filters, table with pagination, forms). Keep fragments small and include them from the page entry file.

- AJAX endpoints:
  - Add `features/residents/admin/ajax/` endpoints for non-blocking operations (search, pagination, create/update/delete). Return JSON responses using `jsonResponse()` from `features/shared/lib/utilities/functions.php`.

- Routes and navigation:
  - Link the admin page from the dashboard sidebar in `features/shared/components/layouts/dashboard-layout.php` (it already links to `/sulamproject/residents`). If you rename or change routes, update the `$base` variable or move to `url()` helper.

- Assets:
  - Add CSS under `features/residents/admin/assets/` (e.g. `residents-admin.css`) and include it by setting `$additionalStyles = [ url('features/residents/admin/assets/residents-admin.css') ];` before including `base.php` in the page entry file.
  - Add JS under `features/residents/admin/assets/` and include similarly via `$additionalScripts`.

Styling guidance
- Shared styling: Use `features/shared/assets/css/` for common components like cards, forms, tables and variables (colors, spacing). Module-specific overrides should go under `features/residents/admin/assets/`.
- Avoid inline styles in production. Replace the inline style attributes in the placeholder files with class names and add rules to `residents-admin.css` or shared files.

Security & best practices
- Always sanitize and escape user input and output. Use `e()` when echoing values to views.
- Use prepared statements for DB queries. If you create a new DB helper, follow the pattern used by existing code in `features/shared/lib/database/mysqli-db.php`.
- Protect endpoints with `requireAuth()` for user access and `requireAdmin()` (or `isAdmin()` checks) for admin-only operations.
- Add CSRF tokens to POST forms. The repo contains `features/shared/lib/utilities/csrf.php` (search for existing CSRF helpers) — reuse it.

Practical edits (what to change where)
- To add a module stylesheet:
  - Create `features/residents/admin/assets/residents-admin.css`.
  - In `features/residents/admin/pages/residents.php` before including `base.php` add:
    ```php
    $additionalStyles = [ url('features/residents/admin/assets/residents-admin.css') ];
    ```

- To replace placeholder content with the included view:
  - Replace the small-card HTML currently in `residents.php` with an `include` of the view fragment:
    ```php
    ob_start();
    include $ROOT . '/features/residents/admin/views/manage-residents.php';
    $content = ob_get_clean();
    ```
  - This separates page wiring (pages/residents.php) from view markup (views/manage-residents.php).

- To wire a controller:
  - Create `features/residents/admin/controllers/ResidentsController.php` with methods such as `index()`, `create()`, `store()`, `edit()`, `update()`, `delete()`.
  - In `pages/residents.php` either instantiate the controller and call `$controller->index()` or use the file as a view that controllers include.

Quick checklist to move from placeholder → working page
- [ ] Create `ResidentsModel.php` and test DB queries with prepared statements.
- [ ] Create `ResidentsController.php` and protect with `requireAuth()`/`requireAdmin()`.
- [ ] Replace inline HTML in `pages/residents.php` with `include` of `views/manage-residents.php` and use real markup.
- [ ] Add CSS/JS assets and register them via `$additionalStyles` / `$additionalScripts`.
- [ ] Implement AJAX endpoints for listing/searching and wire front-end JS.

Related files and references
- Page entry: `features/residents/admin/pages/residents.php`
- View fragment: `features/residents/admin/views/manage-residents.php`
- Dashboard layout: `features/shared/components/layouts/dashboard-layout.php`
- Base layout: `features/shared/components/layouts/base.php`
- Session/auth: `features/shared/lib/auth/session.php`
- Utilities: `features/shared/lib/utilities/functions.php`
- Shared CSS: `features/shared/assets/css/` (cards.css, layout.css, variables.css, etc.)

If you want I can:
- Replace inline HTML in `pages/residents.php` with an `include` of `views/manage-residents.php` (small refactor).
- Add an example `residents-admin.css` with a couple of overrides for `.small-card` and `.notice` to demonstrate proper styling.
- Scaffold a `ResidentsController.php` and a basic `ResidentsModel.php` with a safe sample query.

End of document.
