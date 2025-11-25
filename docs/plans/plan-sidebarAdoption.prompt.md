## Goal
Adopt the styleguide’s cleaner, modern sidebar layout from `styleguides/layout-template.html` into the live system’s shared sidebar component, preserving all existing navigation behavior and RBAC while upgrading the visual structure and UX.

## High-Level Approach
Refactor the shared `sidebar.php` markup to match the styleguide’s sidebar structure (brand, logo placeholder, nav, user profile, footer actions) and align the sidebar-related CSS in `layout.css` with the styleguide’s rules. Keep routing, active-state logic, and role-based visibility exactly as they are.

## Steps
1. **Extract sidebar design from styleguide**  
   - Review `styleguides/layout-template.html` and identify all sidebar-related elements and classes: `.brand`, `.brand-text`, `.logo-placeholder`, `.nav`, `.nav-divider`, `.user-profile`, `.sidebar-footer`, nav link active state indicator, etc.  
   - Note differences between styleguide `.sidebar` structure (flex column inside `.dashboard-wrapper`) and current live sidebar implementation in `features/shared/components/sidebar.php`.

2. **Audit existing live sidebar implementation**  
   - Open `features/shared/components/sidebar.php` and document:  
     - Current HTML nesting (brand/logo, nav links, role-based sections, footer elements).  
     - How `$currentPage` or similar variables are used to mark the active menu item.  
     - Any conditional blocks for admin vs user links.  
   - Confirm how the sidebar is included in layouts (e.g., `app-layout.php`) and how it interacts with `.dashboard` / `.content` in `layout.css`.

3. **Design updated sidebar HTML structure**  
   - Draft a new sidebar structure that:  
     - Wraps the existing navigation links inside a `nav.nav` container, preserving `href`s and permissions.  
     - Adds the styleguide brand block: `.brand` with `.brand-text` (icon + text) and optional `.logo-placeholder`.  
     - Optionally shows a user profile block (`.user-profile`) using current session username/role.  
     - Adds a `.sidebar-footer` section for settings/logout or other global actions.  
   - Keep PHP logic intact (auth checks, role checks), only changing the surrounding HTML classes and hierarchy.

4. **Refactor `sidebar.php` to new structure**  
   - Update `features/shared/components/sidebar.php` markup to match the planned structure, using the styleguide’s class names.  
   - Ensure the active-state logic corresponds to `<a class="active">` on the current page link.  
   - Use existing helpers (e.g., `url()`, `e()`) and session variables for username, role, etc.  
   - Preserve all existing navigation items and their ordering.

5. **Align CSS with styleguide sidebar**  
   - Merge or adjust `.sidebar`, `.nav`, `.nav a`, `.nav a.active`, `.nav-divider`, `.user-profile`, `.sidebar-footer` definitions in `features/shared/assets/css/layout.css` to closely match the styleguide’s appearance.  
   - Ensure the active-dot indicator (pseudo-element on `.sidebar .nav a.active::after`) is included if not present.  
   - Verify the fixed/column layout in the live system (`position: fixed`, `padding`, `height: 100vh`) remains compatible with the new structure, or adjust to the styleguide’s column layout as needed without breaking existing pages.

6. **Preserve and refine active-state handling**  
   - Confirm how pages currently set `$currentPage` or equivalent, and how `sidebar.php` uses it.  
   - Make sure the markup now sets `class="active"` on the correct `<a>` elements and that CSS visually distinguishes active items (background, text color, optional indicator dot).  
   - Optionally standardize active-state logic for newly added/feature-based routes.

7. **Responsive and overflow behavior**  
   - Check existing `responsive.css` for any sidebar-specific rules and adjust if necessary to support the updated structure (e.g., collapsing sidebar on small screens, vertical scroll).  
   - Ensure the brand section, user profile, and footer do not overlap with content on smaller viewports; validate scroll behavior when nav is long.

8. **Update documentation**  
   - Create `docs/implementations/sidebar-usage.md` describing:  
     - The updated `sidebar.php` structure (brand, nav, profile, footer).  
     - How to add new nav items while preserving active state.  
     - How to show/hide items based on roles.  
     - How the sidebar integrates with `app-layout.php` and `.dashboard` layout.  
   - Add a short note to `docs/implementations/STATUS.md` under a "Recent Updates" or UI section summarizing sidebar adoption.

9. **Testing and refinement**  
   - Manually test key pages (admin and user dashboards, residents, donations, events) to ensure:  
     - Sidebar renders correctly with updated design.  
     - Active nav item is highlighted as expected.  
     - User profile and footer links appear for the correct roles.  
     - No layout regressions on desktop and common mobile widths.  
   - Adjust spacing, typography, or breakpoints to maintain consistency with the styleguide.

## Further Considerations
1. Decide whether to keep a simple `Logo` placeholder box or wire a real logo asset once available.  
2. Consider if admin vs user sidebar variants deserve separate label text or icon sets, or if a single shared component with role-based visibility is sufficient.
