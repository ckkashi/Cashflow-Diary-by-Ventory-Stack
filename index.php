<?php
require_once 'includes/config_check.php';
session_start();

// Helper to get base path (if project is in a subdirectory)
$base_path = '/cashflow'; // Adjust if needed
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove base path from the URL
if (strpos($path, $base_path) === 0) {
    $path = substr($path, strlen($base_path));
}

// Ensure path starts with /
if (empty($path)) {
    $path = '/';
}

// Clean trailing slash (except for /)
if ($path != '/' && substr($path, -1) == '/') {
    $path = rtrim($path, '/');
}

// Router Mapping
$routes = [
    '/' => 'views/landing.php',
    '/login' => 'auth/login.php',
    '/register' => 'auth/register.php',
    '/logout' => 'auth/logout.php',
    '/dashboard' => 'dashboard/index.php',
    '/businesses' => 'businesses/list.php',
    '/businesses/add' => 'businesses/add.php',
    '/businesses/edit' => 'businesses/edit.php',
    '/businesses/switch' => 'businesses/switch.php',
    '/expenses' => 'expenses/list.php',
    '/expenses/add' => 'expenses/add.php',
    '/expenses/edit' => 'expenses/edit.php',
    '/income' => 'income/list.php',
    '/income/add' => 'income/add.php',
    '/income/edit' => 'income/edit.php',
    '/udhaar' => 'udhaar/list.php',
    '/udhaar/add' => 'udhaar/add.php',
    '/udhaar/view' => 'udhaar/view.php',
    '/contacts' => 'contacts/list.php',
    '/contacts/add' => 'contacts/add.php',
    '/contacts/edit' => 'contacts/edit.php',
    '/reports' => 'reports/index.php',
    '/reports/expenses' => 'reports/expenses.php',
    '/reports/income' => 'reports/income.php',
    '/reports/udhaar' => 'reports/udhaar.php',
    '/about' => 'about.php',
    '/privacy' => 'privacy.php',
    '/terms' => 'terms.php',
];

// Language Setup (common for all routes)
$current_lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $current_lang;

// Load language data
$lang_file = "languages/{$current_lang}.php";
if (file_exists($lang_file)) {
    $lang_data = require $lang_file;
}

// Include helpers
require_once 'includes/helpers.php';

// Check if route exists
if (isset($routes[$path])) {
    $file = $routes[$path];
    
    // For the landing page, we check session inside
    if ($path == '/') {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . $base_path . '/dashboard');
            exit;
        }
    }

    if (file_exists($file)) {
        require_once $file;
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo "404 - Page Not Found (" . htmlspecialchars($path) . ")";
?>
