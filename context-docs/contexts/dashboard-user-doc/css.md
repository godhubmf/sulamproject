# Dashboard User CSS Documentation

Hey junior dev! This covers the CSS for the user dashboard. The main file is `features/dashboard/user/assets/user-dashboard.css`, but it also uses the global `assets/css/style.css`. I'll explain every part, including how styles work and where to add new ones.

## Overview
The user dashboard uses minimal custom CSS. Most styling comes from the global style.css, which includes base styles, components, etc. The user-dashboard.css is for overrides specific to the user dashboard.

## user-dashboard.css (`features/dashboard/user/assets/user-dashboard.css`)
```css
/* Dashboard User Styles */
.dashboard-card {
  /* Inherits from base.css, add user-specific overrides here */
}
```

- `/* Dashboard User Styles */`: Comment indicating this is for user dashboard styles.
- `.dashboard-card { ... }`: Selector for dashboard cards. Currently empty, meaning it inherits all styles from the global CSS. You can add properties here to override, like changing colors or sizes.

This file is linked in the HTML? Wait, no, the HTML links `/sulamproject/assets/css/style.css`, not this file. So this CSS is not being used! Probably an oversight.

To use it, add `<link rel="stylesheet" href="/sulamproject/features/dashboard/user/assets/user-dashboard.css?v=...">` in the head of dashboard.php.

## Global style.css (`assets/css/style.css`)
This is the main stylesheet. It includes styles for .dashboard, .content, .small-card, .dashboard-header, .dashboard-cards, .dashboard-card, etc.

Since it's large, here's a summary of relevant classes:

- `.dashboard`: Flex layout for sidebar and main content.
- `.content`: Main area, takes remaining space.
- `.small-card`: Card styling with border, shadow, background.
- `.dashboard-header`: Styles for the header (h2, div).
- `.dashboard-cards`: Flex container for cards, wraps on small screens.
- `.dashboard-card`: Card styling: padding, border, hover effects, icon styles.

To add user-specific styles:
- If it's only for user dashboard, add to user-dashboard.css and link it.
- If it affects all dashboards, add to style.css in the appropriate section.

## How to Modify
- **Change card appearance**: Add properties to `.dashboard-card` in user-dashboard.css, like `background-color: lightblue;`.
- **Add new styles**: Define new classes in user-dashboard.css, then use them in the HTML.
- **Override global styles**: Use more specific selectors in user-dashboard.css.
- **Responsive design**: Check style.css for media queries; add user-specific ones in user-dashboard.css.

Remember, CSS cascades, so order matters. Global styles first, then user-specific.

That's the CSS side!</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\dashboard-user-doc\css.md