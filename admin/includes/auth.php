<?php
/**
 * Authentication Check
 * Include this at the top of all admin pages
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';

// Define skip IP check for admin panel
define('SKIP_IP_CHECK', true);

require_once __DIR__ . '/../../config/security.php';

session_start();

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

// Check session timeout (only if SESSION_LIFETIME is defined)
if (defined('SESSION_LIFETIME') && isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
    session_destroy();
    header('Location: index.php?expired=1');
    exit;
}

// Update session time on activity
$_SESSION['login_time'] = time();

// Get admin info
$adminId = $_SESSION['admin_id'];
$adminUsername = $_SESSION['admin_username'] ?? 'Admin';
$adminName = $_SESSION['admin_name'] ?? $adminUsername;
$adminEmail = $_SESSION['admin_email'] ?? '';
?>

