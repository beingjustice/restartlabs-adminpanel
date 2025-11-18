<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Panel'; ?> - RestartLabs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2><i class="fas fa-shield-alt"></i> RestartLabs</h2>
                <p>Admin Panel</p>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item <?php echo ($currentPage ?? '') === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Dashboard
                </a>
                <a href="visitors.php" class="nav-item <?php echo ($currentPage ?? '') === 'visitors' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Visitors
                </a>
                <a href="ip-management.php" class="nav-item <?php echo ($currentPage ?? '') === 'ip-management' ? 'active' : ''; ?>">
                    <i class="fas fa-ban"></i> IP Management
                </a>
                <a href="contacts.php" class="nav-item <?php echo ($currentPage ?? '') === 'contacts' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i> Contact Forms
                    <?php
                    require_once __DIR__ . '/../../config/database.php';
                    $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'");
                    $newContacts = $stmt->fetch()['count'];
                    if ($newContacts > 0) {
                        echo '<span class="badge">' . $newContacts . '</span>';
                    }
                    ?>
                </a>
                <!-- Security Logs - Hidden (not needed) -->
                <!-- <a href="security.php" class="nav-item <?php echo ($currentPage ?? '') === 'security' ? 'active' : ''; ?>">
                    <i class="fas fa-shield-alt"></i> Security Logs
                </a> -->
                <a href="settings.php" class="nav-item <?php echo ($currentPage ?? '') === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </nav>
            
            <div class="sidebar-footer">
                <div class="admin-info">
                    <i class="fas fa-user-circle"></i>
                    <div>
                        <strong><?php echo htmlspecialchars($adminName); ?></strong>
                        <small><?php echo htmlspecialchars($adminUsername); ?></small>
                    </div>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <h1><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
                <div class="header-actions">
                    <span class="current-time" id="currentTime"></span>
                </div>
            </header>
            
            <div class="admin-content">

