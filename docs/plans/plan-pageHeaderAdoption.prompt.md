## Goal
Adopt the revised styleguide page header into the real system as a reusable partial, starting with integration into the shared layout so every page can define its own header content via variables.

## High-Level Approach
Centralize a reusable page header partial with breadcrumb, title, subtitle, and actions; integrate it into the shared layout (`app-layout.php` + `base.php`) so every page can declare a `$pageHeader` array for consistent styling per the styleguide.

## Steps
1. **Create shared page header partial**  
   - Add `features/shared/components/page-header.php` that renders a `.dashboard-header` block following the styleguide structure: breadcrumb, `h1.page-title`, optional subtitle, and an actions area.  
   - Accept a `$pageHeader` associative array and fall back gracefully when pieces are missing.  
   - Ensure all dynamic content is escaped with `htmlspecialchars()` and URLs are sanitized/constructed via existing helpers.

2. **Wire partial into shared layout**  
   - Inspect `features/shared/components/layouts/app-layout.php` and `features/shared/components/layouts/base.php` to confirm the current content wrapping pattern.  
   - Inject `include $ROOT . '/features/shared/components/page-header.php';` (or similar) into `app-layout.php` at the top of the main content area, before `$content` is printed.  
   - Ensure this include can see `$pageHeader` and `$pageTitle`, and works even if `$pageHeader` is not defined (fallback behavior).

3. **Align CSS with styleguide**  
   - Reuse or move the header-specific styles from `styleguides/layout-template.html` into the shared CSS (likely `features/shared/assets/css/layout.css` or another core CSS file that is already loaded by `base.php`).  
   - Make sure classes like `.dashboard-header`, `.breadcrumb`, `.page-title`, `.header-actions`, and related typography align with the styleguide and do not conflict with existing styles.  
   - Avoid inline styles for the header; rely purely on shared CSS.

4. **Define `$pageHeader` contract**  
   - Standardize the expected structure of `$pageHeader`, e.g.:  
     ```php
     $pageHeader = [
         'title' => 'Donations',
         'subtitle' => 'Manage and track donation records.',
         'breadcrumb' => [
             ['label' => 'Home', 'url' => '/'],
             ['label' => 'Donations', 'url' => null], // last becomes active
         ],
         'actions' => [
             [
                 'label' => 'New Donation',
                 'icon'  => 'fa-plus',
                 'url'   => '/features/donations/admin/pages/create.php',
                 'class' => 'btn-primary',
             ],
         ],
     ];
     ```  
   - Implement logic in the partial to:  
     - Use `$pageHeader['title']` if present, otherwise fall back to `$pageTitle` or a sensible default.  
     - Treat the last breadcrumb item as `active` and non-clickable if it has no `url`.  
     - Render `actions` as buttons/links only if provided.

5. **Update representative pages to use `$pageHeader`**  
   - Pick a few key pages as initial adopters (e.g., `features/donations/admin/pages/donations.php`, `features/dashboard/user/pages/dashboard.php`, root `index.php`).  
   - For each, define `$pageHeader` with appropriate title, subtitle, breadcrumb, and actions before including `app-layout.php`/`base.php`.  
   - Remove any duplicated local page header markup from those pages and rely on the shared partial instead.

6. **Ensure all layout paths are consistent**  
   - Confirm that all pages that should display the new header are flowing through `app-layout.php` + `base.php`.  
   - For pages that currently bypass the shared layout and render sidebars/headers manually, plan a gradual migration:  
     - Wrap their content via output buffering into `$content`.  
     - Include `app-layout.php` and then `base.php` so they inherit the common chrome and header behavior.  

7. **Fallbacks and progressive adoption**  
   - Design the partial so that if `$pageHeader` is not defined, it still renders a minimal header (e.g., a title from `$pageTitle` and a simple Home → Current breadcrumb).  
   - This allows pages to gradually adopt richer header metadata without breaking existing screens.

8. **Document usage**  
   - Add a short usage section in `docs/implementations/STATUS.md` or a dedicated `docs/implementations/page-header-usage.md` describing:  
     - Where the partial lives.  
     - The structure of `$pageHeader`.  
     - Example snippets for admin vs user pages.  
   - Optionally, link this documentation from `styleguides/index.html` or relevant context docs so designers and developers can see the live header in both styleguide and real pages.

9. **Testing and refinement**  
   - Manually verify a few pages (admin and user) in the browser to ensure:  
     - Breadcrumbs render correctly and are keyboard accessible.  
     - Titles, subtitles, and actions match the expectations from the PRD/context docs.  
     - The header behaves well on smaller screens (responsive behavior).  
   - Tweak spacing, fonts, and breakpoints as needed to keep parity with the styleguide.
