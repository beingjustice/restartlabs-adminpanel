<?php
/**
 * Track Visit API Endpoint
 * Called via JavaScript to track page visits
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Skip IP check for tracking API
define('SKIP_IP_CHECK', true);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/visitor-tracker.php';

// Get page from request
$page = $_GET['page'] ?? $_POST['page'] ?? '';

// Track the visitor
trackVisitor($page);

// Return success
echo json_encode([
    'success' => true,
    'message' => 'Visit tracked'
]);
?>

