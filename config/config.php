<?php
/**
 * General Configuration
 * RestartLabs Admin Panel
 */

// Site Configuration
define('SITE_NAME', 'RestartLabs');
define('SITE_URL', 'http://localhost/restartlabs');
define('ADMIN_URL', SITE_URL . '/admin');

// Session Configuration
define('SESSION_NAME', 'restartlabs_admin');
define('SESSION_LIFETIME', 3600); // 1 hour

// Security Configuration
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes
define('CSRF_TOKEN_NAME', 'csrf_token');

// Visitor Tracking
define('TRACK_VISITORS', true);
define('BOT_DETECTION_ENABLED', true);
define('AUTO_BLOCK_SUSPICIOUS', false); // Set to true to auto-block suspicious IPs

// Contact Form
define('CONTACT_EMAIL', 'info@restartlab.com');
define('WHATSAPP_NUMBER', '+8801XXXXXXXXX'); // Update with your WhatsApp number
define('WHATSAPP_ENABLED', true);

// IP Geolocation (Free API)
define('GEO_API_URL', 'http://ip-api.com/json/');

// Timezone
date_default_timezone_set('Asia/Dhaka');

// Error Reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

