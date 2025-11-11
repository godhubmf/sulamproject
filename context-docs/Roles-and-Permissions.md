# Roles & Permissions (RBAC)

Status: Draft 0.2  
Owner: [Your Name]  
Last updated: 2025-11-11

**Implementation Note**: RBAC will be implemented as custom middleware in `/features/shared/lib/auth/`. Each feature module has separate admin/ subdirectories with their own controllers and views, enforcing separation at the architectural level.

## Roles
- Admin (can include Treasurer/Finance responsibilities)
- Regular User

## Permission Areas
- Users & Roles
- Resident Registry
- Assistance (Applications, Approvals, Disbursements)
- Donations & Receipts
- Death & Funeral
- Events & Announcements
- Reports
- Audit Log
- System Config (retention, categories, etc.)

## Starter Matrix (MVP)

| Area | Regular | Admin |
|------|---------|-------|
| Users & Roles | - | Full CRUD |
| Resident Registry | View | Full CRUD + Merge |
| Assistance | View own status | Approve/Reject; Disburse; Configure |
| Donations & Receipts | View own receipts | Configure; export |
| Death & Funeral | View | Verify; approve assistance |
| Events & Announcements | View | Approve/Unpublish |
| Reports | View public | View all; export |
| Audit Log | - | View all |
| System Config | - | Full |

Notes:
- "View own" means limited to the user’s own account or actions.  
- Use deny-by-default; grant minimal privileges per role.