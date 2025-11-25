# Users Register View Documentation

## Overview
This document explains the register view file located at `features/users/shared/views/register.php`. This is the HTML template that displays the user registration form in the SulamProject application. It's a simple, self-contained view that renders the registration form with proper validation and security measures.

## File Structure and Purpose
The register view is part of the users feature module, specifically in the shared section because registration is available to all users (both admin and regular users can register, though in practice, registration might be restricted based on business rules).

Location: `features/users/shared/views/register.php`

## Code Breakdown

### HTML Structure
```php
<main class="centered small-card">
```
- `<main>`: The main content area of the page
- `class="centered small-card"`: CSS classes that center the content and apply a small card layout. These classes are defined in the global stylesheet at `assets/css/style.css`.

### Heading
```php
<h2>Register</h2>
```
- Simple heading that displays "Register" at the top of the form.

### Message Display
```php
<?php if (!empty($message)): ?>
    <div class="notice <?php echo strpos($message, 'successful') !== false ? 'success' : 'error'; ?>">
        <?php echo e($message); ?>
    </div>
<?php endif; ?>
```
- **Purpose**: Displays success or error messages to the user after form submission.
- **Logic**:
  - Checks if `$message` variable is not empty (set by the controller).
  - If the message contains 'successful', adds 'success' class; otherwise 'error'.
  - Uses `e()` function (from `features/shared/lib/utilities/functions.php`) to escape HTML output for security.
- **Styling**: The 'notice' class styles it as a notification box, with 'success' or 'error' modifying the color (green for success, red for error).

### Form Element
```php
<form method="post" action="/sulamproject/register">
```
- **Method**: POST - sends data securely (not visible in URL).
- **Action**: Submits to `/sulamproject/register` - this routes to the `AuthController::handleRegister()` method.
- **Note**: The action URL should match your local setup; in development, it might be `/sulamprojectex/register`.

### CSRF Protection
```php
<?php echo csrfField(); ?>
```
- **Purpose**: Prevents Cross-Site Request Forgery attacks.
- **Function**: `csrfField()` is defined in `features/shared/lib/utilities/csrf.php`. It generates a hidden input field with a CSRF token that the server validates on submission.

### Username Field
```php
<label>
    Username
    <input type="text" name="username" required autofocus pattern="[a-zA-Z0-9_]{3,20}" 
           title="3-20 characters, letters, numbers, and underscore only">
</label>
```
- **Label**: "Username" - describes the field.
- **Input attributes**:
  - `type="text"`: Standard text input.
  - `name="username"`: The key used in `$_POST['username']` on the server.
  - `required`: Browser validation - field must be filled.
  - `autofocus`: Automatically focuses this field when the page loads.
  - `pattern="[a-zA-Z0-9_]{3,20}"`: Regex validation - only letters, numbers, underscores, 3-20 characters.
  - `title`: Tooltip text shown if pattern doesn't match.

### Email Field
```php
<label>
    Email
    <input type="email" name="email" required>
</label>
```
- **Input attributes**:
  - `type="email"`: Browser provides email validation and mobile keyboard.
  - `name="email"`: Server receives as `$_POST['email']`.
  - `required`: Must be filled.

### Password Field
```php
<label>
    Password
    <input type="password" name="password" required minlength="8">
</label>
```
- **Input attributes**:
  - `type="password"`: Masks input characters.
  - `name="password"`: Server receives as `$_POST['password']`.
  - `required`: Must be filled.
  - `minlength="8"`: Browser validation - minimum 8 characters.

### Confirm Password Field
```php
<label>
    Confirm Password
    <input type="password" name="confirm_password" required minlength="8">
</label>
```
- **Purpose**: Ensures user types password correctly twice.
- **Attributes**: Same as password field, but `name="confirm_password"`.
- **Server validation**: Controller checks if `password` matches `confirm_password`.

### Actions Section
```php
<div class="actions">
    <button class="btn" type="submit">Create Account</button>
    <a class="btn outline" href="/sulamproject/login">Back to Login</a>
</div>
```
- **Button**: Submits the form. Class "btn" styles it as a primary button.
- **Link**: "Back to Login" - navigates to login page. Class "btn outline" styles it as a secondary button.
- **Styling**: "actions" class (from `assets/css/style.css`) arranges buttons horizontally.

## Related Files and Dependencies

### Controller
- **File**: `features/users/shared/controllers/AuthController.php`
- **Method**: `showRegister()` - includes this view and passes variables like `$message` and `$csrfToken`.
- **Method**: `handleRegister()` - processes the form submission.

### Utilities
- **CSRF**: `features/shared/lib/utilities/csrf.php` - `csrfField()` and `verifyCsrfToken()`.
- **Functions**: `features/shared/lib/utilities/functions.php` - `e()` for HTML escaping, `redirect()`.
- **Validation**: `features/shared/lib/utilities/validation.php` - Server-side validation functions.

### Layout
- **File**: `features/shared/components/layouts/base.php`
- **Purpose**: Wraps this view with common HTML structure (DOCTYPE, head, body, footer).

### Styles
- **Global**: `assets/css/style.css` - Defines classes like `.centered`, `.small-card`, `.notice`, `.btn`, etc.
- **No specific CSS**: Unlike login, register doesn't have its own stylesheet.

### Authentication Service
- **File**: `features/shared/lib/auth/AuthService.php`
- **Method**: `register()` - Actually creates the user account in the database.

## How to Modify

### Adding a New Field
1. Add the HTML input in the form (with appropriate name, validation).
2. Update `AuthController::handleRegister()` to validate and process the new field.
3. Update `AuthService::register()` if database changes are needed.
4. Update validation functions in `validation.php` if custom validation required.

### Changing Validation Rules
- **Client-side**: Modify `pattern`, `minlength`, etc. on inputs.
- **Server-side**: Edit validation in `AuthController::handleRegister()` or `validation.php`.

### Adding Styles
- **Global changes**: Edit `assets/css/style.css`.
- **Register-specific**: Create `features/users/shared/assets/css/register.css` and include it in `AuthController::showRegister()` like login does.

### Changing Messages
- Edit the strings in `AuthController::handleRegister()` where `$_SESSION['message']` is set.

## Security Considerations
- CSRF protection via tokens.
- Password confirmation to prevent typos.
- Server-side validation in addition to client-side.
- HTML output escaping with `e()`.
- Prepared statements used in database operations (in AuthService).

## Common Issues
- **Form not submitting**: Check CSRF token generation/verification.
- **Validation errors**: Ensure server-side validation matches client-side patterns.
- **Styling issues**: Verify CSS classes are defined in `style.css`.
- **Routing problems**: Confirm the action URL matches your routing setup.</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\users-register\view.md