<?php
require_once 'includes/config_check.php';

// If already logged in, redirect to dashboard
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard/index.php');
    exit;
}

header('Location: /cashflow/login');
exit;
?>
