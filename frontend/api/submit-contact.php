<?php
/**
 * Contact Form Submission API
 * Handles contact form submissions from get-help page
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/security.php';
require_once __DIR__ . '/../../includes/whatsapp-sender.php';
require_once __DIR__ . '/../../includes/email-sender.php';

// Get IP address
$ip = getClientIP();

// Check if IP is blocked
if (isIPBlocked($ip)) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Access denied'
    ]);
    exit;
}

// Get and sanitize form data
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');
$phone = sanitizeInput($_POST['phone'] ?? '');
$company = sanitizeInput($_POST['company'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

// Validation
$errors = [];

if (empty($name)) {
    $errors[] = 'Name is required';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (empty($phone)) {
    $errors[] = 'Phone number is required';
}

if (empty($message)) {
    $errors[] = 'Message is required';
}

// Security checks
if (!empty($name) && (detectSQLInjection($name) || detectXSS($name))) {
    logAttack($ip, 'XSS/SQL Injection Attempt', 'Contact form - Name field', 'high', '/get-help');
    $errors[] = 'Invalid input detected';
}

if (!empty($message) && (detectSQLInjection($message) || detectXSS($message))) {
    logAttack($ip, 'XSS/SQL Injection Attempt', 'Contact form - Message field', 'high', '/get-help');
    $errors[] = 'Invalid input detected';
}

// Rate limiting
if (!checkRateLimit($ip, 'contact_submission', 5, 300)) { // Max 5 submissions per 5 minutes
    logAttack($ip, 'Rate Limit Exceeded', 'Too many contact form submissions', 'medium', '/get-help');
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Too many requests. Please try again later.'
    ]);
    exit;
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
    
    // Check if table exists, if not create it
    try {
        $stmt = $pdo->prepare("INSERT INTO contact_submissions 
                               (name, email, phone, company, message, ip_address) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $company, $message, $ip]);
        
        $submissionId = $pdo->lastInsertId();
    } catch(PDOException $e) {
        // Log detailed error
        error_log("Database insert error: " . $e->getMessage());
        error_log("SQL State: " . $e->getCode());
        throw $e; // Re-throw to be caught by outer catch
    }
    
    // Update daily stats
    $today = date('Y-m-d');
    try {
        // Check if today's stats exist
        $stmt = $pdo->prepare("SELECT id FROM daily_stats WHERE date = ?");
        $stmt->execute([$today]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE daily_stats SET contact_submissions = contact_submissions + 1 WHERE date = ?");
            $stmt->execute([$today]);
        } else {
            // Create new
            $stmt = $pdo->prepare("INSERT INTO daily_stats (date, contact_submissions) VALUES (?, 1)");
            $stmt->execute([$today]);
        }
    } catch(PDOException $e) {
        // Silently fail - don't break form submission
        error_log("Daily stats update error: " . $e->getMessage());
    }
    
    // Send email notification (always send - HTML formatted)
    $emailSent = sendHTMLEmailNotification($name, $email, $phone, $company, $message, $ip);
    
    // Send WhatsApp notification if enabled
    $whatsappSent = false;
    if (WHATSAPP_ENABLED && !empty(WHATSAPP_NUMBER)) {
        $whatsappSent = sendWhatsAppNotification($name, $email, $phone, $company, $message, $ip);
        
        if ($whatsappSent) {
            $stmt = $pdo->prepare("UPDATE contact_submissions SET whatsapp_sent = 1 WHERE id = ?");
            $stmt->execute([$submissionId]);
        }
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Thank you! Your message has been sent successfully. We will contact you soon.',
        'email_sent' => $emailSent,
        'whatsapp_sent' => $whatsappSent
    ]);
    
} catch(PDOException $e) {
    error_log("Contact form error: " . $e->getMessage());
    error_log("Contact form error details: " . print_r($e->getTraceAsString(), true));
    error_log("SQL Error Code: " . $e->getCode());
    error_log("SQL Error Info: " . print_r($e->errorInfo, true));
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please check if the contact_submissions table exists.',
        'debug_error' => $e->getMessage() // For debugging - remove in production
    ]);
}
?>

