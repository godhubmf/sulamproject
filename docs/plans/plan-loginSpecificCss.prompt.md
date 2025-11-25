## Plan: Login CSS via Controller View

Create a feature-scoped login stylesheet and load it only for the controller-based login view using the layout’s `$additionalStyles` mechanism. This keeps styles isolated, follows the feature structure, and avoids modifying shared CSS.

### Steps
1. Create `features/users/shared/assets/css/login.css` with selectors scoped to `main.centered.small-card` and its children.
2. Ensure `features/users/shared/assets/` and `css/` directories exist; add `login.css` under `css/`.
3. In `features/users/shared/controllers/AuthController.php` `showLogin()`, set `$additionalStyles = ['/sulamproject/features/users/shared/assets/css/login.css'];` before including the layout.
4. Verify `features/shared/components/layouts/base.php` iterates `$additionalStyles` to emit `<link>` tags and uses absolute `/sulamproject/...` paths.
5. Manually test `/login` under the controller flow to confirm the stylesheet loads and styles are applied.

### Further Considerations
1. Router alignment: `/login` currently points to `login-direct.php`. Option A: keep legacy routes; Option B: switch routes to `AuthController` to see injected CSS.
2. CSS scope: Prefer non-global selectors (e.g., `main.centered.small-card`) to avoid bleed; add a page hook later if needed.
3. Cache-busting: If needed, append a version query (e.g., `login.css?v=1`) in `$additionalStyles`. 
