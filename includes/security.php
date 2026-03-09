<?php
/**
 * Security Helpers for Cashflow Diary
 * XSS Protection, CSRF Tokens, and Session Security
 */

// 1. Secure Session Settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    // Using 1 for secure only if on HTTPS, but Laragon/local often isn't. 
    // Keeping it 0 for local compatibility unless requested otherwise.
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2. CSRF Protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Generate a hidden CSRF input field
 */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
}

/**
 * Validate CSRF token from POST request
 */
function validate_csrf()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die('CSRF token validation failed. Possible Cross-Site Request Forgery detected.');
        }
    }
}

// 3. XSS Protection
/**
 * Global escape helper for HTML output
 */
function e($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Ensure the requested resource belongs to the logged-in user and current business
 */
function authorize_user($resource_user_id, $resource_business_id = null)
{
    if ($resource_user_id != $_SESSION['user_id']) {
        header('Location: ../dashboard/index.php?error=unauthorized_access');
        exit;
    }
    if ($resource_business_id !== null && $resource_business_id != $_SESSION['business_id']) {
        header('Location: ../dashboard/index.php?error=unauthorized_business');
        exit;
    }
}
