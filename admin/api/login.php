<?php
/**
 * Admin Login API
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/security.php';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => true, 'message' => 'Already logged in']);
    exit;
}

// Get input
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

// Security checks
$ip = getClientIP();

// Check for SQL injection
if (detectSQLInjection($username) || detectSQLInjection($password)) {
    logAttack($ip, 'SQL Injection Attempt', 'Admin login - Username/Password', 'high', '/admin');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Check rate limiting
if (!checkRateLimit($ip, 'admin_login', MAX_LOGIN_ATTEMPTS, LOGIN_LOCKOUT_TIME)) {
    logAttack($ip, 'Brute Force Attempt', 'Too many login attempts', 'critical', '/admin');
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many login attempts. Please try again later.']);
    exit;
}

// Verify credentials
try {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, username, password, email, full_name, is_active FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && $admin['is_active'] == 1 && password_verify($password, $admin['password'])) {
        // Login successful
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'] ?? $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['login_time'] = time();
        
        // Update last login
        $stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
        $stmt->execute([$admin['id']]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'admin' => [
                'username' => $admin['username'],
                'name' => $admin['full_name'] ?? $admin['username']
            ]
        ]);
    } else {
        // Log failed attempt
        logAttack($ip, 'Failed Login Attempt', "Username: $username", 'medium', '/admin');
        
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
} catch(PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>

