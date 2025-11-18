<?php
/**
 * Visitor Tracking System
 * Tracks all visitors with detailed information
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/bot-detector.php';

/**
 * Track visitor
 */
function trackVisitor($page = '') {
    if (!TRACK_VISITORS) {
        return;
    }
    
    global $pdo;
    
    // Get visitor information
    $ip = getClientIP();
    
    // Check if IP is blocked (skip if SKIP_IP_CHECK is defined)
    if (!defined('SKIP_IP_CHECK') && isIPBlocked($ip)) {
        http_response_code(403);
        die('Access Denied');
    }
    
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $referrer = $_SERVER['HTTP_REFERER'] ?? '';
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    
    if (empty($page)) {
        $page = parse_url($requestUri, PHP_URL_PATH) ?? 'index';
    }
    
    // Generate or get session ID
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['visitor_session_id'])) {
        $_SESSION['visitor_session_id'] = bin2hex(random_bytes(16));
    }
    $sessionId = $_SESSION['visitor_session_id'];
    
    // Detect if bot
    $isBot = BOT_DETECTION_ENABLED ? isBot($userAgent) : false;
    
    // Check for suspicious behavior
    $isSuspicious = false;
    if (!$isBot) {
        $isSuspicious = isSuspiciousBehavior($ip, $sessionId);
    }
    
    // Get device info
    $deviceType = getDeviceType($userAgent);
    $browser = getBrowser($userAgent);
    $os = getOS($userAgent);
    
    // Get geolocation (using free API)
    $country = 'Unknown';
    $city = 'Unknown';
    
    try {
        $geoData = @file_get_contents(GEO_API_URL . $ip . '?fields=status,country,city');
        if ($geoData) {
            $geo = json_decode($geoData, true);
            if (isset($geo['status']) && $geo['status'] === 'success') {
                $country = $geo['country'] ?? 'Unknown';
                $city = $geo['city'] ?? 'Unknown';
            }
        }
    } catch (Exception $e) {
        // Silently fail if geolocation fails
    }
    
    // Save visitor data
    try {
        $stmt = $pdo->prepare("INSERT INTO visitors 
                               (ip_address, user_agent, referrer, page_visited, is_bot, is_suspicious, 
                                country, city, device_type, browser, os, session_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $ip, $userAgent, $referrer, $page, 
            $isBot ? 1 : 0, $isSuspicious ? 1 : 0,
            $country, $city, $deviceType, $browser, $os, $sessionId
        ]);
        
        // Update or create session
        $stmt = $pdo->prepare("INSERT INTO visitor_sessions 
                               (session_id, ip_address, user_agent, is_bot) 
                               VALUES (?, ?, ?, ?)
                               ON DUPLICATE KEY UPDATE 
                               last_visit = NOW(), 
                               page_count = page_count + 1");
        $stmt->execute([$sessionId, $ip, $userAgent, $isBot ? 1 : 0]);
        
        // Update daily stats
        updateDailyStats($isBot, $isSuspicious);
        
        // Log attack if suspicious
        if ($isSuspicious) {
            logAttack($ip, 'Suspicious Behavior', 
                     "Multiple rapid requests detected", 
                     'medium', $page);
            
            // Auto-block if enabled
            if (AUTO_BLOCK_SUSPICIOUS) {
                blockIP($ip, 'Auto-blocked: Suspicious behavior pattern', null);
            }
        }
        
    } catch(PDOException $e) {
        // Silently fail if database error (don't break the site)
        error_log("Visitor tracking error: " . $e->getMessage());
    }
}

/**
 * Update daily statistics
 */
function updateDailyStats($isBot = false, $isSuspicious = false) {
    global $pdo;
    
    try {
        $today = date('Y-m-d');
        
        // Get or create today's stats
        $stmt = $pdo->prepare("SELECT id FROM daily_stats WHERE date = ?");
        $stmt->execute([$today]);
        $exists = $stmt->fetch();
        
        if ($exists) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE daily_stats SET 
                                   total_visits = total_visits + 1,
                                   bot_visits = bot_visits + ?,
                                   real_visits = real_visits + ?,
                                   blocked_attempts = blocked_attempts + ?
                                   WHERE date = ?");
            $stmt->execute([
                $isBot ? 1 : 0,
                $isBot ? 0 : 1,
                $isSuspicious ? 1 : 0,
                $today
            ]);
        } else {
            // Create new
            $stmt = $pdo->prepare("INSERT INTO daily_stats 
                                   (date, total_visits, bot_visits, real_visits, blocked_attempts) 
                                   VALUES (?, 1, ?, ?, ?)");
            $stmt->execute([
                $today,
                $isBot ? 1 : 0,
                $isBot ? 0 : 1,
                $isSuspicious ? 1 : 0
            ]);
        }
        
        // Update unique visitors count (approximate)
        $stmt = $pdo->prepare("UPDATE daily_stats SET 
                               unique_visitors = (
                                   SELECT COUNT(DISTINCT ip_address) 
                                   FROM visitors 
                                   WHERE DATE(visit_time) = ?
                               ) WHERE date = ?");
        $stmt->execute([$today, $today]);
        
    } catch(PDOException $e) {
        error_log("Daily stats update error: " . $e->getMessage());
    }
}
?>

