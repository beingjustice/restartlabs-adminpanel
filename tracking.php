<?php
/**
 * Visitor Tracking Include File
 * Include this at the top of HTML pages (before any output)
 * Usage: <?php require_once 'tracking.php'; ?>
 */

// Skip tracking for admin panel
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    return;
}

// Define skip check for IP blocker
define('SKIP_IP_CHECK', true);

require_once __DIR__ . '/includes/visitor-tracker.php';

// Track the visitor
$page = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? 'index';
trackVisitor($page);
?>

