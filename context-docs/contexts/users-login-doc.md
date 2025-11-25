**Users Login Page Documentation**

This document explains everything a junior developer needs to know about the login view used by the application: how it is rendered, the helpers it depends on, how the POST is handled, where to change styles, and where to update behavior or logging.

**Location**: `features/users/shared/views/login.php`

**Purpose**: Render the login form (username/email + password), show flash messages, include a CSRF token, and post credentials to the login endpoint.

**Quick Links (files referenced by this page)**
- `features/users/shared/controllers/AuthController.php` : Controller that renders this view and handles the POST.
- `features/shared/lib/utilities/csrf.php` : CSRF helpers and the `csrfField()` helper used in the view.
- `features/shared/lib/utilities/functions.php` : Small helpers like `e()` (escape) and `redirect()` used across controllers/views.
- `features/shared/lib/auth/AuthService.php` : Authentication logic (login, register, logout).
- `features/shared/components/layouts/base.php` : HTML layout used to wrap the view (styles and page chrome are loaded here).
- `features/users/shared/assets/css/login.css` : Styles scoped to this login view.
- `features/shared/lib/routes.php` : Router mapping for `/login` (shows login page and handles POST).
- `features/shared/lib/audit/audit-log.php` : Audit logging used when login attempts are recorded by the controller.

**Where this view is included**
- The controller method `AuthController::showLogin()` includes this view and sets up `$message` and `$csrfToken` before including the `base.php` layout.
- Router mappings: see `features/shared/lib/routes.php` — `GET /login` calls `showLogin()`, `POST /login` calls `handleLogin()`.

**Rendered HTML structure (overview)**
- Root element: `<main class="centered small-card">` — this provides the centered card layout.
- Heading: `<h2>Login</h2>`
- Conditional message block: if `$message` is not empty, a `<div class="notice ...">` is rendered. The class will be `success` when the message text contains the word "successful"; otherwise `error`.
- Form: `<form method="post" action="/sulamproject/login">` with:
  - CSRF hidden field: output of `csrfField()` (returns a hidden `<input name="csrf_token" ...>`).
  - `input[name="username"]` (text) for username or email.
  - `input[name="password"]` (password).
  - Submit button `.btn` and a link to the register page.
- A small paragraph with a register link is shown below the form.

**Line-by-line explanation (key lines)**
- `<?php if (!empty($message)): ?>` — `$message` is expected to be provided by the controller (usually from `$_SESSION['message']`). If present, the view prints it inside a notice box.
- `<?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>` — quick heuristic to choose notice styling. If you need more exact types (info/warning), change this logic in the view or have controllers provide `message_type`.
- `<?php echo e($message); ?>` — `e()` is a small helper that calls `htmlspecialchars()` to safely escape output. See `features/shared/lib/utilities/functions.php`.
- `<form method="post" action="/sulamproject/login">` — posts credentials to the login endpoint which is routed to `AuthController::handleLogin()`.
- `<?php echo csrfField(); ?>` — prints a hidden input with the CSRF token. Implementation: `features/shared/lib/utilities/csrf.php`.

**How the login flow works (data & control flow)**
1. A visitor requests `/login` (GET). Router (`features/shared/lib/routes.php`) invokes `AuthController::showLogin()`.
2. `showLogin()` calls `initSecureSession()` (start session/config) and checks `isAuthenticated()`; if already logged in it redirects to `/dashboard`.
3. `showLogin()` reads any flash message from `$_SESSION['message']`, then calls `generateCsrfToken()` and includes this view (`login.php`) via PHP `include` inside the base layout. It also adds an `additionalStyles` entry for `features/users/shared/assets/css/login.css` so the view has scoped styles.
4. The view renders the HTML and the `csrfField()` (hidden input). The browser submits the form POST to `/sulamproject/login`.
5. Router maps `POST /login` to `AuthController::handleLogin()` which:
   - Initializes session and ensures method is POST.
   - Verifies the CSRF token via `verifyCsrfToken($_POST['csrf_token'] ?? '')`.
   - Validates non-empty username + password.
   - Calls `AuthService::login($username, $password)`.
   - On success, the service sets session values and the controller records the login in the audit log and redirects to `/dashboard`.
   - On failure, the controller sets `$_SESSION['message']` with the error message and redirects back to `/login` so the view can display it.

**Key helper functions and where they live**
- `csrfField()`, `generateCsrfToken()`, `verifyCsrfToken()` — `features/shared/lib/utilities/csrf.php` (use this file if you need a different token strategy or rotate tokens).
- `e($string)` — escape helper. `features/shared/lib/utilities/functions.php`.
- `initSecureSession()`, `isAuthenticated()`, `getUserId()`, `destroySession()` — session helpers live under `features/shared/lib/auth/session.php` and are used across controllers.
- `AuthService` methods (`login`, `register`, `logout`) — `features/shared/lib/auth/AuthService.php`.
- `AuditLog` — `features/shared/lib/audit/audit-log.php` (controller uses this to record success/failure events).

**Where to change styles**
- Scoped styles for this view are in: `features/users/shared/assets/css/login.css`.
  - To change form layout, update that file.
  - To change global variables (colors, spacing), edit `features/shared/assets/css/variables.css` which the base layout loads for all pages.

**Where to change server-side behavior**
- To change how the login POST is handled or validation rules: edit `features/users/shared/controllers/AuthController.php` (`handleLogin()` method).
- To change authentication logic (password hashing, token creation, session keys): edit `features/shared/lib/auth/AuthService.php`.
- To change routing (e.g., alternate endpoints), edit `features/shared/lib/routes.php` and the `Router` implementation at `features/shared/lib/utilities/Router.php`.

**Security notes (what to be careful about)**
- CSRF: The view uses `csrfField()` and the controller verifies the token. If you change the form, ensure the hidden input is still present.
- Escaping: All messages are printed through `e()` which escapes HTML. DO NOT remove escaping or echo raw user-supplied data without sanitizing.
- Sessions: Session initialization and regeneration are handled by session helpers; avoid duplicating session logic incorrectly.
- Password handling: `AuthService::login()` uses `password_verify()` against `password_hash()`; keep these secure defaults.

**Examples — Common edits**
- Add a "Forgot password" link: update `features/users/shared/views/login.php` to include an anchor next to the register link, and add a route + controller action for password reset in `features/shared/lib/routes.php` and a new controller file.
- Change error notice logic: instead of checking `strpos($message, 'successful')`, have the controller set `$_SESSION['message_type'] = 'success'|'error'` and read `message_type` in the view.
- Add client-side validation: include an additional script via the layout by populating `$additionalScripts` in the controller before including `base.php`.

**Debugging tips**
- If the flash message does not appear after a redirect, inspect `$_SESSION` contents in `AuthController::handleLogin()` right before redirect to ensure `$_SESSION['message']` is set.
- If CSRF verification fails unexpectedly, ensure sessions are persisting across requests and that the `csrf_token` field is present in the POST payload (use browser devtools or `var_dump($_POST)`).
- If styling doesn't load, confirm `additionalStyles` contains the correct path and `base.php` prints them; check file permissions and that the path is reachable from the site base (`/sulamproject/...`).

**References (paths to open now)**
- View: `features/users/shared/views/login.php`
- Controller: `features/users/shared/controllers/AuthController.php`
- Auth service: `features/shared/lib/auth/AuthService.php`
- CSRF helpers: `features/shared/lib/utilities/csrf.php`
- Utilities: `features/shared/lib/utilities/functions.php`
- Layout: `features/shared/components/layouts/base.php`
- Styles: `features/users/shared/assets/css/login.css`
- Routes: `features/shared/lib/routes.php`

If you'd like, I can also:
- Add inline comments to `login.php` explaining each block in-place.
- Create a small checklist for migrating this page into a separate feature module or converting the notice logic to use typed flash messages.

-- End of `users-login-doc.md`
