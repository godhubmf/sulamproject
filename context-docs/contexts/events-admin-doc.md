## Events — Admin Page Documentation

Purpose
- Explain in detail how the Events admin page works: session and DB initialization, form handling (uploads and URLs), inserting records into the `events` table, listing events, and where to change behavior or presentation.

Files covered
- `features/events/admin/pages/events.php` — main admin page entry (handles form submit, uploads, DB insert, lists items).
- `features/events/admin/views/manage-events.php` — presentational fragment (placeholder view).
- `features/shared/lib/database/mysqli-db.php` — DB connection used by the page.
- `features/shared/components/layouts/dashboard-layout.php` — layout wrapper.
- `features/shared/components/layouts/base.php` — base HTML shell.
- `features/shared/lib/auth/session.php` — session/auth helpers.
- `features/shared/lib/utilities/functions.php` — helpers like `url()` and `e()`.

High-level flow
- Request arrives at `events.php`.
- Session and DB are prepared; user authentication is enforced.
- If the user is admin and the request is POST, the page handles creating a new event (description + optional image upload or URL), inserting into `events` table.
- The page queries `events` table and renders the list.
- The HTML is captured into `$content`, wrapped by the dashboard layout, and rendered inside `base.php`.

Line-by-line explanation of `events.php`
- `$ROOT = dirname(__DIR__, 4);` — compute repo root.
- `require_once $ROOT . '/features/shared/lib/auth/session.php';` — session/auth helpers.
- `require_once $ROOT . '/features/shared/lib/database/mysqli-db.php';` — DB connection exposing `$mysqli`.
- `initSecureSession(); requireAuth(); $isAdmin = isAdmin();` — start session, enforce login, and detect admin role.
- `$message` / `$messageClass` — used to show feedback messages to the user.

Handling POST (create event)
- `if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') { ... }`
  - Reads `description` from `$_POST` and validates it.
  - Processes `$_FILES['gamba']` similarly to donations:
    - Ensures `assets/uploads` exists.
    - Builds a sanitized filename `event_{timestamp}_{random}.{ext}`.
    - Moves the uploaded file into `assets/uploads` and sets `$gamba` to that relative path.
  - If `gamba_url` present, uses it instead.
  - Inserts into `events` table using prepared statements: `INSERT INTO events (description, image_path) VALUES (?, ?)`.

Listing events
- `$res = $mysqli->query('SELECT id, description, image_path, created_at FROM events ORDER BY id DESC');` — fetch rows into `$events` array.
- Rendering: each event prints an image if `image_path` exists (handles both relative paths and absolute URLs), the description, and `created_at` timestamp. Output is escaped with `htmlspecialchars()` and `nl2br()`.

Presentation
- The inner HTML is generated and captured to `$content`.
- `dashboard-layout.php` is included to wrap the content into the sidebar/main layout.
- `base.php` renders the final HTML document; add page-specific CSS by setting `$additionalStyles` before including `base.php`.

Where to change things
- Modify DB schema/migrations in `database/migrations` if you need additional fields like `title`, `start_time`, `location`.
- Add extra form fields (date, time, location) in the POST form and bind them in the prepared statement.
- Add validation and sanitization for new fields. Use `e()` for output escaping.
- Add CSRF tokens for the POST form.
- Move logic into controllers under `features/events/admin/controllers/` for better separation.

Security considerations
- Uploaded images are saved under `assets/uploads` — ensure proper permissions and scanning.
- Validate uploaded file types and sizes; do not rely solely on the extension.
- Use prepared statements (already used) to avoid SQL injection.

Related files and assets
- Page: `features/events/admin/pages/events.php`
- View: `features/events/admin/views/manage-events.php`
- DB helper: `features/shared/lib/database/mysqli-db.php`
- Layouts: `features/shared/components/layouts/dashboard-layout.php`, `features/shared/components/layouts/base.php`
- Session/auth: `features/shared/lib/auth/session.php`

Practical next steps
- Add event details fields and update DB migration.
- Add edit/delete functionality for events with proper admin checks.
- Add pagination for event listings.
- Extract logic into controllers and models: `EventsController.php` and `EventsModel.php`.

End of Events admin doc.
