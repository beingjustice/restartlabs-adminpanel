<?php
/**
 * IP Blocker System
 * Handles IP blocking and unblocking
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/security.php';

/**
 * Check if current visitor's IP is blocked
 */
function checkIPBlock() {
    $ip = getClientIP();
    
    if (isIPBlocked($ip)) {
        http_response_code(403);
        header('Content-Type: text/html; charset=UTF-8');
        echo '<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            text-align: center; 
            padding: 50px; 
            background: #000; 
            color: #fff; 
        }
        h1 { color: #ff4444; }
    </style>
</head>
<body>
    <h1>Access Denied</h1>
    <p>Your IP address has been blocked.</p>
    <p>If you believe this is an error, please contact the administrator.</p>
</body>
</html>';
        exit;
    }
}

// Auto-check on include
if (!defined('SKIP_IP_CHECK')) {
    checkIPBlock();
}
?>

