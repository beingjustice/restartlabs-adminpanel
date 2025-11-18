<?php
/**
 * Update Contact Status API
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

$id = intval($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if ($id <= 0 || !in_array($status, ['new', 'read', 'replied'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    global $pdo;
    
    $stmt = $pdo->prepare("UPDATE contact_submissions SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} catch(PDOException $e) {
    error_log("Update contact status error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
}
?>

