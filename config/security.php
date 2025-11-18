<?php
/**
 * Security Functions
 * RestartLabs Admin Panel
 */

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/config.php';

/**
 * Get client IP address
 */
function getClientIP() {
    $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
               'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, 
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Check if IP is blocked
 */
function isIPBlocked($ip) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id FROM blocked_ips WHERE ip_address = ? AND is_active = 1");
        $stmt->execute([$ip]);
        return $stmt->fetch() !== false;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Block an IP address
 */
function blockIP($ip, $reason = '', $adminId = null) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO blocked_ips (ip_address, reason, blocked_by) 
                               VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE is_active = 1, reason = ?, blocked_at = NOW()");
        $stmt->execute([$ip, $reason, $adminId, $reason]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Unblock an IP address
 */
function unblockIP($ip) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("UPDATE blocked_ips SET is_active = 0 WHERE ip_address = ?");
        $stmt->execute([$ip]);
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Sanitize input
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Log attack attempt
 */
function logAttack($ip, $attackType, $description, $severity = 'medium', $pageUrl = '') {
    global $pdo;
    
    try {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt = $pdo->prepare("INSERT INTO attack_logs (ip_address, attack_type, description, page_url, severity, user_agent) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ip, $attackType, $description, $pageUrl, $severity, $userAgent]);
        
        // Auto-block if critical
        if ($severity === 'critical') {
            blockIP($ip, "Auto-blocked: $attackType", null);
        }
        
        return true;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Check for SQL injection attempts
 */
function detectSQLInjection($input) {
    $patterns = [
        '/(\bUNION\b.*\bSELECT\b)/i',
        '/(\bSELECT\b.*\bFROM\b)/i',
        '/(\bINSERT\b.*\bINTO\b)/i',
        '/(\bDELETE\b.*\bFROM\b)/i',
        '/(\bUPDATE\b.*\bSET\b)/i',
        '/(\bDROP\b.*\bTABLE\b)/i',
        '/(\bEXEC\b|\bEXECUTE\b)/i',
        '/(\bSCRIPT\b)/i',
        '/(--|\#|\/\*|\*\/)/',
        '/(\bOR\b.*=.*=)/i',
        '/(\bAND\b.*=.*=)/i',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check for XSS attempts
 */
function detectXSS($input) {
    $patterns = [
        '/<script[^>]*>.*?<\/script>/is',
        '/<iframe[^>]*>.*?<\/iframe>/is',
        '/javascript:/i',
        '/on\w+\s*=/i',
        '/<img[^>]+src[^>]*=.*javascript:/i',
        '/<svg[^>]*onload/i',
    ];
    
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Rate limiting check
 */
function checkRateLimit($ip, $action, $maxAttempts = 10, $timeWindow = 60) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM attack_logs 
                               WHERE ip_address = ? AND attack_type = ? 
                               AND detected_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$ip, $action, $timeWindow]);
        $result = $stmt->fetch();
        
        return $result['count'] < $maxAttempts;
    } catch(PDOException $e) {
        return true; // Allow if error
    }
}
?>

