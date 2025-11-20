-- ============================================
-- RestartLabs Admin Panel Database Structure
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS rest_restartlabs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rest_restartlabs;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    full_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin (username: admin, password: admin123 - CHANGE THIS!)
-- To generate a new password hash, use: php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"
INSERT INTO admins (username, password, email, full_name) VALUES 
('admin', '$2y$10$DSc.Rvg3GTq9zgA21nufV./O1IVrjsQytrdwXIJYl06PUc8NOSwwu', 'admin@restartlab.com', 'Administrator');
-- Default password: admin123 (change after first login!)

-- Visitors Table
CREATE TABLE IF NOT EXISTS visitors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    referrer TEXT,
    page_visited VARCHAR(255),
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_bot TINYINT(1) DEFAULT 0,
    is_suspicious TINYINT(1) DEFAULT 0,
    country VARCHAR(100),
    city VARCHAR(100),
    device_type VARCHAR(50),
    browser VARCHAR(100),
    os VARCHAR(100),
    session_id VARCHAR(100),
    visit_duration INT DEFAULT 0,
    page_views INT DEFAULT 1,
    INDEX idx_ip (ip_address),
    INDEX idx_visit_time (visit_time),
    INDEX idx_is_bot (is_bot),
    INDEX idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Blocked IPs Table
CREATE TABLE IF NOT EXISTS blocked_ips (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) UNIQUE NOT NULL,
    reason TEXT,
    blocked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    blocked_by INT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (blocked_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_ip (ip_address),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact Form Submissions Table
CREATE TABLE IF NOT EXISTS contact_submissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    whatsapp_sent TINYINT(1) DEFAULT 0,
    notes TEXT,
    INDEX idx_status (status),
    INDEX idx_submitted (submitted_at),
    INDEX idx_ip (ip_address)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Attack Logs Table
CREATE TABLE IF NOT EXISTS attack_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    attack_type VARCHAR(50),
    description TEXT,
    page_url VARCHAR(255),
    detected_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    severity ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
    user_agent TEXT,
    INDEX idx_ip (ip_address),
    INDEX idx_severity (severity),
    INDEX idx_detected (detected_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daily Statistics Table
CREATE TABLE IF NOT EXISTS daily_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE UNIQUE NOT NULL,
    total_visits INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    bot_visits INT DEFAULT 0,
    real_visits INT DEFAULT 0,
    blocked_attempts INT DEFAULT 0,
    contact_submissions INT DEFAULT 0,
    INDEX idx_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sessions Table (for tracking user sessions)
CREATE TABLE IF NOT EXISTS visitor_sessions (
    session_id VARCHAR(100) PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    first_visit TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_visit TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    page_count INT DEFAULT 1,
    is_bot TINYINT(1) DEFAULT 0,
    INDEX idx_ip (ip_address),
    INDEX idx_last_visit (last_visit)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings Table (for storing admin panel settings)
CREATE TABLE IF NOT EXISTS settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (updated_by) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO settings (setting_key, setting_value) VALUES 
('whatsapp_number', '+8801XXXXXXXXX'),
('whatsapp_enabled', '1'),
('contact_email', 'info@restartlab.com')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

