# Product Requirements Document (PRD)

Status: Draft 0.2  
Owner: [Your Name]  
Last updated: 2025-11-11

**Technology Stack**: Plain PHP, CSS, JavaScript + Vite for bundling  
**Architecture**: Feature-based modular structure (see ./Feature-Based-Structure.md)

## 1. Overview

Problem: Administering community records and assistance using Excel/myMasjid is tedious, fragmented, and misses needy residents.  
Goal: Provide a unified, auditable system for Desa Ilmu to manage residents, financial aid (zakat), donations, deaths/funerals, events, and reporting.

## 2. Objectives and Success Metrics

- Single source of truth for residents/households (searchable, deduplicated).  
- Timely assistance with traceable approvals and disbursements.  
- Replace Excel/myMasjid for target workflows within 3 months of MVP.  
- Performance: page loads ≤ 3s; queries ≤ 2s; 50 concurrent users.  
- Security: hashed+salted passwords; RBAC; input validation; HTTPS in prod.  
- Availability: 99% during operational hours; no data loss (backups/transactions).

## 3. Users and Roles

- Admin (may include Treasurer/Finance Admin)
- Staff
- Regular User

See: ./Roles-and-Permissions.md

## 4. Scope

In scope (MVP focus):
- Resident registry (households, individuals, relationships, consent, documents).  
- Financial assistance (zakat) applications → assessment → approval → disbursement → receipt.  
- Donations tracking (donors, pledges, receipts) and basic reports.  
- Events/announcements publishing.  
- Audit logging and summary reports.

Out of scope (for MVP):
- Full accounting general ledger; multi-tenant support; advanced payment gateway integration.

## 5. Functional Requirements

1. Admins can create, edit, and delete user accounts with roles (Admin, Staff, Regular).  
2. Users/staff can register, update, and manage household and individual resident records.  
3. Record zakat payers and applicants; process applications; admins approve or reject.  
4. Record death notifications, next-of-kin (NOK), funeral assistance information.  
5. Record and track donations, pledges, and disbursements.  
6. Staff can create, publish, and update events/announcements visible to users.  
7. Provide efficient search/filter for residents, donations, events, etc.  
8. Generate summary reports for zakat, donations, and resident records.  
9. Record user activities (data edits, approvals) for accountability.  
10. Secure login/logout, only authorized users access sensitive data.

## 6. Non-Functional Requirements

- Performance: page ≤ 3s; queries ≤ 2s; ≥ 50 concurrent users.  
- Security: hashed+salted passwords; RBAC authorization; input validation (SQLi/XSS); HTTPS in prod.  
- Reliability: 99% availability; transactional integrity/backups; auto-recovery from minor failures.

## 7. Detailed Features by Module

**Architecture Note**: All modules follow the feature-based structure documented in ./Feature-Based-Structure.md. Each feature has its own directory under `/features/` with admin/ and staff/ subdirectories containing controllers, views, ajax endpoints, assets (CSS/JS), and business logic.

### 7.1 Resident Registry
- Entities: Household, Individual, Relationship (family/NOK), Contact, Consent, Document.  
- Actions: create/update residents; attach IDs/documents; link family; capture consent.  
- Search: by name/ID/address/phone; deduplication hints.

### 7.2 Financial Assistance (Zakat)
- Entities: Payer/Donor, Applicant, Eligibility (asnaf), Application, Assessment, Approval, Disbursement, Receipt.  
- Workflow: Application → Assessment → Approval (1-step to start) → Disbursement (cash/bank) → Receipt.  
- Controls: approval thresholds; audit trail of every change.  
- Reports: disbursements by category/period; pending vs approved.

### 7.3 Death and Funeral
- Entities: DeathNotification, Verification, NOK link, FuneralLogistics, Assistance.  
- Record verification details, burial logistics, and any assistance disbursed.  
- Note: Protect living persons’ data (PDPA); deceased data treated outside PDPA but linked records must be protected.

### 7.4 Donations and Finance
- Entities: Donor, Donation, Pledge, Receipt.  
- Record donations and issue receipts; basic donation summaries and trends.

### 7.5 Events and Announcements
- Create/publish events and notices; schedule, description, visibility; basic list and filters.

### 7.6 Audit, Reporting, Retention
- Immutable audit log for CRUD and approvals (who, what, when, before/after).  
- Standard reports (zakat, donations, residents).  
- Retention: configurable periods; deletion/archival policies.

## 8. Data Model Summary

High-level ERD and fields: see ./Data-Model-ERD.md

## 9. Flows and UX

- Key flows: Resident registration; Assistance application-to-disbursement; Death notification-to-assistance; Donation receipt; Events publishing.  
- Flow diagrams and notes: see ./Use-Cases-and-Flows.md

## 10. Risks and Open Questions

- Role granularity (e.g., Treasurer vs Admin vs Staff) – minimum viable matrix first.  
- Approvals: single vs multi-step, thresholds.  
- Data migration from Excel: scope, templates, deduplication strategy.  
- Integrations (payments, SMS/WhatsApp/email).  
- Localization (BM/EN).  
- Hosting/HTTPS strategy and backup frequency.

## 11. Acceptance Criteria (Sign-off Checklist)

- [ ] MVP roles: Admin/Staff/User implemented with RBAC.  
- [ ] Resident registry CRUD with relationships, consent, documents; search filters.  
- [ ] Assistance workflow end-to-end with receipts and audit log.  
- [ ] Donations recorded with receipts; basic reports available.  
- [ ] Events/announcements publishing.  
- [ ] Performance meets targets (3s/2s/50 users) in staging tests.  
- [ ] Security controls validated (hashing, input validation, HTTPS in prod).  
- [ ] Backups tested; basic recovery drill documented.

## 12. Milestones (Draft)

- M1: PRD sign-off; core data model; RBAC scaffold.  
- M2: Resident registry + search; audit log.  
- M3: Assistance workflow + reports.  
- M4: Donations + receipts + reports.  
- M5: Events/announcements; security hardening; MVP release.