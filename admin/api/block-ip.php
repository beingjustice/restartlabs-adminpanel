<?php
/**
 * Block IP API
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/security.php';

// Check authentication
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$ip = $_POST['ip'] ?? '';
$reason = $_POST['reason'] ?? '';

if (empty($ip)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'IP address is required']);
    exit;
}

// Validate IP format
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid IP address format']);
    exit;
}

// Block the IP
$result = blockIP($ip, $reason, $_SESSION['admin_id']);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'IP blocked successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to block IP']);
}
?>

