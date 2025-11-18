<?php
/**
 * Bot Detection System
 * Detects bots and suspicious visitors
 */

/**
 * Check if visitor is a bot
 */
function isBot($userAgent = '') {
    if (empty($userAgent)) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    // Known bot user agents
    $botPatterns = [
        'bot', 'crawler', 'spider', 'scraper', 'crawl',
        'googlebot', 'bingbot', 'yandexbot', 'slurp',
        'duckduckbot', 'baiduspider', 'yisouspider',
        'facebookexternalhit', 'twitterbot', 'rogerbot',
        'linkedinbot', 'embedly', 'quora', 'pinterest',
        'slackbot', 'redditbot', 'applebot', 'flipboard',
        'tumblr', 'bitlybot', 'skypeuripreview', 'nuzzel',
        'discordbot', 'qwantify', 'pinterestbot', 'bitrix',
        'xing-contenttabreceiver', 'chrome-lighthouse',
        'telegrambot', 'curl', 'wget', 'python-requests',
        'java', 'go-http-client', 'apache-httpclient',
        'okhttp', 'scrapy', 'mechanize', 'phantomjs',
        'headless', 'selenium', 'webdriver'
    ];
    
    $userAgentLower = strtolower($userAgent);
    
    foreach ($botPatterns as $pattern) {
        if (strpos($userAgentLower, $pattern) !== false) {
            return true;
        }
    }
    
    // Check for empty or suspicious user agents
    if (empty($userAgent) || strlen($userAgent) < 10) {
        return true;
    }
    
    // Check for suspicious patterns
    $suspiciousPatterns = [
        '/^[a-z0-9]+$/i', // Only alphanumeric (suspicious)
        '/^mozilla\/4\.0$/i', // Old browser signature
    ];
    
    foreach ($suspiciousPatterns as $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Check if visitor behavior is suspicious
 */
function isSuspiciousBehavior($ip, $sessionId = '') {
    global $pdo;
    
    try {
        // Check for too many requests in short time
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM visitors 
                               WHERE ip_address = ? AND visit_time > DATE_SUB(NOW(), INTERVAL 1 MINUTE)");
        $stmt->execute([$ip]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 30) { // More than 30 requests per minute
            return true;
        }
        
        // Check for multiple page views in very short time (bot behavior)
        if (!empty($sessionId)) {
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM visitors 
                                   WHERE session_id = ? AND visit_time > DATE_SUB(NOW(), INTERVAL 10 SECOND)");
            $stmt->execute([$sessionId]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 5) { // More than 5 pages in 10 seconds
                return true;
            }
        }
        
        return false;
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Get device type from user agent
 */
function getDeviceType($userAgent = '') {
    if (empty($userAgent)) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    $userAgentLower = strtolower($userAgent);
    
    if (preg_match('/mobile|android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i', $userAgentLower)) {
        return 'Mobile';
    } elseif (preg_match('/tablet|ipad|playbook|silk/i', $userAgentLower)) {
        return 'Tablet';
    } else {
        return 'Desktop';
    }
}

/**
 * Get browser name from user agent
 */
function getBrowser($userAgent = '') {
    if (empty($userAgent)) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    $browsers = [
        'Chrome' => '/chrome/i',
        'Firefox' => '/firefox/i',
        'Safari' => '/safari/i',
        'Edge' => '/edge|edg/i',
        'Opera' => '/opera|opr/i',
        'Internet Explorer' => '/msie|trident/i',
        'Brave' => '/brave/i',
    ];
    
    foreach ($browsers as $name => $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return $name;
        }
    }
    
    return 'Unknown';
}

/**
 * Get OS from user agent
 */
function getOS($userAgent = '') {
    if (empty($userAgent)) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }
    
    $oses = [
        'Windows' => '/windows|win32|win64/i',
        'Mac OS' => '/macintosh|mac os x/i',
        'Linux' => '/linux/i',
        'Android' => '/android/i',
        'iOS' => '/iphone|ipad|ipod/i',
        'Unix' => '/unix/i',
    ];
    
    foreach ($oses as $name => $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return $name;
        }
    }
    
    return 'Unknown';
}
?>

