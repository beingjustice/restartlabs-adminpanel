<?php
/**
 * Change Admin Password API
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
$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validate input
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

// Check if new password matches confirmation
if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match']);
    exit;
}

// Validate password strength
if (strlen($newPassword) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters long']);
    exit;
}

// Security checks
$ip = getClientIP();

// Check for SQL injection
if (detectSQLInjection($currentPassword) || detectSQLInjection($newPassword)) {
    logAttack($ip, 'SQL Injection Attempt', 'Password change', 'high', '/admin/settings');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Verify current password and update
try {
    global $pdo;
    $adminId = $_SESSION['admin_id'];
    
    // Get current password hash
    $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Admin not found']);
        exit;
    }
    
    // Verify current password
    if (!password_verify($currentPassword, $admin['password'])) {
        logAttack($ip, 'Failed Password Change Attempt', "Admin ID: $adminId", 'medium', '/admin/settings');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Current password is incorrect']);
        exit;
    }
    
    // Check if new password is same as current
    if (password_verify($newPassword, $admin['password'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'New password must be different from current password']);
        exit;
    }
    
    // Hash new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
    $stmt->execute([$hashedPassword, $adminId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);
} catch(PDOException $e) {
    error_log("Password change error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>

