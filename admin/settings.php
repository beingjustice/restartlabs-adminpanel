<?php
$pageTitle = 'Settings';
$currentPage = 'settings';
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/database.php';

require_once 'includes/header.php';
?>

<div class="data-table-container">
    <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
        <h3 style="color: var(--accent-teal); margin: 0;">System Settings</h3>
    </div>
    
    <div style="padding: 20px;">
        <div style="margin-bottom: 30px;">
            <h4 style="color: var(--text-primary); margin-bottom: 15px;">Database Information</h4>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.9rem;">
                    <div>
                        <div style="color: var(--text-secondary); margin-bottom: 5px;">Database Name</div>
                        <div style="color: var(--text-primary);"><?php echo DB_NAME; ?></div>
                    </div>
                    <div>
                        <div style="color: var(--text-secondary); margin-bottom: 5px;">Database Host</div>
                        <div style="color: var(--text-primary);"><?php echo DB_HOST; ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h4 style="color: var(--text-primary); margin-bottom: 15px;">Configuration</h4>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.9rem;">
                    <div>
                        <div style="color: var(--text-secondary); margin-bottom: 5px;">Visitor Tracking</div>
                        <div style="color: var(--accent-teal);">
                            <?php echo TRACK_VISITORS ? '<i class="fas fa-check-circle"></i> Enabled' : '<i class="fas fa-times-circle"></i> Disabled'; ?>
                        </div>
                    </div>
                    <div>
                        <div style="color: var(--text-secondary); margin-bottom: 5px;">Bot Detection</div>
                        <div style="color: var(--accent-teal);">
                            <?php echo BOT_DETECTION_ENABLED ? '<i class="fas fa-check-circle"></i> Enabled' : '<i class="fas fa-times-circle"></i> Disabled'; ?>
                        </div>
                    </div>
                    <div>
                        <div style="color: var(--text-secondary); margin-bottom: 5px;">Auto-Block Suspicious</div>
                        <div style="color: var(--accent-teal);">
                            <?php echo AUTO_BLOCK_SUSPICIOUS ? '<i class="fas fa-check-circle"></i> Enabled' : '<i class="fas fa-times-circle"></i> Disabled'; ?>
                        </div>
                    </div>
                    <div>
                        <div style="color: var(--text-secondary); margin-bottom: 5px;">WhatsApp Integration</div>
                        <div style="color: var(--accent-teal);">
                            <?php echo WHATSAPP_ENABLED ? '<i class="fas fa-check-circle"></i> Enabled' : '<i class="fas fa-times-circle"></i> Disabled'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h4 style="color: var(--accent-teal); margin-bottom: 20px;">
                <i class="fab fa-whatsapp"></i> WhatsApp Configuration
            </h4>
            
            <?php
            // Get current settings from database
            try {
                // Check if table exists first
                $stmt = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'settings'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $GLOBALS['pdo']->query("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('whatsapp_number', 'whatsapp_enabled', 'contact_email')");
                    $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    
                    $currentWhatsAppNumber = $dbSettings['whatsapp_number'] ?? WHATSAPP_NUMBER;
                    $currentWhatsAppEnabled = isset($dbSettings['whatsapp_enabled']) ? (int)$dbSettings['whatsapp_enabled'] : (WHATSAPP_ENABLED ? 1 : 0);
                    $currentContactEmail = $dbSettings['contact_email'] ?? CONTACT_EMAIL;
                } else {
                    // Table doesn't exist, use constants
                    $currentWhatsAppNumber = WHATSAPP_NUMBER;
                    $currentWhatsAppEnabled = WHATSAPP_ENABLED ? 1 : 0;
                    $currentContactEmail = CONTACT_EMAIL;
                }
            } catch(PDOException $e) {
                // Fallback to constants if table doesn't exist
                $currentWhatsAppNumber = WHATSAPP_NUMBER;
                $currentWhatsAppEnabled = WHATSAPP_ENABLED ? 1 : 0;
                $currentContactEmail = CONTACT_EMAIL;
            }
            ?>
            
            <div class="data-table-container">
                <form id="whatsappSettingsForm">
                    <div style="padding: 20px;">
                        <div class="form-group">
                            <label for="whatsapp_number">
                                <i class="fab fa-whatsapp"></i> WhatsApp Number
                            </label>
                            <input type="text" 
                                   id="whatsapp_number" 
                                   name="whatsapp_number" 
                                   value="<?php echo htmlspecialchars($currentWhatsAppNumber); ?>" 
                                   placeholder="+8801XXXXXXXXX"
                                   pattern="^\+?[1-9]\d{1,14}$"
                                   required>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Use international format (e.g., +8801XXXXXXXXX)
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact_email">
                                <i class="fas fa-envelope"></i> Contact Email
                            </label>
                            <input type="email" 
                                   id="contact_email" 
                                   name="contact_email" 
                                   value="<?php echo htmlspecialchars($currentContactEmail); ?>" 
                                   placeholder="info@restartlab.com"
                                   required>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Email address for contact form notifications
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" 
                                       id="whatsapp_enabled" 
                                       name="whatsapp_enabled" 
                                       value="1"
                                       <?php echo $currentWhatsAppEnabled ? 'checked' : ''; ?>
                                       style="width: auto; margin: 0;">
                                <span>Enable WhatsApp Notifications</span>
                            </label>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> When enabled, you'll receive WhatsApp notifications for new contact form submissions
                            </small>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                            <span id="saveStatus" style="margin-left: 15px; color: var(--accent-teal);"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h4 style="color: var(--text-primary); margin-bottom: 15px;">Database Statistics</h4>
            <div style="background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px;">
                <?php
                $tables = ['visitors', 'blocked_ips', 'contact_submissions', 'attack_logs', 'daily_stats'];
                foreach ($tables as $table):
                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM $table");
                    $count = $stmt->fetch()['count'];
                ?>
                    <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <span style="color: var(--text-secondary);"><?php echo ucfirst(str_replace('_', ' ', $table)); ?></span>
                        <span style="color: var(--accent-teal); font-weight: 600;"><?php echo number_format($count); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div style="background: rgba(255, 170, 0, 0.1); border: 1px solid rgba(255, 170, 0, 0.3); padding: 15px; border-radius: 8px;">
            <div style="color: var(--warning); font-weight: 600; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle"></i> Important Notes
            </div>
            <ul style="color: var(--text-secondary); font-size: 0.9rem; margin-left: 20px;">
                <li>Change default admin password after first login</li>
                <li>WhatsApp notifications are currently logged to <code>logs/whatsapp_notifications.txt</code></li>
                <li>To enable real-time WhatsApp sending, configure WhatsApp API (Twilio, etc.) in <code>includes/whatsapp-sender.php</code></li>
                <li>Regularly review and clean up old logs</li>
                <li>Keep database backups regularly</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.getElementById('whatsappSettingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const saveStatus = document.getElementById('saveStatus');
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
    saveStatus.textContent = '';
    
    try {
        const response = await fetch('api/save-settings.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            saveStatus.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            saveStatus.style.color = 'var(--accent-teal)';
            
            // Hide status after 3 seconds
            setTimeout(() => {
                saveStatus.textContent = '';
            }, 3000);
        } else {
            saveStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Failed to save');
            saveStatus.style.color = 'var(--danger)';
            
            // Show error for 5 seconds
            setTimeout(() => {
                saveStatus.textContent = '';
            }, 5000);
        }
    } catch (error) {
        saveStatus.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + error.message;
        saveStatus.style.color = 'var(--danger)';
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

