# Users Register Controller Documentation

## Overview
This document explains the register-related methods in the AuthController, located at `features/users/shared/controllers/AuthController.php`. The AuthController handles authentication logic, including user registration. We'll focus on the `showRegister()` and `handleRegister()` methods that manage the registration process.

## File Context
The AuthController is part of the users feature's shared controllers. It uses dependency injection for AuthService and AuditLog, following the project's architecture patterns.

Location: `features/users/shared/controllers/AuthController.php`

## Class Structure
```php
class AuthController {
    private $authService;
    private $auditLog;
    
    public function __construct() {
        $this->authService = new AuthService();
        $this->auditLog = new AuditLog();
    }
    // ... methods
}
```

### Dependencies
- **AuthService**: Handles core authentication logic (login, register, logout).
- **AuditLog**: Logs security events like user creation.

## showRegister() Method

### Purpose
Displays the registration form to the user. This method is called when someone visits the `/register` URL.

### Code Breakdown
```php
public function showRegister() {
    initSecureSession();
    
    // Redirect if already logged in
    if (isAuthenticated()) {
        redirect('/dashboard');
    }
    
    $message = $_SESSION['message'] ?? '';
    unset($_SESSION['message']);
    
    $csrfToken = generateCsrfToken();
    
    ob_start();
    include __DIR__ . '/../views/register.php';
    $content = ob_get_clean();
    
    $pageTitle = 'Register';
    include __DIR__ . '/../../../shared/components/layouts/base.php';
}
```

### Step-by-Step Explanation

1. **Session Initialization**
   ```php
   initSecureSession();
   ```
   - **Function**: From `features/shared/lib/auth/session.php`
   - **Purpose**: Starts or resumes a secure PHP session with proper security settings (httponly, secure, etc.)
   - **Why**: Ensures session security for handling user data

2. **Authentication Check**
   ```php
   if (isAuthenticated()) {
       redirect('/dashboard');
   }
   ```
   - **Function**: `isAuthenticated()` from `features/shared/lib/auth/AuthService.php` or utilities
   - **Purpose**: Prevents logged-in users from accessing registration
   - **Logic**: If user is already logged in, redirect to dashboard

3. **Message Handling**
   ```php
   $message = $_SESSION['message'] ?? '';
   unset($_SESSION['message']);
   ```
   - **Purpose**: Retrieves and clears any flash messages (errors/success from previous attempts)
   - **Logic**: Uses null coalescing (`??`) for safe access, then unsets to prevent re-display

4. **CSRF Token Generation**
   ```php
   $csrfToken = generateCsrfToken();
   ```
   - **Function**: From `features/shared/lib/utilities/csrf.php`
   - **Purpose**: Creates a unique token to prevent cross-site request forgery
   - **Storage**: Token is stored in session for later verification

5. **View Rendering**
   ```php
   ob_start();
   include __DIR__ . '/../views/register.php';
   $content = ob_get_clean();
   ```
   - **Output Buffering**: `ob_start()` captures the included file's output
   - **Include Path**: `../views/register.php` - the registration form template
   - **Content Capture**: `ob_get_clean()` gets the buffered content and cleans the buffer
   - **Variables Available**: `$message`, `$csrfToken` are available to the included view

6. **Layout Rendering**
   ```php
   $pageTitle = 'Register';
   include __DIR__ . '/../../../shared/components/layouts/base.php';
   ```
   - **Page Title**: Sets the HTML title and heading
   - **Layout Include**: Base layout wraps the content with HTML structure, navigation, footer
   - **Path**: Goes up to shared components directory

## handleRegister() Method

### Purpose
Processes the registration form submission. Validates input, creates the user account, and handles success/failure.

### Code Breakdown
```php
public function handleRegister() {
    initSecureSession();
    
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect('/register');
    }
    
    // Verify CSRF token
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['message'] = 'Invalid request. Please try again.';
        redirect('/register');
    }
    
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    $errors = [];
    
    $usernameValidation = validateUsername($username);
    if (!$usernameValidation['valid']) {
        $errors[] = $usernameValidation['message'];
    }
    
    $emailValidation = validateEmail($email);
    if (!$emailValidation['valid']) {
        $errors[] = $emailValidation['message'];
    }
    
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        $errors[] = $passwordValidation['message'];
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (!empty($errors)) {
        $_SESSION['message'] = implode(' ', $errors);
        redirect('/register');
    }
    
    $result = $this->authService->register($username, $email, $password);
    
    if ($result['success']) {
        $this->auditLog->logCreate('user', $result['user_id']);
        $_SESSION['message'] = 'Registration successful! Please login.';
        redirect('/login');
    } else {
        $_SESSION['message'] = $result['message'];
        redirect('/register');
    }
}
```

### Step-by-Step Explanation

1. **Session and Method Check**
   ```php
   initSecureSession();
   if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
       redirect('/register');
   }
   ```
   - **Session**: Ensures secure session for handling form data
   - **Method Check**: Only accepts POST requests for security

2. **CSRF Verification**
   ```php
   if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
       $_SESSION['message'] = 'Invalid request. Please try again.';
       redirect('/register');
   }
   ```
   - **Function**: `verifyCsrfToken()` from csrf.php
   - **Purpose**: Validates the token matches what's in session
   - **Failure**: Sets error message and redirects back

3. **Input Collection**
   ```php
   $username = trim($_POST['username'] ?? '');
   $email = trim($_POST['email'] ?? '');
   $password = $_POST['password'] ?? '';
   $confirmPassword = $_POST['confirm_password'] ?? '';
   ```
   - **Trim**: Removes whitespace from username and email
   - **Null Coalescing**: Safe access to POST data
   - **Variables**: Store form inputs for validation

4. **Validation Section**
   ```php
   $errors = [];
   
   $usernameValidation = validateUsername($username);
   if (!$usernameValidation['valid']) {
       $errors[] = $usernameValidation['message'];
   }
   // ... similar for email and password
   ```
   - **Validation Functions**: From `features/shared/lib/utilities/validation.php`
   - **Structure**: Each returns array with 'valid' boolean and 'message' string
   - **Error Collection**: Builds array of error messages

5. **Password Confirmation**
   ```php
   if ($password !== $confirmPassword) {
       $errors[] = 'Passwords do not match.';
   }
   ```
   - **Logic**: Simple string comparison
   - **Purpose**: Ensures user typed password correctly

6. **Error Handling**
   ```php
   if (!empty($errors)) {
       $_SESSION['message'] = implode(' ', $errors);
       redirect('/register');
   }
   ```
   - **Condition**: If any validation failed
   - **Message**: Joins all errors with spaces
   - **Redirect**: Back to form with error message

7. **User Registration**
   ```php
   $result = $this->authService->register($username, $email, $password);
   ```
   - **Service Call**: Delegates to AuthService
   - **Parameters**: Validated username, email, password
   - **Return**: Array with 'success' boolean and either 'user_id' or 'message'

8. **Success Handling**
   ```php
   if ($result['success']) {
       $this->auditLog->logCreate('user', $result['user_id']);
       $_SESSION['message'] = 'Registration successful! Please login.';
       redirect('/login');
   }
   ```
   - **Audit Log**: Records the user creation event
   - **Message**: Success message for login page
   - **Redirect**: To login page (not dashboard, since not logged in yet)

9. **Failure Handling**
   ```php
   else {
       $_SESSION['message'] = $result['message'];
       redirect('/register');
   }
   ```
   - **Message**: Error from AuthService (e.g., duplicate username)
   - **Redirect**: Back to registration form

## Related Files

### Core Dependencies
- **AuthService**: `features/shared/lib/auth/AuthService.php` - `register()` method
- **AuditLog**: `features/shared/lib/audit/audit-log.php` - `logCreate()` method
- **Session**: `features/shared/lib/auth/session.php` - `initSecureSession()`
- **CSRF**: `features/shared/lib/utilities/csrf.php` - token functions
- **Validation**: `features/shared/lib/utilities/validation.php` - validation functions
- **Functions**: `features/shared/lib/utilities/functions.php` - `redirect()`, `isAuthenticated()`

### Views and Layouts
- **Register View**: `features/users/shared/views/register.php`
- **Base Layout**: `features/shared/components/layouts/base.php`

## How to Modify

### Adding New Validation
1. Create validation function in `validation.php`
2. Call it in `handleRegister()` and add to `$errors` array
3. Update the view if needed (add fields, change patterns)

### Changing Redirects
- Edit the `redirect()` calls for different post-registration flow
- For success, could redirect to login or auto-login

### Adding Fields
1. Update view to include new input
2. Modify `handleRegister()` to collect and validate new field
3. Update `AuthService::register()` to handle new data
4. Modify database schema if needed

### Security Enhancements
- Add rate limiting for registration attempts
- Implement email verification before account activation
- Add captcha for bot prevention

## Common Issues
- **CSRF failures**: Check token generation/storage in session
- **Validation inconsistencies**: Ensure client and server validation match
- **Database errors**: Check AuthService and database connection
- **Session issues**: Verify session configuration in php.ini or session.php</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\users-register\controller.md