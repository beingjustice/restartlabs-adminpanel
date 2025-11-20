<?php
/**
 * Change Admin Username API
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/security.php';

// Check if logged in
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get input
$newUsername = trim($_POST['new_username'] ?? '');
$currentPassword = $_POST['current_password'] ?? '';

// Validate input
if (empty($newUsername) || empty($currentPassword)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and current password are required']);
    exit;
}

// Validate username format
if (strlen($newUsername) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username must be at least 3 characters long']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_]+$/', $newUsername)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username can only contain letters, numbers, and underscores']);
    exit;
}

// Security checks
$ip = getClientIP();

// Check for SQL injection
if (detectSQLInjection($newUsername) || detectSQLInjection($currentPassword)) {
    logAttack($ip, 'SQL Injection Attempt', 'Username change', 'high', '/admin/settings');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Verify current password and update username
try {
    global $pdo;
    $adminId = $_SESSION['admin_id'];
    
    // Get current admin data
    $stmt = $pdo->prepare("SELECT username, password FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $admin['password'])) {
        logAttack($ip, 'Failed Username Change Attempt', "Admin ID: $adminId", 'medium', '/admin/settings');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Check if new username is same as current
    if ($newUsername === $admin['username']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'New username must be different from current username']);
        exit;
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ? AND id != ?");
    $stmt->execute([$newUsername, $adminId]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Username already exists']);
        exit;
    }
    
    // Update username
    $stmt = $pdo->prepare("UPDATE admins SET username = ? WHERE id = ?");
    $stmt->execute([$newUsername, $adminId]);
    
    // Update session
    $_SESSION['admin_username'] = $newUsername;
    
    echo json_encode([
        'success' => true,
        'message' => 'Username changed successfully',
        'new_username' => $newUsername
    ]);
} catch(PDOException $e) {
    error_log("Username change error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>

