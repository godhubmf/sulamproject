## Plan: Adopt Styleguide into Shared Assets

Separate styleguide sections (notices, cards, buttons, forms, etc.) into individual CSS files in `features/shared/assets/css/`, import them all into `base.css`, and ensure `base.php` imports `base.css` for modular, maintainable styling aligned with the design system.

### Steps
1. Examine `styleguides/styleguide.css` and `styleguides/index.html` to identify all sections (colors, typography, buttons, forms, links, icons, cards, notices, toasts, layout, footer).
2. Create separate CSS files in `features/shared/assets/css/` for each section (e.g., `notices.css`, `cards.css`, `buttons.css`, `forms.css`, `typography.css`, `layout.css`, `toasts.css`, `icons.css`, `footer.css`, `responsive.css`).
3. Move corresponding styles from `base.css` into the new section files, ensuring consistency with styleguide examples and adding missing refinements (e.g., focus states for forms).
4. Update `base.css` to import all new section files in logical order (e.g., reset first, then components), keeping it as the single import point.
5. Verify `features/shared/components/layouts/base.php` imports `base.css` (it already does via `<link>` tags after `variables.css`).

### Further Considerations
1. Preserve `variables.css` as the first import in `base.php` since it defines custom properties used by all styles.
2. Add any styleguide-specific enhancements (e.g., ARIA for toasts, responsive breakpoints) to production CSS if missing.
3. Consider using Vite for concatenation in production to optimize loading, as per project build tools.
