# Users Register Validation Documentation

## Overview
This document explains the validation functions used in the user registration process, located in `features/shared/lib/utilities/validation.php`. These functions provide server-side validation for user input during registration.

## File Context
The validation.php file contains reusable validation utilities used throughout the application. For registration, we use `validateUsername()`, `validateEmail()`, and `validatePassword()`.

Location: `features/shared/lib/utilities/validation.php`

## validateUsername() Function

### Purpose
Validates username format and length requirements for user accounts.

### Code
```php
function validateUsername($username) {
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        return ['valid' => false, 'message' => 'Username must be 3-20 characters (letters, numbers, underscore only)'];
    }
    return ['valid' => true];
}
```

### Parameters
- **$username** (string): The username to validate

### Return Value
- **Array**: `['valid' => boolean, 'message' => string]` (message only present if invalid)

### Validation Rules
- **Length**: 3-20 characters
- **Characters**: Only letters (a-z, A-Z), numbers (0-9), and underscore (_)
- **Regex**: `/^[a-zA-Z0-9_]{3,20}$/`
  - `^` - Start of string
  - `[a-zA-Z0-9_]` - Character class allowing letters, numbers, underscore
  - `{3,20}` - Between 3 and 20 of the preceding characters
  - `$` - End of string

### Usage in Registration
Called in `AuthController::handleRegister()` to validate the username before attempting registration.

## validateEmail() Function

### Purpose
Validates email address format using PHP's built-in email validation.

### Code
```php
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    return ['valid' => true];
}
```

### Parameters
- **$email** (string): The email address to validate

### Return Value
- **Array**: `['valid' => boolean, 'message' => string]` (message only if invalid)

### Validation Rules
- Uses `filter_var()` with `FILTER_VALIDATE_EMAIL`
- Follows RFC 5322 email format standards
- Allows standard email formats like `user@example.com`
- Rejects malformed addresses

### Usage in Registration
Validates the email field in the registration form.

## validatePassword() Function

### Purpose
Validates minimum password requirements.

### Code
```php
function validatePassword($password) {
    if (strlen($password) < 8) {
        return ['valid' => false, 'message' => 'Password must be at least 8 characters'];
    }
    return ['valid' => true];
}
```

### Parameters
- **$password** (string): The password to validate

### Return Value
- **Array**: `['valid' => boolean, 'message' => string]` (message only if invalid)

### Validation Rules
- **Minimum Length**: 8 characters
- **Current Implementation**: Only checks length, but can be extended

### Usage in Registration
Validates the password field before hashing and storing.

## Related Functions (Used Elsewhere)

### validateRequired()
```php
function validateRequired($value, $fieldName = 'Field') {
    if (empty(trim($value))) {
        return ['valid' => false, 'message' => "$fieldName is required"];
    }
    return ['valid' => true];
}
```
- Checks if a field is not empty after trimming whitespace

### validateLength()
```php
function validateLength($value, $min, $max, $fieldName = 'Field') {
    $length = strlen($value);
    if ($length < $min || $length > $max) {
        return ['valid' => false, 'message' => "$fieldName must be between $min and $max characters"];
    }
    return ['valid' => true];
}
```
- Validates string length between min and max values

### sanitizeInput()
```php
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
```
- Trims whitespace and escapes HTML special characters

### validateAndSanitize()
```php
function validateAndSanitize($value, $validators = []) {
    foreach ($validators as $validator) {
        $result = $validator($value);
        if (!$result['valid']) {
            return $result;
        }
    }
    return ['valid' => true, 'value' => sanitizeInput($value)];
}
```
- Runs multiple validators and sanitizes if all pass

## How to Modify

### Strengthening Password Validation
Current password validation only checks length. To add complexity requirements:

```php
function validatePassword($password) {
    if (strlen($password) < 8) {
        return ['valid' => false, 'message' => 'Password must be at least 8 characters'];
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one uppercase letter'];
    }
    if (!preg_match('/[a-z]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one lowercase letter'];
    }
    if (!preg_match('/[0-9]/', $password)) {
        return ['valid' => false, 'message' => 'Password must contain at least one number'];
    }
    return ['valid' => true];
}
```

### Adding Username Restrictions
To prevent reserved usernames:

```php
function validateUsername($username) {
    $reserved = ['admin', 'root', 'system', 'user'];
    if (in_array(strtolower($username), $reserved)) {
        return ['valid' => false, 'message' => 'This username is not available'];
    }
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        return ['valid' => false, 'message' => 'Username must be 3-20 characters (letters, numbers, underscore only)'];
    }
    return ['valid' => true];
}
```

### Email Domain Restrictions
To restrict to specific domains:

```php
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    $allowedDomains = ['company.com', 'example.org'];
    $domain = substr(strrchr($email, "@"), 1);
    if (!in_array($domain, $allowedDomains)) {
        return ['valid' => false, 'message' => 'Email must be from an allowed domain'];
    }
    return ['valid' => true];
}
```

## Security Considerations
- **Server-side validation**: Always validate on server, don't rely on client-side
- **Consistent with client**: Keep server rules matching HTML5 validation attributes
- **Error messages**: Generic enough to not leak information about valid formats
- **Sanitization**: Always sanitize output, not just validate input

## Common Issues
- **Regex patterns**: Test thoroughly with various inputs
- **Unicode support**: Current patterns may not handle international characters
- **Performance**: Complex regex can impact performance with many validations
- **Maintenance**: Keep validation rules synchronized between client and server</content>
<parameter name="filePath">c:\laragon\www\sulamprojectex\context-docs\contexts\users-register\validation.md