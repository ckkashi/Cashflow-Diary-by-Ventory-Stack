<?php
require_once '../includes/config_check.php';
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$business_id = $_GET['id'] ?? null;

if ($business_id) {
    // Verify user owns this business
    $stmt = $pdo->prepare("SELECT id FROM businesses WHERE id = ? AND user_id = ?");
    $stmt->execute([$business_id, $user_id]);
    if ($stmt->fetch()) {
        $_SESSION['business_id'] = $business_id;
    }
}

// Redirect back to referring page or dashboard
$redirect = $_SERVER['HTTP_REFERER'] ?? '../dashboard/index.php';
header("Location: $redirect");
exit;
?>
