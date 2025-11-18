<?php
/**
 * Save Settings API
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

// Get POST data
$whatsappNumber = sanitizeInput($_POST['whatsapp_number'] ?? '');
$whatsappEnabled = isset($_POST['whatsapp_enabled']) ? 1 : 0;
$contactEmail = sanitizeInput($_POST['contact_email'] ?? '');

// Validation
$errors = [];

if (!empty($whatsappNumber) && !preg_match('/^\+?[1-9]\d{1,14}$/', $whatsappNumber)) {
    $errors[] = 'Invalid WhatsApp number format. Use international format (e.g., +8801XXXXXXXXX)';
}

if (!empty($contactEmail) && !filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

// Save to database
try {
    global $pdo;
    
    // Check if settings table exists, create if not
    try {
        $pdo->query("SELECT 1 FROM settings LIMIT 1");
    } catch(PDOException $e) {
        // Table doesn't exist, create it
        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY AUTO_INCREMENT,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            updated_by INT,
            INDEX idx_key (setting_key)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
    
    // Save WhatsApp number
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, updated_by) 
                           VALUES ('whatsapp_number', ?, ?)
                           ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?, updated_at = NOW()");
    $stmt->execute([$whatsappNumber, $_SESSION['admin_id'], $whatsappNumber, $_SESSION['admin_id']]);
    
    // Save WhatsApp enabled
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, updated_by) 
                           VALUES ('whatsapp_enabled', ?, ?)
                           ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?, updated_at = NOW()");
    $stmt->execute([$whatsappEnabled, $_SESSION['admin_id'], $whatsappEnabled, $_SESSION['admin_id']]);
    
    // Save contact email
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, updated_by) 
                           VALUES ('contact_email', ?, ?)
                           ON DUPLICATE KEY UPDATE setting_value = ?, updated_by = ?, updated_at = NOW()");
    $stmt->execute([$contactEmail, $_SESSION['admin_id'], $contactEmail, $_SESSION['admin_id']]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Settings saved successfully!'
    ]);
    
} catch(PDOException $e) {
    error_log("Save settings error: " . $e->getMessage());
    error_log("SQL Error Code: " . $e->getCode());
    error_log("SQL Error Info: " . print_r($e->errorInfo, true));
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to save settings: ' . $e->getMessage(),
        'debug' => $e->getMessage() // Remove in production
    ]);
}
?>

