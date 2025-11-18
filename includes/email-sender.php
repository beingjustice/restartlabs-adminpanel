<?php
/**
 * Email Notification System
 * Sends email notifications for contact form submissions
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Get email settings from database or config
 */
function getEmailSettings() {
    global $pdo;
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_key = 'contact_email'");
        $result = $stmt->fetch(PDO::FETCH_KEY_PAIR);
        
        return [
            'email' => $result['contact_email'] ?? CONTACT_EMAIL
        ];
    } catch(PDOException $e) {
        return [
            'email' => CONTACT_EMAIL
        ];
    }
}

/**
 * Send email notification for contact form submission
 */
function sendEmailNotification($name, $email, $phone, $company, $message, $ip) {
    $settings = getEmailSettings();
    $to = $settings['email'];
    
    if (empty($to)) {
        error_log("Email notification: No recipient email configured");
        return false;
    }
    
    // Email subject
    $subject = "🔔 New Contact Form Submission - " . $name;
    
    // Format email message
    $emailMessage = "You have received a new contact form submission from your website.\n\n";
    $emailMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $emailMessage .= "👤 NAME: $name\n";
    $emailMessage .= "📧 EMAIL: $email\n";
    $emailMessage .= "📱 PHONE: $phone\n";
    
    if (!empty($company)) {
        $emailMessage .= "🏢 COMPANY: $company\n";
    }
    
    $emailMessage .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $emailMessage .= "💬 MESSAGE:\n";
    $emailMessage .= str_repeat("─", 50) . "\n";
    $emailMessage .= $message . "\n";
    $emailMessage .= str_repeat("─", 50) . "\n\n";
    
    $emailMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $emailMessage .= "🌐 IP ADDRESS: $ip\n";
    $emailMessage .= "⏰ TIME: " . date('Y-m-d H:i:s') . "\n";
    $emailMessage .= "📅 DATE: " . date('F j, Y') . "\n";
    
    // WhatsApp link (if number is configured)
    try {
        $stmt = $GLOBALS['pdo']->query("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
        $whatsappNumber = $stmt->fetchColumn();
        
        if (!empty($whatsappNumber)) {
            // Format WhatsApp message
            $whatsappText = "New Contact Form Submission\n\n";
            $whatsappText .= "Name: $name\n";
            $whatsappText .= "Email: $email\n";
            $whatsappText .= "Phone: $phone\n";
            if (!empty($company)) {
                $whatsappText .= "Company: $company\n";
            }
            $whatsappText .= "\nMessage: $message";
            
            $whatsappLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode($whatsappText);
            
            $emailMessage .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $emailMessage .= "📱 REPLY VIA WHATSAPP:\n";
            $emailMessage .= $whatsappLink . "\n\n";
        }
    } catch(Exception $e) {
        // Silently fail if WhatsApp number not found
    }
    
    $emailMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $emailMessage .= "View in Admin Panel: " . ADMIN_URL . "/contacts.php\n";
    
    // Email headers
    $headers = "From: " . SITE_NAME . " <noreply@" . parse_url(SITE_URL, PHP_URL_HOST) . ">\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: 8bit\r\n";
    
    // Send email
    try {
        $result = mail($to, $subject, $emailMessage, $headers);
        
        if ($result) {
            error_log("Email notification sent successfully to: $to");
            return true;
        } else {
            error_log("Email notification failed to send to: $to");
            return false;
        }
    } catch(Exception $e) {
        error_log("Email notification error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send HTML email notification (better formatting)
 */
function sendHTMLEmailNotification($name, $email, $phone, $company, $message, $ip) {
    $settings = getEmailSettings();
    $to = $settings['email'];
    
    if (empty($to)) {
        return false;
    }
    
    $subject = "🔔 New Contact Form Submission - " . $name;
    
    // Get WhatsApp number for link
    $whatsappLink = '';
    try {
        $stmt = $GLOBALS['pdo']->query("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
        $whatsappNumber = $stmt->fetchColumn();
        
        if (!empty($whatsappNumber)) {
            $whatsappText = "New Contact Form Submission\n\nName: $name\nEmail: $email\nPhone: $phone\n\nMessage: $message";
            $whatsappLink = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode($whatsappText);
        }
    } catch(Exception $e) {
        // Silently fail
    }
    
    // HTML email body
    $htmlMessage = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #1cd4c2; color: #000; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
            .content { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #1cd4c2; }
            .message-box { background: #fff; padding: 15px; border-left: 4px solid #1cd4c2; margin: 15px 0; }
            .footer { background: #333; color: #fff; padding: 15px; text-align: center; border-radius: 0 0 8px 8px; font-size: 12px; }
            .whatsapp-btn { display: inline-block; background: #25D366; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin-top: 15px; }
            .whatsapp-btn:hover { background: #20BA5A; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🔔 New Contact Form Submission</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <span class='label'>👤 Name:</span> $name
                </div>
                <div class='field'>
                    <span class='label'>📧 Email:</span> <a href='mailto:$email'>$email</a>
                </div>
                <div class='field'>
                    <span class='label'>📱 Phone:</span> <a href='tel:$phone'>$phone</a>
                </div>";
    
    if (!empty($company)) {
        $htmlMessage .= "
                <div class='field'>
                    <span class='label'>🏢 Company:</span> $company
                </div>";
    }
    
    $htmlMessage .= "
                <div class='message-box'>
                    <div class='label'>💬 Message:</div>
                    <div style='margin-top: 10px; white-space: pre-wrap;'>" . htmlspecialchars($message) . "</div>
                </div>
                
                <div class='field'>
                    <span class='label'>🌐 IP Address:</span> $ip
                </div>
                <div class='field'>
                    <span class='label'>⏰ Time:</span> " . date('Y-m-d H:i:s') . "
                </div>";
    
    if (!empty($whatsappLink)) {
        $htmlMessage .= "
                <div style='text-align: center; margin-top: 20px;'>
                    <a href='$whatsappLink' class='whatsapp-btn' target='_blank'>
                        📱 Reply via WhatsApp
                    </a>
                </div>";
    }
    
    $htmlMessage .= "
                <div style='margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center;'>
                    <a href='" . ADMIN_URL . "/contacts.php' style='color: #1cd4c2; text-decoration: none;'>
                        View in Admin Panel →
                    </a>
                </div>
            </div>
            <div class='footer'>
                <p>This is an automated notification from " . SITE_NAME . "</p>
                <p>Do not reply to this email. Reply directly to: <a href='mailto:$email' style='color: #1cd4c2;'>$email</a></p>
            </div>
        </div>
    </body>
    </html>";
    
    // HTML email headers
    $headers = "From: " . SITE_NAME . " <noreply@" . parse_url(SITE_URL, PHP_URL_HOST) . ">\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    try {
        $result = mail($to, $subject, $htmlMessage, $headers);
        return $result;
    } catch(Exception $e) {
        error_log("HTML email notification error: " . $e->getMessage());
        return false;
    }
}
?>

