<?php
/**
 * WhatsApp Notification System
 * Sends WhatsApp messages for contact form submissions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Get WhatsApp settings from database or config
 */
function getWhatsAppSettings() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('whatsapp_number', 'whatsapp_enabled')");
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        return [
            'number' => $settings['whatsapp_number'] ?? WHATSAPP_NUMBER,
            'enabled' => isset($settings['whatsapp_enabled']) ? (int)$settings['whatsapp_enabled'] : (WHATSAPP_ENABLED ? 1 : 0)
        ];
    } catch(PDOException $e) {
        // Fallback to constants
        return [
            'number' => WHATSAPP_NUMBER,
            'enabled' => WHATSAPP_ENABLED ? 1 : 0
        ];
    }
}

/**
 * Send WhatsApp notification for contact form submission
 * 
 * Note: This is a placeholder. You need to integrate with a WhatsApp API service.
 * Options:
 * 1. Twilio WhatsApp API (paid, reliable)
 * 2. WhatsApp Business API (requires approval)
 * 3. WhatsApp Web automation (whatsapp-web.js - Node.js)
 * 4. Email-to-WhatsApp gateway
 */
function sendWhatsAppNotification($name, $email, $phone, $company, $message, $ip) {
    $settings = getWhatsAppSettings();
    
    if (!$settings['enabled'] || empty($settings['number'])) {
        return false;
    }
    
    // Format the message
    $whatsappMessage = "🔔 *New Contact Form Submission*\n\n";
    $whatsappMessage .= "👤 *Name:* $name\n";
    $whatsappMessage .= "📧 *Email:* $email\n";
    $whatsappMessage .= "📱 *Phone:* $phone\n";
    
    if (!empty($company)) {
        $whatsappMessage .= "🏢 *Company:* $company\n";
    }
    
    $whatsappMessage .= "\n💬 *Message:*\n$message\n\n";
    $whatsappMessage .= "🌐 *IP Address:* $ip\n";
    $whatsappMessage .= "⏰ *Time:* " . date('Y-m-d H:i:s') . "\n";
    
    // Option 1: Using Twilio (Recommended - requires Twilio account)
    // return sendViaTwilio($whatsappMessage);
    
    // Option 2: Using WhatsApp Web API (requires API key)
    // return sendViaWhatsAppAPI($whatsappMessage);
    
    // Option 3: Log to file for manual sending or external service
    return logWhatsAppMessage($whatsappMessage);
}

/**
 * Log WhatsApp message to file (for manual sending or external service)
 */
function logWhatsAppMessage($message) {
    $settings = getWhatsAppSettings();
    
    $logFile = __DIR__ . '/../logs/whatsapp_notifications.txt';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logEntry = date('Y-m-d H:i:s') . " | " . $settings['number'] . "\n";
    $logEntry .= str_repeat("-", 50) . "\n";
    $logEntry .= $message . "\n";
    $logEntry .= str_repeat("=", 50) . "\n\n";
    
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    return true;
}

/**
 * Send via Twilio WhatsApp API
 * Uncomment and configure if using Twilio
 */
/*
function sendViaTwilio($message) {
    require_once __DIR__ . '/../vendor/autoload.php'; // If using Composer
    
    $settings = getWhatsAppSettings();
    $accountSid = 'YOUR_TWILIO_ACCOUNT_SID';
    $authToken = 'YOUR_TWILIO_AUTH_TOKEN';
    $fromNumber = 'whatsapp:+14155238886'; // Twilio WhatsApp number
    
    $client = new Twilio\Rest\Client($accountSid, $authToken);
    
    try {
        $client->messages->create(
            'whatsapp:' . $settings['number'],
            [
                'from' => $fromNumber,
                'body' => $message
            ]
        );
        return true;
    } catch (Exception $e) {
        error_log("Twilio WhatsApp error: " . $e->getMessage());
        return false;
    }
}
*/

/**
 * Send via generic WhatsApp API
 * Configure with your WhatsApp API service
 */
/*
function sendViaWhatsAppAPI($message) {
    $settings = getWhatsAppSettings();
    $apiUrl = 'https://api.whatsapp.com/send'; // Replace with your API endpoint
    $apiKey = 'YOUR_API_KEY';
    
    $data = [
        'to' => $settings['number'],
        'message' => $message,
        'api_key' => $apiKey
    ];
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return $httpCode === 200;
}
*/

/**
 * Alternative: Send email that can be forwarded to WhatsApp
 */
function sendEmailForWhatsApp($name, $email, $phone, $company, $message, $ip) {
    $to = CONTACT_EMAIL;
    $subject = "New Contact Form Submission - " . $name;
    
    $emailMessage = "New Contact Form Submission\n\n";
    $emailMessage .= "Name: $name\n";
    $emailMessage .= "Email: $email\n";
    $emailMessage .= "Phone: $phone\n";
    if (!empty($company)) {
        $emailMessage .= "Company: $company\n";
    }
    $emailMessage .= "\nMessage:\n$message\n\n";
    $emailMessage .= "IP Address: $ip\n";
    $emailMessage .= "Time: " . date('Y-m-d H:i:s') . "\n";
    
    $headers = "From: " . CONTACT_EMAIL . "\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    return mail($to, $subject, $emailMessage, $headers);
}
?>

