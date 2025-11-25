## Waris (Inheritors) — Admin/User Page Documentation

Purpose
- This document explains the `Waris` page implementation used by users to manage their inheritors (next-of-kin). It walks a junior developer through every include, variable, SQL call, form, and rendering step so they can safely modify, extend, or refactor the page.

Files involved
- `features/users/waris/pages/waris.php` — main page entry that handles form submission (add/delete), reads records from the DB, and renders the UI.
- `features/shared/lib/auth/session.php` — session and authentication helpers used by this page (`initSecureSession()`, `requireAuth()`, `getUserId()`, `isAdmin()`).
- `features/shared/lib/database/mysqli-db.php` — provides the `$mysqli` DB connection used for prepared statements and queries.
- `features/shared/components/layouts/dashboard-layout.php` — layout that wraps the page content with sidebar and main content container.
- `features/shared/components/layouts/base.php` — base HTML shell that includes shared CSS and renders the final page.
- `features/shared/lib/utilities/functions.php` — helpers such as `e()`, `url()`, and `jsonResponse()` used across the app.
- `database/migrations` and `database/schema.sql` — where table definitions and migrations live (look here to confirm `next_of_kin` schema).

High-level flow (what happens when this page loads)
1. Compute project root and include shared helpers (session + DB).
2. Initialize a secure session and require that the user is authenticated.
3. If the request is POST, handle add or delete actions (with prepared statements).
4. Query the `next_of_kin` table for rows belonging to the current user and map column names for presentation.
5. Capture HTML (view) into `$content` via `ob_start()` / `ob_get_clean()`.
6. Wrap `$content` with the dashboard layout and render using the base layout.

Open the page file: `features/users/waris/pages/waris.php`
Below is a breakdown of the important sections and why they exist.

1) Bootstrapping and security
- `$ROOT = dirname(__DIR__, 4);`
  - Computes the repo root relative to this file. `__DIR__` is `.../features/users/waris/pages`, so walking 4 levels reaches the project root. This is used to reliably include other project files.

- `require_once $ROOT . '/features/shared/lib/auth/session.php';`
  - Loads session and auth helpers. See `features/shared/lib/auth/session.php` for implementations of `initSecureSession()`, `requireAuth()`, `getUserId()`, etc.

- `require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';`
  - Loads the mysqli connection. After this include, a `$mysqli` variable is available for DB operations.

- `initSecureSession(); requireAuth();`
  - `initSecureSession()` sets secure session flags and starts the session if not started. `requireAuth()` forces unauthenticated users to the login page. These are essential for protecting user data.

- `$userId = (int) getUserId();`
  - Retrieves the current authenticated user's ID and casts it to an integer for safe DB use.

2) Message variables
- `$message = ''; $messageClass = 'notice';`
  - Used to show user feedback (errors or success). `$messageClass` contains CSS classes (for example, `notice success`) and is echoed into the view if `$message` is set.

3) POST handling: add and delete
- The script checks `if ($_SERVER['REQUEST_METHOD'] === 'POST')` then inspects `$_POST['action']`.

- Add action (`action === 'add'`):
  - Count existing waris for this user to enforce a 3-item limit.
    - Prepared statement: `SELECT COUNT(*) AS c FROM next_of_kin WHERE user_id=?`.
    - If count >= 3, set message: "You can only add up to 3 waris." This enforces a business rule in the application layer.
  - If count < 3, read input fields from `$_POST`: `name`, `email`, `no_telefon`, `alamat` and `trim()` them.
  - Validate required fields: `name` is required; if empty, set message accordingly.
  - Insert the new next_of_kin record using a prepared statement:
    - `INSERT INTO next_of_kin (user_id, name, email, phone_number, address) VALUES (?, ?, ?, ?, ?)`
    - Bind parameters with `bind_param('issss', $userId, $name, $email, $no_telefon, $alamat)` and execute.
    - On failure, show the prepared statement error or the mysqli error safely using `htmlspecialchars()`.

- Delete action (`action === 'delete'`):
  - Read `id` from POST (cast to `(int)`), and run a prepared `DELETE FROM next_of_kin WHERE id=? AND user_id=?` to ensure users can only delete their own records.
  - Use prepared statement parameter binding `bind_param('ii', $id, $userId)` and execute. On success set a success message.

Why prepared statements
- Prepared statements avoid SQL injection by separating SQL structure from user data. This file correctly uses `prepare()` and `bind_param()` for all SQL mutations.

4) Fetching waris for display
- The code prepares and executes `SELECT id, name, email, phone_number, address FROM next_of_kin WHERE user_id=? ORDER BY id DESC` and iterates result rows with `fetch_assoc()`.
- Mapping: the script maps DB column names to the view's expected keys:
  - `no_telefon` <- `phone_number`
  - `alamat` <- `address`
  - This mapping preserves compatibility with older view templates or naming conventions.

5) View / HTML capture
- The page uses PHP output buffering to build the inner content:
  - `ob_start();` then a block of HTML with embedded PHP echos (form, table, messages) and `ob_get_clean()` to set `$content`.
- Key UI elements within the content:
  - Heading `Waris (Inheritors)`.
  - Feedback message area: printed only when `$message` is non-empty: `<div class="{$messageClass}">{$message}</div>`.
  - Add Waris form (POST): hidden `action=add`, input fields for `name` (required), `email`, `no_telefon`, `alamat`. Submit button labeled "Add".
  - The list of existing waris is rendered as a `<table>` with columns Name, Email, No Telefon, Alamat, Action.
  - Each row includes an inline delete form with `action=delete` and hidden `id` set to the waris id. The delete form uses `onsubmit="return confirm('Delete this waris?');"` for a JS confirmation.

6) Layout and final rendering
- After `$content` is prepared, the page captures the dashboard layout via:
  - `ob_start(); include $ROOT . '/features/shared/components/layouts/dashboard-layout.php'; $content = ob_get_clean();`
  - `dashboard-layout.php` wraps the `$content` variable into the sidebar and main area. It also includes session init (idempotent) and uses `isAdmin()` to render admin links when applicable.
- Finally, `$pageTitle = 'Waris'; include $ROOT . '/features/shared/components/layouts/base.php';` renders the complete HTML document with shared CSS and prints `$content`.

Security notes & best practices
- Always escape output printed into HTML: this file uses `htmlspecialchars()` for table cell output and for error strings.
- Input validation: the page trims inputs and requires `name`. For more robust validation, consider using stricter validation (length checks, email format checks) and server-side sanitization.
- CSRF protection: this page does not include CSRF tokens in its forms. To harden it, integrate a CSRF token generator/validator (there is a `csrf.php` utility under `features/shared/lib/utilities/csrf.php` in this repo if present — if not, add one) and include a hidden token field in every POST form and validate it on handling.
- Rate/limit: the 3-item limit is enforced in the application code. Consider adding DB constraints or additional business rules if needed.
- Authorization checks: delete uses `WHERE id=? AND user_id=?` to ensure a user cannot delete another user's records.

Database expectations
- Table: `next_of_kin` (columns used here):
  - `id` (primary key)
  - `user_id` (foreign key to users table)
  - `name` (string)
  - `email` (string or nullable)
  - `phone_number` (string)
  - `address` (string)
  - `created_at` / `updated_at` (optional timestamps depending on migrations)
- Confirm actual column names and types in `database/schema.sql` or migration files in `database/migrations` before making schema changes.

Where to change UI/behavior
- Add fields to the form (e.g. relationship, priority) — update both the HTML in the `ob_start()` block and the POST `INSERT` prepared statement binding parameters.
- Move inline HTML to a view file:
  - Create `features/users/waris/views/manage-waris.php` and `include` it from `pages/waris.php` (replace the hard-coded HTML). This separates presentation from page wiring.
- Add page-specific CSS:
  - Create `features/users/waris/assets/waris-admin.css` and set `$additionalStyles = [ url('features/users/waris/assets/waris-admin.css') ];` before including `base.php`.
- Add JS for client-side validation or enhanced UI: place under `features/users/waris/assets/waris-admin.js` and include with `$additionalScripts` in the page.

Refactoring suggestions for maintainability
- Extract DB work into a model class: `features/users/waris/shared/NextOfKinModel.php` with methods `countByUser($userId)`, `create($userId, $data)`, `delete($id, $userId)`, `listByUser($userId)`.
- Add a controller class `features/users/waris/controllers/WarisController.php` that handles request routing and keeps `pages/waris.php` minimal.
- Add unit/integration tests around DB code and controller logic where possible.

Troubleshooting common issues
- Form submissions not working: check that `method="post"` is present and that PHP can access `$_POST`. Also ensure `action` fields are set correctly.
- Delete not working: ensure `id` is passed and the prepared statement binds two ints `id` and `userId`. Also check DB constraints.
- No waris displayed: ensure your user has `user_id` matching `getUserId()` into `next_of_kin` rows.

Related files & quick links
- Page: `features/users/waris/pages/waris.php`
- Routes: `features/shared/lib/routes.php` (search for `/waris` to see route mappings)
- Layouts: `features/shared/components/layouts/dashboard-layout.php`, `features/shared/components/layouts/base.php`
- Session/auth: `features/shared/lib/auth/session.php`
- DB helper: `features/shared/lib/database/mysqli-db.php`
- Shared utilities: `features/shared/lib/utilities/functions.php`
- Migrations/schema: `database/migrations/` and `database/schema.sql`

Practical checklist for a junior dev
- [ ] Add CSRF protection to all POST forms.
- [ ] Extract HTML to `views/manage-waris.php` and include from `pages/waris.php` for clarity.
- [ ] Add server-side validation for `email` and `phone_number` formats.
- [ ] Add tests for DB operations and the 3-waris limit.
- [ ] Move DB operations into a `NextOfKinModel` and create a `WarisController` to handle requests.

If you'd like, I can:
- Extract the inline HTML into a new view file and update `pages/waris.php` to include it (small refactor).
- Add a sample `waris-admin.css` and show how to include it with `$additionalStyles`.
- Scaffold a `NextOfKinModel.php` and a `WarisController.php` with safe example methods.

End of document.
