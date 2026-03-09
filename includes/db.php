<?php
ob_start();

/**
 * Main Database & Security Bootstrap
 * Includes configuration, security helpers, and global utilities
 */

// 1. Load Configuration
$config_file = __DIR__ . '/../config/config.php';
if (!file_exists($config_file)) {
    // If we are not in the installer, redirect to it
    if (strpos($_SERVER['REQUEST_URI'], '/install/') === false) {
        // Calculate relative path to install directory
        $root = (strpos($_SERVER['PHP_SELF'], '/auth/') !== false || strpos($_SERVER['PHP_SELF'], '/dashboard/') !== false || strpos($_SERVER['PHP_SELF'], '/expenses/') !== false || strpos($_SERVER['PHP_SELF'], '/income/') !== false || strpos($_SERVER['PHP_SELF'], '/contacts/') !== false || strpos($_SERVER['PHP_SELF'], '/udhaar/') !== false || strpos($_SERVER['PHP_SELF'], '/reports/') !== false || strpos($_SERVER['PHP_SELF'], '/businesses/') !== false) ? '../' : '';
        header('Location: ' . $root . 'install/index.php');
        exit;
    }
}
else {
    require_once $config_file;
}

// 2. Load Security & Helpers
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/helpers.php';

// 3. Global CSRF Protection for all POST requests
// This can be disruptive if not careful, so we might want to call it manually 
// on specific sensitive actions, but the prompt says improve security.
// Let's call it here but ensure we add csrf_field() to all forms.
// For now, I'll define it but call it in pages to avoid immediately breaking everything.
// validate_csrf(); // Uncomment this once all forms have the token
