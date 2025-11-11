# Project Documentation

This folder is your single source of truth for designing and delivering the system. Start with the PRD, then work through the core supporting docs in order.

## Technology Stack
- **Backend**: Plain PHP (no frameworks)
- **Frontend**: Plain HTML, CSS, JavaScript
- **Build Tool**: Vite for bundling and asset management
- **Database**: MySQL/MariaDB
- **Architecture**: Feature-based modular structure

Recommended order ("first document" marked):

1. PRD – Product Requirements Document (first document)
2. Vision & Scope
3. Feature-Based Structure (NEW - defines directory organization)
4. Roles & Permissions (RBAC)
5. Use Cases & Flows
6. Data Model & ERD
7. Architecture
8. Security & Privacy
9. Audit Logging & Retention (later – see Security & Privacy to start)
10. Reporting Catalog (later)
11. Project Plan & Risks (later)

Quick links:

- Requirements Summary (source input): ./Requirements_Summary.md
- PRD: ./PRD.md
- Vision & Scope: ./Vision-And-Scope.md
- **Feature-Based Structure: ./Feature-Based-Structure.md** (NEW)
- Roles & Permissions: ./Roles-and-Permissions.md
- Use Cases & Flows: ./Use-Cases-and-Flows.md
- Data Model & ERD: ./Data-Model-ERD.md
- Architecture: ./Architecture.md
- Security & Privacy: ./Security-and-Privacy.md

Filling guidance:
- Keep each doc concise and living; link instead of duplicating.
- Capture open questions inline; resolve or move them to Risks/Decisions later.
- Prefer diagrams where they clarify (ERD, flowcharts, or sequence diagrams).
- All file paths and structures refer to the feature-based organization (see Feature-Based-Structure.md).

Ownership & versioning:
- Each doc begins with Owner, Status, and Last updated.
- Use PRs for material changes; use checklists for sign-off.
