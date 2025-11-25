# Users Register Module Documentation

## Overview
This documentation covers the complete user registration functionality in SulamProject. The registration system allows new users to create accounts with username, email, and password. It follows the feature-based architecture and implements security best practices.

## Module Structure
The registration functionality is split across multiple components:

- **View Layer**: `features/users/shared/views/register.php` - The HTML form
- **Controller Layer**: `features/users/shared/controllers/AuthController.php` - Request handling
- **Service Layer**: `features/shared/lib/auth/AuthService.php` - Business logic
- **Validation Layer**: `features/shared/lib/utilities/validation.php` - Input validation

## Architecture Flow

### Registration Process
1. **User visits `/register`**
   - `AuthController::showRegister()` displays the form
   - Includes CSRF protection and session management

2. **User submits form**
   - `AuthController::handleRegister()` processes the submission
   - Validates CSRF token and input data
   - Calls `AuthService::register()` for account creation

3. **Account creation**
   - `AuthService::register()` checks for duplicates
   - Hashes password securely
   - Inserts user into database
   - Logs audit event

4. **Response**
   - Success: Redirects to login with success message
   - Failure: Returns to form with error message

## Key Features

### Security Measures
- **CSRF Protection**: Tokens prevent cross-site request forgery
- **Password Hashing**: Uses bcrypt (PASSWORD_DEFAULT) for secure storage
- **Input Validation**: Both client-side (HTML5) and server-side validation
- **SQL Injection Prevention**: Prepared statements throughout
- **Session Security**: Secure session configuration
- **Audit Logging**: All registrations are logged

### Validation Rules
- **Username**: 3-20 characters, letters/numbers/underscore only
- **Email**: Valid email format (RFC 5322 compliant)
- **Password**: Minimum 8 characters
- **Confirmation**: Password must match confirmation field

### User Experience
- **Real-time feedback**: Client-side validation with helpful messages
- **Error handling**: Clear error messages for validation failures
- **Success flow**: Redirects to login after successful registration
- **Duplicate handling**: Graceful handling of existing usernames/emails

## File Relationships

### Core Files
```
features/users/shared/
├── views/register.php                    # Registration form HTML
├── controllers/AuthController.php        # Request handling logic
└── assets/css/                           # (No specific CSS, uses global)

features/shared/lib/
├── auth/AuthService.php                  # Account creation logic
├── utilities/validation.php              # Input validation functions
├── utilities/csrf.php                    # CSRF token handling
├── utilities/functions.php               # Helper functions (e(), redirect())
├── audit/audit-log.php                   # Audit logging
└── database/Database.php                 # Database operations
```

### Routing
- **URL**: `/register` (GET for form, POST for submission)
- **Controller Methods**:
  - `AuthController::showRegister()` - GET `/register`
  - `AuthController::handleRegister()` - POST `/register`

## Database Schema
The registration system expects a `users` table with:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `username` (VARCHAR, UNIQUE)
- `email` (VARCHAR, UNIQUE)
- `password_hash` (VARCHAR) - Stores bcrypt hash
- `role` (VARCHAR, DEFAULT 'user')
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

## Configuration Requirements

### PHP Extensions
- `mysqli` or `pdo_mysql` for database connectivity
- `password_hash()` and `password_verify()` (PHP 5.5+)

### Session Configuration
- Secure session settings in `features/shared/lib/auth/session.php`
- `session.cookie_httponly = 1`
- `session.cookie_secure = 1` (if HTTPS)
- `session.use_strict_mode = 1`

### Database
- MySQL/MariaDB with InnoDB engine
- Proper charset (utf8mb4 recommended)
- Unique constraints on username and email columns

## Common Modification Scenarios

### Adding New Fields
1. Update the view (`register.php`) to include new input fields
2. Modify `AuthController::handleRegister()` to collect and validate new data
3. Update `AuthService::register()` to accept and store new fields
4. Alter database schema to add new columns

### Implementing Email Verification
1. Add verification token generation in `AuthService::register()`
2. Create email sending functionality
3. Add verification endpoint (`/verify-email`)
4. Update login to check verification status

### Adding Role Selection
1. Modify view to include role selection (if allowed)
2. Update controller validation for role values
3. Pass role parameter to service (with proper authorization checks)

### Strengthening Password Requirements
1. Update `validatePassword()` function with new rules
2. Modify client-side validation attributes
3. Consider password strength indicator in view

## Security Checklist
- [ ] CSRF tokens implemented and verified
- [ ] Passwords hashed with secure algorithm
- [ ] Prepared statements used for all database queries
- [ ] Input validation on both client and server
- [ ] Error messages don't leak sensitive information
- [ ] Audit logging enabled for all registrations
- [ ] Session fixation protection in place
- [ ] Rate limiting considered for production
- [ ] HTTPS enforced for production deployment

## Testing Considerations
- **Unit Tests**: Test validation functions independently
- **Integration Tests**: Test full registration flow
- **Security Tests**: Attempt SQL injection, XSS, CSRF attacks
- **Edge Cases**: Test duplicate usernames, invalid emails, weak passwords
- **Database Tests**: Verify data persistence and constraints

## Troubleshooting
- **Registration fails silently**: Check database connection and error logs
- **CSRF errors**: Verify token generation and session storage
- **Validation inconsistencies**: Compare client and server rules
- **Email issues**: Check SMTP configuration if email verification added
- **Session problems**: Verify session configuration and cookie settings

## Related Documentation
- [Users Login Documentation](users-login-doc.md) - Login functionality
- [Security and Privacy](../../Security-and-Privacy.md) - Security guidelines
- [Feature-Based Structure](../../Feature-Based-Structure.md) - Architecture patterns
- [Database Schema](../../Architecture.md#database-schema) - Complete schema details</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\users-register\README.md