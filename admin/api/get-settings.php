<?php
/**
 * Get Settings API
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

try {
    global $pdo;
    
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Return settings with defaults
    echo json_encode([
        'success' => true,
        'settings' => [
            'whatsapp_number' => $settings['whatsapp_number'] ?? WHATSAPP_NUMBER,
            'whatsapp_enabled' => isset($settings['whatsapp_enabled']) ? (int)$settings['whatsapp_enabled'] : (WHATSAPP_ENABLED ? 1 : 0),
            'contact_email' => $settings['contact_email'] ?? CONTACT_EMAIL
        ]
    ]);
    
} catch(PDOException $e) {
    error_log("Get settings error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to load settings'
    ]);
}
?>

