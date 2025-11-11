# Architecture

Status: Draft 0.2  
Owner: [Your Name]  
Last updated: 2025-11-11

## System Context
- Users: Admin, Regular User.  
- External: Email/SMS gateway (optional later), Payment channels (future), Backup storage.## Technology Stack
- Backend: Plain PHP (no frameworks) with custom routing and session management.  
- DB: MySQL/MariaDB (Laragon for dev).  
- Frontend: Plain HTML, CSS, and JavaScript (no frameworks).  
- Build Tool: Vite for bundling CSS/JS assets.  
- Auth: Custom PHP session-based authentication with RBAC middleware.

## Key Components
- Auth & RBAC (custom PHP session management)  
- Resident Registry  
- Financial (applications, approvals, disbursements)  
- Death & Funeral  
- Donations & Receipts  
- Events & Announcements  
- Audit Logging  
- Reporting

## Project Directory Structure (Feature-Based)

### Overview
The project follows a feature-based architecture where all related files (PHP, CSS, JS) are grouped by feature module. This improves maintainability, code discovery, and reduces coupling between features.

### Core Principles
1. **Feature Cohesion**: Group all related files by feature/module
2. **Clear Boundaries**: Separate admin and user interfaces within features
3. **Shared Components**: Extract truly reusable code into shared modules
4. **Consistent Patterns**: Use the same structure across all features

### Directory Structure
```
sulam-project/
├── index.php                      # Front controller / router
├── login.php                      # Authentication entry point
├── logout.php                     # Logout handler
├── layouts/                       # Layout templates
│   ├── base.php                   # Main layout template
│   ├── admin.php                  # Admin-specific layout
│   └── user.php                   # User-specific layout
├── admin/                         # Admin user routes (direct file-based)
│   ├── dashboard.php              # Admin dashboard router
│   ├── residents.php              # Residents list router
│   ├── residents/
│   │   ├── create.php             # Create resident router
│   │   ├── edit.php               # Edit resident router
│   │   ├── view.php               # View resident router
│   │   └── ajax/
│   │       └── validate.php       # AJAX validation router
│   └── financial.php              # Financial management router
├── user/                          # Regular user routes (direct file-based)
│   ├── dashboard.php              # User dashboard router
│   ├── financial/
│   │   ├── apply.php              # Financial assistance application
│   │   ├── status.php             # Application status
│   │   └── ajax/
│   │       └── validate.php       # AJAX validation router
│   └── profile.php                # User profile router
│   ├── residents/
│   │   ├── create.php             # Create resident router
│   │   ├── edit.php               # Edit resident router
│   │   ├── view.php               # View resident router
│   │   └── ajax/
│   │       └── validate.php       # AJAX validation router
│   └── financial.php              # Financial management router
├── user/                          # Regular user routes (direct file-based)
│   ├── dashboard.php              # User dashboard router
│   ├── financial/
│   │   ├── apply.php              # Financial assistance application
│   │   ├── status.php             # Application status
│   │   └── ajax/
│   │       └── validate.php       # AJAX validation router
│   └── profile.php                # User profile router
├── assets/                        # Compiled/bundled assets (Vite output)
│   ├── css/
│   └── js/
├── uploads/                       # User-uploaded files (protected)
│   ├── residents/
│   ├── applications/
│   └── documents/
│
├── features/                      # Feature-based modules
│   ├── residents/                 # Resident registry module
│   │   ├── shared/
│   │   │   ├── lib/
│   │   │   │   ├── Resident.php
│   │   │   │   ├── Household.php
│   │   │   │   └── ResidentValidation.php
│   │   │   ├── api/
│   │   │   │   └── residents.php
│   │   │   └── assets/
│   │   │       ├── css/
│   │   │       │   └── residents-shared.css
│   │   │       └── js/
│   │   │           └── resident-utils.js
│   │   ├── admin/
│   │   │   ├── controllers/
│   │   │   │   └── AdminResidentsController.php
│   │   │   ├── views/
│   │   │   │   ├── manage-residents.php
│   │   │   │   ├── view-resident.php
│   │   │   │   └── partials/
│   │   │   │       ├── resident-form.php
│   │   │   │       └── residents-table.php
│   │   │   ├── ajax/
│   │   │   │   ├── save-resident.php
│   │   │   │   └── search-residents.php
│   │   │   ├── assets/
│   │   │   │   ├── css/
│   │   │   │   │   └── admin-residents.css
│   │   │   │   └── js/
│   │   │   │       └── manage-residents.js
│   │   │   └── lib/
│   │   │       └── AdminResidentsModel.php
│   │   └── user/
│   │       ├── controllers/
│   │       ├── views/
│   │       ├── ajax/
│   │       ├── assets/
│   │       └── lib/
│   │
│   ├── financial/                 # Financial assistance (zakat) module
│   │   ├── shared/
│   │   │   ├── lib/
│   │   │   │   ├── Application.php
│   │   │   │   ├── Assessment.php
│   │   │   │   └── Disbursement.php
│   │   │   └── api/
│   │   │       └── applications.php
│   │   ├── admin/
│   │   │   ├── controllers/
│   │   │   ├── views/
│   │   │   │   ├── manage-applications.php
│   │   │   │   ├── approve-application.php
│   │   │   │   └── disbursements.php
│   │   │   ├── ajax/
│   │   │   ├── assets/
│   │   │   └── lib/
│   │   └── user/
│   │       ├── controllers/
│   │       ├── views/
│   │       ├── ajax/
│   │       ├── assets/
│   │       └── lib/
│   │
│   ├── donations/                 # Donations and receipts module
│   │   ├── shared/
│   │   ├── admin/
│   │   └── user/
│   │
│   ├── death-funeral/             # Death and funeral assistance module
│   │   ├── shared/
│   │   ├── admin/
│   │   └── user/
│   │
│   ├── events/                    # Events and announcements module
│   │   ├── shared/
│   │   ├── admin/
│   │   └── user/
│   │
│   ├── dashboard/                 # Dashboard module
│   │   ├── admin/
│   │   │   ├── controllers/
│   │   │   │   └── AdminDashboardController.php
│   │   │   ├── views/
│   │   │   │   └── dashboard.php
│   │   │   ├── ajax/
│   │   │   │   └── dashboard-data.php
│   │   │   ├── assets/
│   │   │   │   ├── css/
│   │   │   │   └── js/
│   │   │   └── lib/
│   │   └── user/
│   │       ├── controllers/
│   │       ├── views/
│   │       ├── ajax/
│   │       ├── assets/
│   │       └── lib/
│   │
│   ├── users/                     # User management module
│   │   ├── shared/
│   │   │   ├── lib/
│   │   │   │   └── UserModel.php
│   │   │   └── api/
│   │   ├── admin/
│   │   │   ├── controllers/
│   │   │   ├── views/
│   │   │   │   ├── manage-users.php
│   │   │   │   └── edit-user.php
│   │   │   ├── ajax/
│   │   │   ├── assets/
│   │   │   └── lib/
│   │   └── user/
│   │       ├── views/
│   │       │   └── profile.php
│   │       └── assets/
│   │
│   ├── reports/                   # Reporting module
│   │   ├── shared/
│   │   │   └── lib/
│   │   │       └── ReportGenerator.php
│   │   ├── admin/
│   │   └── user/
│   │
│   └── shared/                    # Shared/common components
│       ├── components/
│       │   ├── layouts/
│       │   │   ├── base.php
│       │   │   ├── navbar-modern.php
│       │   │   ├── footer-modern.php
│       │   │   └── page-header.php
│       │   ├── forms/
│       │   │   ├── input.php
│       │   │   ├── select.php
│       │   │   └── textarea.php
│       │   ├── modals/
│       │   │   ├── confirm-modal.php
│       │   │   └── info-modal.php
│       │   └── ui/
│       │       ├── buttons.php
│       │       ├── cards.php
│       │       └── tables.php
│       ├── controllers/
│       │   ├── AuthController.php
│       │   └── BaseController.php
│       ├── lib/
│       │   ├── database/
│       │   │   ├── db-connect.php
│       │   │   └── Database.php
│       │   ├── auth/
│       │   │   ├── session.php
│       │   │   └── AuthService.php
│       │   ├── utilities/
│       │   │   ├── functions.php
│       │   │   └── validation.php
│       │   ├── audit/
│       │   │   └── audit-log.php
│       │   └── notifications/
│       │       └── email-notifications.php
│       ├── api/
│       │   └── login.php
│       ├── ajax/
│       │   └── clear-session-message.php
│       └── assets/
│           ├── css/
│           │   ├── base.css
│           │   ├── variables.css
│           │   └── components/
│           │       ├── buttons-modern.css
│           │       ├── forms-modern.css
│           │       └── tables-modern.css
│           └── js/
│               ├── shared/
│               │   ├── utils.js
│               │   ├── form-validation.js
│               │   └── ui-helpers.js
│               └── components/
│                   └── modals.js
│
├── database/
│   ├── migrations/                # SQL migration files
│   └── seeds/                     # Sample/initial data
│
├── storage/                       # Logs, cache, sessions
│   ├── logs/
│   ├── cache/
│   └── sessions/
│
├── tests/                         # Unit and integration tests
├── context-docs/                  # Project documentation
├── .env.example                   # Environment variables template
├── .env                           # Environment variables (gitignored)
├── .gitignore
├── vite.config.js                 # Vite configuration
├── package.json                   # Node dependencies (Vite)
└── README.md
```

### Feature Module Structure Pattern
Each feature module follows this consistent pattern:
```
feature-name/
├── shared/              # Code used by both admin and user
│   ├── lib/            # Business logic and models
│   ├── api/            # REST API endpoints
│   └── assets/         # Shared CSS/JS
├── admin/              # Admin-specific code
│   ├── controllers/    # Admin controllers
│   ├── views/          # Admin views
│   ├── ajax/           # Admin AJAX endpoints
│   ├── assets/         # Admin CSS/JS
│   └── lib/            # Admin-specific logic
└── user/               # User-specific code
    ├── controllers/
    ├── views/
    ├── ajax/
    ├── assets/
    └── lib/
```

### Key Notes
- **Document root points to root directory** - all files accessible from web root (use .htaccess for security)
- **Vite bundles** `/features/**/assets` → `/assets` for optimized delivery
- **Direct file-based routing**: Each PHP file corresponds to a route (e.g., `/admin/residents/create.php`) and delegates to feature-based controllers
- **Database credentials** and secrets stored in `.env` file (never committed)
- **Feature isolation**: Changes to one feature don't affect others
- **Shared components**: Extracted to `/features/shared` to avoid duplication

## Routing Architecture

### Direct File-Based Routing Pattern
We use direct file-based routing where each PHP file corresponds to a specific route:

#### Route Structure
```
/
/admin/dashboard.php           → Admin dashboard
/user/dashboard.php            → User dashboard
/admin/residents.php           → Residents list
/admin/residents/create.php    → Create resident form
/admin/residents/edit.php      → Edit resident form
/admin/residents/view.php      → View resident details
/user/financial/apply.php      → Financial assistance application
/user/financial/ajax/validate.php → AJAX validation
```

#### Router File Pattern
Each route file follows this pattern:
```php
<?php
require_once 'core/path_helper.php';
require_once project_include('features/residents/admin/controllers/AdminResidentsController.php');

$controller = new AdminResidentsController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->createResident();  // Handle form submission
} else {
    $controller->showCreateResident();  // Show form
}
?>
```

#### Benefits
- **Intuitive URLs**: `/admin/residents/create` is self-explanatory
- **Simple maintenance**: Add new route by creating new file
- **Feature isolation**: Business logic stays in `features/` directory
- **HTTP method handling**: Easy GET/POST differentiation
- **AJAX integration**: Natural organization for AJAX endpoints

## Templating Architecture

### Base Layout System
We use plain PHP includes with a sophisticated base layout system for templating:

#### Layout Structure
```
base.php (main layout template)
/admin/layout.php (admin-specific layout)
/user/layout.php (user-specific layout)
```

#### Base Layout Components
- **HTML Skeleton**: Consistent DOCTYPE, head, body structure
- **Asset Management**: Vite integration for CSS/JS loading
- **Navigation**: Conditional navbar/sidebar loading
- **Content Area**: Placeholder for page-specific content
- **Footer**: Consistent footer across all pages

#### Variable Injection Pattern
Each page customizes the layout through variables:
```php
<?php
$pageTitle = 'Resident Registry';
$contentFile = 'features/residents/admin/views/manage-residents.php';
$cssBundle = 'admin-residents';
$jsBundle = 'adminResidents';
$showNavbar = true;
$currentUser = $authService->getCurrentUser();

include 'layouts/base.php';  // ← Layout template
?>
```

#### Template Organization
```
layouts/
├── base.php              # Main layout template
├── admin.php             # Admin-specific layout
└── user.php              # User-specific layout

features/{feature}/views/ # Page-specific content
├── admin/
│   ├── dashboard.php
│   ├── manage-residents.php
│   └── partials/
└── user/
    ├── dashboard.php
    ├── apply-financial.php
    └── partials/
```

#### Benefits
- **Zero dependencies**: No additional template engine libraries
- **Full PHP power**: Complete access to PHP features in templates
- **Performance**: No template compilation overhead
- **Familiarity**: Standard PHP development patterns
- **Flexibility**: Can include complex logic when needed
- **Asset integration**: Seamless Vite bundle loading

## Cross-cutting Concerns
- Security: password hashing (password_hash/password_verify), input validation, prepared statements, CSRF tokens, XSS defenses, HTTPS in prod.  
- Observability: request logs, audit logs, error tracking (custom error handler).  
- Data: SQL migrations, seeders, retention/archival jobs, backups.
- Asset Management: Vite for CSS/JS bundling, minification, and hot module replacement in dev.

## Deployment
- Dev: Laragon/XAMPP (Windows) with Vite dev server for asset hot-reload.  
- Staging/Prod: Linux server or managed hosting with HTTPS (Let's Encrypt), nightly backups, restricted DB access.  
- Document root points to root directory with proper .htaccess security rules.
- Build Process: `npm run build` compiles assets via Vite before deployment.
- CI/CD: Lint, tests, migrations; artifact-based deploys or zero-downtime if possible.

## Availability & Performance
- Targets: page ≤ 3s; queries ≤ 2s; 50 concurrent users.  
- Tactics: DB indexing, pagination, caching (config/query when safe), N+1 avoidance.  
- Backups & Recovery: nightly full + hourly binlog (or equivalent); recovery drills.

## Data Retention & Audit
- Immutable audit log for CRUD and approvals.  
- Configurable retention jobs for data subject to policy.

## Implementation Approach
- **No frameworks policy**: Keep dependencies minimal; only Vite for asset bundling.
- **Server-side rendering**: All HTML generated via PHP; progressive enhancement with vanilla JS.
- **Custom routing**: Direct file-based routing - each PHP file corresponds to a route (e.g., `/admin/residents/create.php`) and delegates to feature-based controllers.
- **Templating**: Plain PHP includes with base layout system - reusable layout templates with variable injection for page-specific content.
- **Database access**: PDO with prepared statements.
- **ORM usage**: Plain PDO (no query builder or ORM library).
- **Notification channels**: None initially (keep simple, in-app notifications only).
- **Templates**: PHP includes or simple template partials in `/src/views`.
- **RBAC**: Custom middleware checks user role/permissions before controller execution.
- **Approval workflow**: Single-step process for financial assistance applications.
- **Session management**: Native PHP sessions with secure configuration.
- **Asset pipeline**: Vite handles CSS preprocessing (if needed), JS bundling, minification, and versioning.