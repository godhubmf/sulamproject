## Donations — Admin Page Documentation

Purpose
- Explain every part of the Donations admin page so a junior developer can understand the includes, request handling, file uploads, DB interactions, and where to change behavior or styles.

Files covered
- `features/donations/admin/pages/donations.php` — main admin page entry (handles form submit, uploads, DB insert, lists items).
- `features/donations/admin/views/manage-donations.php` — presentational fragment (placeholder view).
- `features/shared/lib/database/mysqli-db.php` — DB connection used by the page.
- `features/shared/components/layouts/dashboard-layout.php` — page shell (sidebar + main area).
- `features/shared/components/layouts/base.php` — base HTML shell for CSS and scripts.
- `features/shared/lib/auth/session.php` — session/auth helpers.
- `features/shared/lib/utilities/functions.php` — helpers like `url()` and `e()` used for escaping and URL building.

High-level flow
- Request lands on `donations.php`.
- Session and DB are initialized; user authentication is enforced with `requireAuth()`.
- If the user is admin and the request is a POST, the page handles creating a donation post (description + optional image upload or URL), inserting a record into `donations` table.
- The page then queries `donations` table and lists results.
- Page HTML is captured with `ob_start()` into `$content`, wrapped with dashboard layout, and rendered inside `base.php`.

Line-by-line explanation of `donations.php`
- `$ROOT = dirname(__DIR__, 4);`
  - Computes repo root. `__DIR__` is `features/donations/admin/pages`.

- `require_once $ROOT . '/features/shared/lib/auth/session.php';`
  - Loads session and auth helpers.

- `require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';`
  - Loads DB connection. The page later uses `$mysqli` to run queries.

- `initSecureSession(); requireAuth(); $isAdmin = isAdmin();`
  - Start secure session, require authentication (redirects unauthenticated users), and determine admin status.

- `$message` / `$messageClass` — variables to hold user-facing operation messages.

Handling POST (create donation)
- `if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') { ... }`
  - Only admins can create posts. The block:
    - Reads `description` from `$_POST` (trimmed) and validates it's not empty.
    - Processes `$_FILES['gamba']` upload if provided:
      - Creates `assets/uploads` directory if missing.
      - Builds a safe filename: `donation_{timestamp}_{random}.{ext}`. It sanitizes the extension.
      - Uses `move_uploaded_file()` to store the file and sets `$gamba` to `assets/uploads/{basename}` if successful.
    - Fallback: if `gamba_url` is provided (a remote URL), it uses that as `$gamba`.
    - If description is empty, set error message. Otherwise prepare MySQL insert using prepared statements:
      - `$stmt = $mysqli->prepare('INSERT INTO donations (description, image_path) VALUES (?, ?)');` then bind and execute.
      - On success set a success message.

Notes about file uploads and security
- The code accepts both uploaded files and external URLs. When accepting uploads:
  - The upload directory is under `assets/uploads` — web-accessible. In production, ensure permissions and scanning rules are secure.
  - The filename is randomized and sanitized for the extension, but the uploaded file's content type is not validated further. For security, validate MIME type or re-encode images server-side before saving.

Listing donations
- `$res = $mysqli->query('SELECT id, description, image_path, created_at FROM donations ORDER BY id DESC');` — simple query, result fetched into `$items` array.
- Each item is printed in a card. When printing image path, the code checks whether `image_path` starts with `http`:
  - If not, it runs `url($imgSrc)` to make it relative to the app base.
  - Images are printed with `htmlspecialchars()` to avoid XSS.
- Description is printed with `nl2br(htmlspecialchars(...))` to preserve newlines while escaping.

Presentation & templates
- The page builds the inner HTML and captures to `$content`.
- Then it includes `dashboard-layout.php` which expects `$content` and prints the sidebar and main area.
- Finally `base.php` is included; it includes shared CSS and the full HTML shell.

Where to change things
- Change DB schema or fields: update DB migration files in `database/migrations`.
- Change upload folder: modify the `$uploadDir` variable in the POST handling block.
- Add validation: add server-side checks for allowed file extensions and MIME types before `move_uploaded_file()`.
- Add CSRF protection: integrate CSRF token generation and verification (look for `features/shared/lib/utilities/csrf.php` or create one).
- Move logic to a controller: extract POST handling and listing to `features/donations/admin/controllers/DonationsController.php` for cleaner separation.

Related views and assets
- Presentational fragment: `features/donations/admin/views/manage-donations.php` (placeholder).
- Add CSS under `features/donations/admin/assets/` and include via `$additionalStyles = [ url('features/donations/admin/assets/donations-admin.css') ];` before including `base.php`.

DB table expectations
- The code expects a `donations` table with columns at least: `id`, `description`, `image_path`, `created_at`.
- Check `database/schema.sql` or migrations for column types.

Best practices & security reminders
- Escape all output: the code uses `htmlspecialchars()` and `nl2br()` — good.
- Use prepared statements for all user input: the insert uses prepared statements.
- Validate uploads: check mime type and file size; consider using image libraries to re-encode uploaded images.
- Only admins should create/delete posts. This page checks `isAdmin()` before processing POST.

Practical tasks you might do next
- Extract POST logic into a controller and reuse for an API endpoint.
- Add server-side validation and CSRF checks.
- Add pagination for the listings.
- Add admin-only delete/edit actions (with confirmation and proper prepared statements).

References
- Page: `features/donations/admin/pages/donations.php`
- View: `features/donations/admin/views/manage-donations.php`
- DB helper: `features/shared/lib/database/mysqli-db.php`
- Layouts: `features/shared/components/layouts/dashboard-layout.php`, `features/shared/components/layouts/base.php`
- Session/auth: `features/shared/lib/auth/session.php`

End of Donations admin doc.
