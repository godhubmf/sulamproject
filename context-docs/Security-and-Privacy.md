# Security & Privacy

Status: Draft 0.2  
Owner: [Your Name]  
Last updated: 2025-11-11

## Technology Note
This system uses plain PHP without frameworks. All security implementations use native PHP functions and best practices.

## Identity & Access
- Passwords hashed and salted using PHP's native `password_hash()` and verified with `password_verify()` (uses bcrypt by default).  
- RBAC enforcement for every route/action (deny-by-default) via custom middleware.  
- Session security using PHP's native session management with secure configuration, CSRF protection, secure cookies.  
- Account lockout/attempt throttling implemented in custom logic.

## Data Protection
- Input validation and output encoding to prevent SQL injection and XSS using PHP's filter functions.  
- HTTPS in production (HSTS, modern TLS).  
- Least-privilege DB user; parameterized queries via PDO prepared statements.  
- Document uploads validated and scanned; limit types/sizes; store outside web root or in protected directory.
- All database access through PDO with prepared statements (no ORM).

## Privacy & PDPA Considerations
- Deceased data outside PDPA, but related living persons’ data must be protected.  
- Consent records for data processing and retention; revocation handling.  
- Data subject rights (export/redaction) – document process (post-MVP if needed).  
- Configurable data retention per category; documented archival/deletion.

## Audit & Monitoring
- Immutable audit log (who, what, when, before/after, IP).  
- Admin access logs and periodic review.  
- Alerting for repeated failed logins and privilege changes.

## Backup & Recovery
- Encrypted backups; stored offsite or separate storage.  
- Regular recovery drills; RPO/RTO targets agreed with stakeholders.

## Secure Development Checklist (MVP)
- [ ] Password hashing configured with `password_hash()` and verified.  
- [ ] RBAC middleware on sensitive routes (custom PHP implementation).  
- [ ] Input validation + output encoding in all forms/pages (`filter_input()`, `htmlspecialchars()`).  
- [ ] CSRF tokens on write endpoints; CSP headers where applicable.  
- [ ] File upload hardening; antivirus scan if available; file type whitelisting.  
- [ ] HTTPS enforced in staging/prod; HSTS configured.  
- [ ] Audit log append-only; tamper-evident (in `/features/shared/lib/audit/`).  
- [ ] Backups encrypted; restore test performed.
- [ ] PDO with prepared statements for all database queries.
- [ ] Session configuration: `session.cookie_httponly=1`, `session.cookie_secure=1` (in prod).