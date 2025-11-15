# Sulam Project - Feature-Based Structure

Status: Draft 0.1  
Owner: [Your Name]  
Last updated: 2025-11-11

## Overview

This document outlines the feature-based structure for the Sulam Project, organizing all related files (PHP, CSS, JS) by feature module rather than by file type. This architecture improves maintainability, reduces coupling, and enhances developer experience.

## Core Principles

1. **Feature Cohesion**: Group all related files (PHP, CSS, JS) by feature
2. **Clear Boundaries**: Separate admin and user interfaces logically within each feature
3. **Shared Components**: Extract truly reusable code into shared modules
4. **Consistent Patterns**: Use the same structure across all features
5. **Security First**: Document root points to root directory directly (use .htaccess for security)

## Feature Modules

### 1. Residents Module (`/features/residents/`)

**Purpose**: Manage household and individual resident records, relationships, consent, and documents.

```
residents/
├── shared/
│   ├── lib/
│   │   ├── Resident.php              # Core resident entity
│   │   ├── Household.php             # Household entity
│   │   ├── Relationship.php          # Family/NOK relationships
│   │   ├── Consent.php               # Consent management
│   │   ├── Document.php              # Document handling
│   │   └── ResidentValidation.php    # Validation rules
│   ├── api/
│   │   ├── residents.php             # CRUD operations
│   │   └── search-residents.php      # Search functionality
│   └── assets/
│       ├── css/
│       │   └── residents-shared.css
│       └── js/
│           ├── resident-utils.js
│           └── deduplication.js
├── admin/
│   ├── controllers/
│   │   └── AdminResidentsController.php
│   ├── views/
│   │   ├── manage-residents.php
│   │   ├── view-resident.php
│   │   ├── edit-resident.php
│   │   └── partials/
│   │       ├── resident-form.php
│   │       ├── residents-table.php
│   │       ├── household-details.php
│   │       ├── family-tree.php
│   │       └── consent-history.php
│   ├── ajax/
│   │   ├── save-resident.php
│   │   ├── search-residents.php
│   │   ├── merge-residents.php
│   │   └── upload-document.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin-residents.css
│   │   └── js/
│   │       ├── manage-residents.js
│   │       ├── resident-search.js
│   │       └── document-upload.js
│   └── lib/
│       └── AdminResidentsModel.php
└── user/
    ├── controllers/
    │   └── UserResidentsController.php
    ├── views/
    │   ├── register-resident.php
    │   ├── view-resident.php
    │   └── partials/
    │       └── resident-form.php
    ├── ajax/
    │   └── save-resident.php
    ├── assets/
    │   ├── css/
    │   │   └── user-residents.css
    │   └── js/
    │       └── register-resident.js
    └── lib/
        └── UserResidentsModel.php
```

**Key Features**:
- Household and individual management
- Family relationship tracking
- Consent record management
- Document attachment system
- Deduplication hints
- Search and filter functionality

---

### 2. Assistance Module (`/features/assistance/`)

**Purpose**: Manage financial assistance (zakat) applications, assessments, approvals, disbursements, and receipts.

```
assistance/
├── shared/
│   ├── lib/
│   │   ├── Application.php           # Application entity
│   │   ├── Assessment.php            # Assessment entity
│   │   ├── Approval.php              # Approval workflow
│   │   ├── Disbursement.php          # Disbursement handling
│   │   ├── Receipt.php               # Receipt generation
│   │   ├── AssistanceValidation.php  # Validation rules
│   │   └── AsnaafCategories.php      # Eligibility categories
│   ├── api/
│   │   ├── applications.php
│   │   └── disbursements.php
│   └── assets/
│       ├── css/
│       │   └── assistance-shared.css
│       └── js/
│           └── assistance-utils.js
├── admin/
│   ├── controllers/
│   │   └── AdminAssistanceController.php
│   ├── views/
│   │   ├── manage-applications.php
│   │   ├── approve-application.php
│   │   ├── disbursements.php
│   │   ├── view-application.php
│   │   └── partials/
│   │       ├── applications-table.php
│   │       ├── assessment-form.php
│   │       ├── approval-form.php
│   │       ├── disbursement-form.php
│   │       └── receipt-template.php
│   ├── ajax/
│   │   ├── save-assessment.php
│   │   ├── approve-application.php
│   │   ├── disburse-funds.php
│   │   └── generate-receipt.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin-assistance.css
│   │   └── js/
│   │       ├── manage-applications.js
│   │       ├── approval-workflow.js
│   │       └── disbursement.js
│   └── lib/
│       ├── AdminAssistanceModel.php
│       └── ApprovalWorkflow.php
└── user/
    ├── controllers/
    │   └── UserAssistanceController.php
    ├── views/
    │   ├── create-application.php
    │   ├── edit-application.php
    │   ├── view-applications.php
    │   └── partials/
    │       └── application-form.php
    ├── ajax/
    │   └── submit-application.php
    ├── assets/
    │   ├── css/
    │   │   └── user-assistance.css
    │   └── js/
    │       └── create-application.js
    └── lib/
        └── UserAssistanceModel.php
```

**Key Features**:
- Application submission workflow
- Assessment and eligibility checking
- Single-step approval (expandable to multi-step)
- Disbursement tracking (cash/bank)
- Receipt generation
- Audit trail of all changes

---

### 3. Donations Module (`/features/donations/`)

**Purpose**: Track donations, pledges, donors, and issue receipts.

```
donations/
├── shared/
│   ├── lib/
│   │   ├── Donor.php
│   │   ├── Donation.php
│   │   ├── Pledge.php
│   │   └── DonationReceipt.php
│   └── api/
│       └── donations.php
├── admin/
│   ├── controllers/
│   ├── views/
│   │   ├── manage-donations.php
│   │   ├── manage-donors.php
│   │   └── reports.php
│   ├── ajax/
│   ├── assets/
│   └── lib/
└── user/
    ├── controllers/
    ├── views/
    │   ├── record-donation.php
    │   └── issue-receipt.php
    ├── ajax/
    ├── assets/
    └── lib/
```

**Key Features**:
- Donor profile management
- Donation recording
- Pledge tracking
- Receipt generation
- Basic donation reports

---

### 4. Death & Funeral Module (`/features/death-funeral/`)

**Purpose**: Manage death notifications, verification, NOK details, funeral logistics, and assistance.

```
death-funeral/
├── shared/
│   ├── lib/
│   │   ├── DeathNotification.php
│   │   ├── Verification.php
│   │   ├── FuneralLogistics.php
│   │   └── FuneralAssistance.php
│   └── api/
│       └── death-notifications.php
├── admin/
│   ├── controllers/
│   ├── views/
│   │   ├── manage-notifications.php
│   │   ├── verify-death.php
│   │   ├── funeral-logistics.php
│   │   └── approve-assistance.php
│   ├── ajax/
│   ├── assets/
│   └── lib/
└── user/
    ├── controllers/
    ├── views/
    │   ├── record-notification.php
    │   ├── view-notifications.php
    │   └── logistics-tracking.php
    ├── ajax/
    ├── assets/
    └── lib/
```

**Key Features**:
- Death notification recording
- Verification details
- Next-of-kin (NOK) management
- Funeral logistics planning
- Assistance disbursement
- PDPA compliance for deceased data

---

### 5. Events Module (`/features/events/`)

**Purpose**: Create, publish, and manage events and announcements.

```
events/
├── shared/
│   ├── lib/
│   │   └── Event.php
│   └── api/
│       └── events.php
├── admin/
│   ├── controllers/
│   ├── views/
│   │   ├── manage-events.php
│   │   ├── create-event.php
│   │   └── edit-event.php
│   ├── ajax/
│   ├── assets/
│   └── lib/
└── user/
    ├── controllers/
    ├── views/
    │   ├── view-events.php
    │   ├── create-event.php
    │   └── publish-announcement.php
    ├── ajax/
    ├── assets/
    └── lib/
```

**Key Features**:
- Event creation and publishing
- Announcement system
- Schedule management
- Visibility controls

---

### 6. Dashboard Module (`/features/dashboard/`)

**Purpose**: Admin and user dashboards with statistics and overview.

```
dashboard/
├── admin/
│   ├── controllers/
│   │   └── AdminDashboardController.php
│   ├── views/
│   │   ├── dashboard.php
│   │   └── partials/
│   │       ├── stats-overview.php
│   │       ├── recent-applications.php
│   │       ├── pending-approvals.php
│   │       └── residents-summary.php
│   ├── ajax/
│   │   └── dashboard-data.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin-dashboard.css
│   │   └── js/
│   │       ├── admin-dashboard.js
│   │       └── dashboard-charts.js
│   └── lib/
│       └── AdminDashboardStats.php
└── user/
    ├── controllers/
    │   └── UserDashboardController.php
    ├── views/
    │   ├── dashboard.php
    │   └── partials/
    │       ├── my-tasks.php
    │       └── recent-activity.php
    ├── ajax/
    │   └── dashboard-data.php
    ├── assets/
    │   ├── css/
    │   │   └── user-dashboard.css
    │   └── js/
    │       └── user-dashboard.js
    └── lib/
        └── UserDashboardStats.php
```

**Key Features**:
- Role-specific dashboards
- Statistics and KPIs
- Recent activity feed
- Quick actions
- Pending items overview

---

### 7. Users Module (`/features/users/`)

**Purpose**: User account management and profiles.

```
users/
├── shared/
│   ├── lib/
│   │   ├── UserModel.php
│   │   └── UserValidation.php
│   └── api/
│       └── users.php
├── admin/
│   ├── controllers/
│   ├── views/
│   │   ├── manage-users.php
│   │   ├── add-user.php
│   │   └── edit-user.php
│   ├── ajax/
│   │   ├── save-user.php
│   │   └── get-users-list.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── admin-users.css
│   │   └── js/
│   │       └── manage-users.js
│   └── lib/
│       └── AdminUsersModel.php
└── user/
    ├── views/
    │   └── profile.php
    └── assets/
        ├── css/
        │   └── profile.css
        └── js/
            └── profile.js
```

**Key Features**:
- User CRUD operations (Admin only)
- Role assignment (Admin, Regular User)
- Profile management
- Password management

---

### 8. Reports Module (`/features/reports/`)

**Purpose**: Generate summary reports for various modules.

```
reports/
├── shared/
│   └── lib/
│       ├── ReportGenerator.php
│       └── ReportService.php
├── admin/
│   ├── controllers/
│   ├── views/
│   │   └── generate-reports.php
│   ├── ajax/
│   │   ├── generate-report.php
│   │   └── report-data.php
│   ├── assets/
│   │   ├── css/
│   │   │   └── reports.css
│   │   └── js/
│   │       └── reports.js
│   └── lib/
│       └── AdminReports.php
└── user/
    ├── views/
    │   └── view-reports.php
    └── assets/
```

**Key Features**:
- Zakat/assistance reports
- Donation summaries
- Resident statistics
- Export functionality

---

### 9. Shared Components (`/features/shared/`)

**Purpose**: Truly reusable components used across multiple features.

```
shared/
├── components/
│   ├── layouts/
│   │   ├── base.php                  # Main HTML structure
│   │   ├── navbar-modern.php         # Navigation bar
│   │   ├── footer-modern.php         # Footer
│   │   └── page-header.php           # Page headers
│   ├── forms/
│   │   ├── input.php                 # Input component
│   │   ├── select.php                # Select dropdown
│   │   ├── textarea.php              # Textarea component
│   │   └── file-upload.php           # File upload component
│   ├── modals/
│   │   ├── confirm-modal.php         # Confirmation modal
│   │   └── info-modal.php            # Information modal
│   └── ui/
│       ├── buttons.php               # Button components
│       ├── cards.php                 # Card components
│       ├── tables.php                # Table components
│       └── alerts.php                # Alert/notification components
├── controllers/
│   ├── AuthController.php            # Authentication
│   └── BaseController.php            # Base controller class
├── lib/
│   ├── database/
│   │   ├── db-connect.php            # Database connection
│   │   └── Database.php              # Database wrapper
│   ├── auth/
│   │   ├── session.php               # Session management
│   │   └── AuthService.php           # Authentication service
│   ├── utilities/
│   │   ├── functions.php             # General utilities
│   │   └── validation.php            # Validation helpers
│   ├── audit/
│   │   └── audit-log.php             # Audit logging
│   └── notifications/
│       └── email-notifications.php   # Email system
├── api/
│   └── login.php                     # Login API
├── ajax/
│   └── clear-session-message.php     # Session helpers
└── assets/
    ├── css/
    │   ├── base.css                  # Main CSS orchestrator
    │   ├── variables.css             # CSS custom properties
    │   └── components/
    │       ├── buttons-modern.css
    │       ├── forms-modern.css
    │       ├── cards-modern.css
    │       ├── tables-modern.css
    │       ├── modals-modern.css
    │       └── alerts-modern.css
    └── js/
        ├── shared/
        │   ├── utils.js              # Utility functions
        │   ├── form-validation.js    # Form validation
        │   └── ui-helpers.js         # UI manipulation
        └── components/
            ├── modals.js             # Modal handling
            └── notifications.js      # Notification system
```

---

## Benefits of Feature-Based Structure

### 1. Improved Maintainability
- All related code in one place
- Easy to understand feature scope
- Reduced cognitive load

### 2. Better Code Discovery
- No need to search across multiple directories
- Clear feature boundaries
- Intuitive file organization

### 3. Reduced Coupling
- Features are self-contained
- Changes to one feature don't affect others
- Clear dependency management

### 4. Enhanced Developer Experience
- Faster development
- Easier onboarding for new developers
- Clear ownership of features

### 5. Better Testing
- Feature-level test isolation
- Easier to mock dependencies
- Clear test boundaries

### 6. Scalability
- Easy to add new features
- Team can work on different features simultaneously
- Clear merge conflict boundaries

---

## Migration Strategy

### Phase 1: Setup Foundation
1. Create new directory structure
2. Set up Vite configuration
3. Configure environment variables
4. Set up database connection

### Phase 2: Extract Shared Components
1. Identify truly reusable components
2. Extract to `/features/shared`
3. Update import paths
4. Test shared components

### Phase 3: Migrate Features One-by-One
1. Start with smallest feature (e.g., Events)
2. Move files to feature directory
3. Update import paths
4. Test feature thoroughly
5. Repeat for next feature

### Phase 4: Final Integration
1. Update routing in front controller
2. Test all features together
3. Performance testing
4. Security audit
5. Deploy to production

---

## Naming Conventions

### Files
- **Controllers**: `PascalCaseController.php` (e.g., `AdminResidentsController.php`)
- **Models/Entities**: `PascalCase.php` (e.g., `Resident.php`)
- **Views**: `kebab-case.php` (e.g., `manage-residents.php`)
- **AJAX endpoints**: `kebab-case.php` (e.g., `save-resident.php`)
- **CSS files**: `kebab-case.css` (e.g., `admin-residents.css`)
- **JS files**: `kebab-case.js` (e.g., `manage-residents.js`)

### Directories
- **Feature modules**: `kebab-case` (e.g., `death-funeral`)
- **Subdirectories**: `lowercase` (e.g., `controllers`, `views`, `assets`)

### Classes
- **Controllers**: `PascalCase` with `Controller` suffix
- **Models**: `PascalCase` matching entity name
- **Services**: `PascalCase` with `Service` suffix

---

## Open Questions

1. Should we use a lightweight routing library or custom routing? **Decision: Use custom direct file-based routing as outlined in Architecture.md.**
2. Do we need a template engine or use plain PHP includes? **Decision: Use plain PHP includes with a base layout system.**
3. Should we implement a simple ORM or use raw PDO? **Decision: Use raw PDO with prepared statements (no ORM).**
4. How granular should the admin/user separation be? **Decision: Separate admin and user interfaces within each feature, with admin having full access and user having limited access based on RBAC.**
5. Should we implement API versioning from the start? **Decision: Not needed for MVP; keep simple.**

---

## Next Steps

1. Review and approve this structure
2. Create initial directory structure
3. Set up Vite and build process
4. Begin migration with shared components
5. Migrate one feature as proof of concept
