<?php
/**
 * Visitor Tracking Script
 * Include this at the top of all pages to track visitors
 */

// Skip tracking for admin panel
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    return;
}

// Define skip check for IP blocker
define('SKIP_IP_CHECK', true);

require_once __DIR__ . '/visitor-tracker.php';

// Track the visitor
$page = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? 'index';
trackVisitor($page);
?>

