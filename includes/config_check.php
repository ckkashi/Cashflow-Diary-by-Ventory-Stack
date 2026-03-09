<?php
// includes/config_check.php

$config_file = __DIR__ . '/../config/config.php';

if (!file_exists($config_file)) {
    // If we are already in the install directory, don't redirect to avoid loop
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
?>
