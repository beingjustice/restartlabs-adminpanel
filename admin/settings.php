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
            <h4 style="color: var(--accent-teal); margin-bottom: 20px;">
                <i class="fas fa-user"></i> Change Username
            </h4>
            
            <div class="data-table-container">
                <form id="changeUsernameForm">
                    <div style="padding: 20px;">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-user-circle"></i> Current Username
                            </label>
                            <input type="text" 
                                   value="<?php echo htmlspecialchars($adminUsername); ?>" 
                                   disabled
                                   style="background: rgba(255, 255, 255, 0.05); color: var(--text-secondary); cursor: not-allowed;">
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> This is your current username
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_username">
                                <i class="fas fa-user-edit"></i> New Username
                            </label>
                            <input type="text" 
                                   id="new_username" 
                                   name="new_username" 
                                   placeholder="Enter new username (min 3 characters)"
                                   minlength="3"
                                   pattern="[a-zA-Z0-9_]+"
                                   required>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Username must be at least 3 characters and can only contain letters, numbers, and underscores
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="username_current_password">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <div style="position: relative;">
                                <input type="password" 
                                       id="username_current_password" 
                                       name="current_password" 
                                       placeholder="Enter current password to confirm"
                                       required
                                       style="padding-right: 40px;">
                                <button type="button" 
                                        onclick="togglePasswordVisibility('username_current_password', this)" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px;">
                                    <i class="fas fa-eye" id="username_password_toggle_icon"></i>
                                </button>
                            </div>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Enter your current password to change username
                            </small>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Change Username
                            </button>
                            <span id="usernameChangeStatus" style="margin-left: 15px; color: var(--accent-teal);"></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h4 style="color: var(--accent-teal); margin-bottom: 20px;">
                <i class="fas fa-key"></i> Change Password
            </h4>
            
            <div class="data-table-container">
                <form id="changePasswordForm">
                    <div style="padding: 20px;">
                        <div class="form-group">
                            <label>
                                <i class="fas fa-lock"></i> Current Username
                            </label>
                            <input type="text" 
                                   value="<?php echo htmlspecialchars($adminUsername); ?>" 
                                   disabled
                                   style="background: rgba(255, 255, 255, 0.05); color: var(--text-secondary); cursor: not-allowed;">
                        </div>
                        
                        <div class="form-group">
                            <label for="current_password">
                                <i class="fas fa-lock"></i> Current Password
                            </label>
                            <div style="position: relative;">
                                <input type="password" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Enter current password"
                                       required
                                       style="padding-right: 40px;">
                                <button type="button" 
                                        onclick="togglePasswordVisibility('current_password', this)" 
                                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px;">
                                    <i class="fas fa-eye" id="password_toggle_icon"></i>
                                </button>
                            </div>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Enter your current password
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">
                                <i class="fas fa-key"></i> New Password
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Enter new password (min 6 characters)"
                                   minlength="6"
                                   required>
                            <small style="color: var(--text-secondary); font-size: 0.85rem; margin-top: 5px; display: block;">
                                <i class="fas fa-info-circle"></i> Password must be at least 6 characters long
                            </small>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">
                                <i class="fas fa-check-circle"></i> Confirm New Password
                            </label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Confirm new password"
                                   minlength="6"
                                   required>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Change Password
                            </button>
                            <span id="passwordChangeStatus" style="margin-left: 15px; color: var(--accent-teal);"></span>
                        </div>
                    </div>
                </form>
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
                </div>
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <h4 style="color: var(--accent-teal); margin-bottom: 20px;">
                <i class="fas fa-envelope"></i> Email Configuration
            </h4>
            
            <?php
            // Get current email setting from database
            try {
                // Check if table exists first
                $stmt = $GLOBALS['pdo']->query("SHOW TABLES LIKE 'settings'");
                if ($stmt->rowCount() > 0) {
                    $stmt = $GLOBALS['pdo']->query("SELECT setting_value FROM settings WHERE setting_key = 'contact_email'");
                    $result = $stmt->fetch();
                    $currentContactEmail = $result ? $result['setting_value'] : CONTACT_EMAIL;
                } else {
                    // Table doesn't exist, use constants
                    $currentContactEmail = CONTACT_EMAIL;
                }
            } catch(PDOException $e) {
                // Fallback to constants if table doesn't exist
                $currentContactEmail = CONTACT_EMAIL;
            }
            ?>
            
            <div class="data-table-container">
                <form id="emailSettingsForm">
                    <div style="padding: 20px;">
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
                                <i class="fas fa-info-circle"></i> Email address where contact form submissions will be sent
                            </small>
                        </div>
                        
                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Email Settings
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
                <li>Contact form submissions will be sent to the configured email address</li>
                <li>Make sure your email server is properly configured for sending emails</li>
                <li>Regularly review and clean up old logs</li>
                <li>Keep database backups regularly</li>
            </ul>
        </div>
    </div>
</div>

<script>
// Password visibility toggle function
function togglePasswordVisibility(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Username Change Form
document.getElementById('changeUsernameForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const statusEl = document.getElementById('usernameChangeStatus');
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
    statusEl.textContent = '';
    
    try {
        const response = await fetch('api/change-username.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            statusEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            statusEl.style.color = 'var(--accent-teal)';
            
            // Update displayed username in both forms
            const currentUsernameInputs = document.querySelectorAll('input[disabled][type="text"]');
            currentUsernameInputs.forEach(input => {
                if (input.value === '<?php echo htmlspecialchars($adminUsername); ?>') {
                    input.value = data.new_username;
                }
            });
            
            // Reload page after 2 seconds to update session
            setTimeout(() => {
                window.location.reload();
            }, 2000);
            
            // Clear form except current username
            document.getElementById('new_username').value = '';
            document.getElementById('username_current_password').value = '';
            
            // Hide status after 5 seconds
            setTimeout(() => {
                statusEl.textContent = '';
            }, 5000);
        } else {
            statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Failed to change username');
            statusEl.style.color = 'var(--danger)';
            
            // Show error for 5 seconds
            setTimeout(() => {
                statusEl.textContent = '';
            }, 5000);
        }
    } catch (error) {
        statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + error.message;
        statusEl.style.color = 'var(--danger)';
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }
});

// Password Change Form
document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const statusEl = document.getElementById('passwordChangeStatus');
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Check if passwords match
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Passwords do not match';
        statusEl.style.color = 'var(--danger)';
        return;
    }
    
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changing...';
    statusEl.textContent = '';
    
    try {
        const response = await fetch('api/change-password.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            statusEl.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
            statusEl.style.color = 'var(--accent-teal)';
            
            // Clear form
            this.reset();
            
            // Hide status after 5 seconds
            setTimeout(() => {
                statusEl.textContent = '';
            }, 5000);
        } else {
            statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + (data.message || 'Failed to change password');
            statusEl.style.color = 'var(--danger)';
            
            // Show error for 5 seconds
            setTimeout(() => {
                statusEl.textContent = '';
            }, 5000);
        }
    } catch (error) {
        statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error: ' + error.message;
        statusEl.style.color = 'var(--danger)';
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = originalText;
    }
});

// Email Settings Form
document.getElementById('emailSettingsForm').addEventListener('submit', async function(e) {
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

