# Users Register AuthService Documentation

## Overview
This document explains the `register()` method in the AuthService class, located at `features/shared/lib/auth/AuthService.php`. The AuthService handles the core business logic for user authentication, including account creation during registration.

## File Context
AuthService is a service class in the shared library, providing authentication functionality used across the application. It uses the Database class for data persistence and follows the project's security patterns.

Location: `features/shared/lib/auth/AuthService.php`

## register() Method

### Method Signature
```php
public function register($username, $email, $password, $role = 'user')
```

### Parameters
- **$username** (string): The desired username for the new account
- **$email** (string): The email address for the new account
- **$password** (string): The plain-text password (will be hashed)
- **$role** (string, optional): User role, defaults to 'user' (can be 'admin')

### Return Value
Returns an associative array with:
- **success** (boolean): True if registration succeeded, false otherwise
- **user_id** (int): The ID of the newly created user (only on success)
- **message** (string): Success message or error description

### Code Breakdown
```php
public function register($username, $email, $password, $role = 'user') {
    // Check if username or email already exists
    $existing = $this->db->fetchOne(
        "SELECT id FROM users WHERE username = ? OR email = ?",
        [$username, $email]
    );
    
    if ($existing) {
        return ['success' => false, 'message' => 'Username or email already exists'];
    }
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        $this->db->execute(
            "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)",
            [$username, $email, $hashedPassword, $role]
        );
        
        return [
            'success' => true,
            'user_id' => $this->db->lastInsertId(),
            'message' => 'Registration successful'
        ];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}
```

### Step-by-Step Explanation

1. **Duplicate Check**
   ```php
   $existing = $this->db->fetchOne(
       "SELECT id FROM users WHERE username = ? OR email = ?",
       [$username, $email]
   );
   
   if ($existing) {
       return ['success' => false, 'message' => 'Username or email already exists'];
   }
   ```
   - **Query**: Searches for existing users with same username OR email
   - **Prepared Statement**: Uses parameterized query for security
   - **Logic**: If any existing user found, registration fails
   - **Message**: Generic message (doesn't specify which field is duplicate for security)

2. **Password Hashing**
   ```php
   $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
   ```
   - **Function**: PHP's built-in `password_hash()`
   - **Algorithm**: PASSWORD_DEFAULT (currently bcrypt, may change with PHP updates)
   - **Security**: Creates secure, salted hash suitable for storage
   - **No manual salt**: `password_hash()` handles salting automatically

3. **Database Insertion**
   ```php
   try {
       $this->db->execute(
           "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)",
           [$username, $email, $hashedPassword, $role]
       );
   ```
   - **Try-Catch**: Wraps in exception handling for database errors
   - **Prepared Statement**: Prevents SQL injection
   - **Fields**: Inserts username, email, hashed password, and role
   - **Role**: Defaults to 'user', can be set to 'admin' for administrative accounts

4. **Success Response**
   ```php
   return [
       'success' => true,
       'user_id' => $this->db->lastInsertId(),
       'message' => 'Registration successful'
   ];
   ```
   - **Success Flag**: Indicates operation succeeded
   - **User ID**: Returns the auto-generated ID from the database
   - **Message**: Confirmation message

5. **Error Handling**
   ```php
   } catch (Exception $e) {
       return ['success' => false, 'message' => 'Registration failed'];
   }
   ```
   - **Catch**: Catches any database or other exceptions
   - **Generic Message**: Doesn't expose internal error details for security
   - **Logging**: In production, should log the actual exception for debugging

## Database Schema
The method assumes a `users` table with columns:
- `id` (auto-increment primary key)
- `username` (unique)
- `email` (unique)
- `password_hash` (varchar for hashed password)
- `role` (enum or varchar, default 'user')

## Related Files and Dependencies

### Database Layer
- **Database Class**: `features/shared/lib/database/Database.php` - Provides `fetchOne()`, `execute()`, `lastInsertId()`
- **Connection**: Database connection is established in Database class constructor

### Security
- **Session**: `features/shared/lib/auth/session.php` - Though not directly used here, related to auth flow
- **Password Hashing**: Uses PHP's built-in functions for secure password storage

### Usage in Controller
- **AuthController**: `features/users/shared/controllers/AuthController.php` - Calls this method in `handleRegister()`

## How to Modify

### Adding New User Fields
1. Update database schema to add new columns
2. Modify the INSERT query to include new fields
3. Update method parameters to accept new data
4. Update controller to pass the new data

### Changing Default Role
- Modify the default parameter: `public function register($username, $email, $password, $role = 'user')`

### Adding Email Verification
1. Generate verification token during registration
2. Store token in database (add column)
3. Send verification email
4. Add verification endpoint to confirm account

### Implementing User Activation
1. Add `active` column to users table (boolean, default false)
2. Set `active = 1` only after verification
3. Update login to check `active` status

### Custom Validation
- Add more sophisticated checks (e.g., email domain restrictions)
- Return specific error messages for different failure types

## Security Considerations
- **SQL Injection**: Uses prepared statements
- **Password Security**: Proper hashing with bcrypt
- **Duplicate Prevention**: Checks for existing username/email
- **Error Handling**: Generic error messages prevent information leakage
- **Role Assignment**: Careful control of role parameter (should validate allowed roles)

## Common Issues
- **Database Connection**: Ensure Database class is properly configured
- **Table Schema**: Verify users table exists with correct columns
- **Unique Constraints**: Database should have unique indexes on username and email
- **Exception Handling**: Check database logs for actual error details
- **Password Policy**: Ensure password meets minimum requirements before calling this method</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\users-register\service.md