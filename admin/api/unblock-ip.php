<?php
/**
 * Unblock IP API
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

if (empty($ip)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'IP address is required']);
    exit;
}

// Unblock the IP
$result = unblockIP($ip);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'IP unblocked successfully']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to unblock IP']);
}
?>

