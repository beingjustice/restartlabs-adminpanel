<?php
$pageTitle = 'Security Logs';
$currentPage = 'security';
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Get filter parameters
$severity = $_GET['severity'] ?? '';
$attackType = $_GET['attack_type'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if (!empty($severity)) {
    $where[] = "severity = ?";
    $params[] = $severity;
}

if (!empty($attackType)) {
    $where[] = "attack_type LIKE ?";
    $params[] = "%$attackType%";
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM attack_logs $whereClause";
$stmt = $GLOBALS['pdo']->prepare($countQuery);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

// Get attack logs
$query = "SELECT * FROM attack_logs $whereClause ORDER BY detected_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $GLOBALS['pdo']->prepare($query);
$stmt->execute($params);
$attacks = $stmt->fetchAll();

// Get statistics
$stmt = $GLOBALS['pdo']->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN severity = 'critical' THEN 1 ELSE 0 END) as critical,
    SUM(CASE WHEN severity = 'high' THEN 1 ELSE 0 END) as high,
    SUM(CASE WHEN severity = 'medium' THEN 1 ELSE 0 END) as medium,
    SUM(CASE WHEN severity = 'low' THEN 1 ELSE 0 END) as low
    FROM attack_logs");
$stats = $stmt->fetch();

require_once 'includes/header.php';
?>

<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Total Attacks</div>
            <div class="stat-card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['total']); ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Critical</div>
            <div class="stat-card-icon" style="background: rgba(255, 68, 68, 0.1); color: var(--danger);">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
        </div>
        <div class="stat-card-value" style="color: var(--danger);"><?php echo number_format($stats['critical']); ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">High</div>
            <div class="stat-card-icon" style="background: rgba(255, 170, 0, 0.1); color: var(--warning);">
                <i class="fas fa-exclamation-circle"></i>
            </div>
        </div>
        <div class="stat-card-value" style="color: var(--warning);"><?php echo number_format($stats['high']); ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Medium</div>
            <div class="stat-card-icon" style="background: rgba(68, 170, 255, 0.1); color: #44aaff;">
                <i class="fas fa-info-circle"></i>
            </div>
        </div>
        <div class="stat-card-value" style="color: #44aaff;"><?php echo number_format($stats['medium']); ?></div>
    </div>
</div>

<div style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
        <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
            <label>Severity</label>
            <select name="severity">
                <option value="">All</option>
                <option value="critical" <?php echo $severity === 'critical' ? 'selected' : ''; ?>>Critical</option>
                <option value="high" <?php echo $severity === 'high' ? 'selected' : ''; ?>>High</option>
                <option value="medium" <?php echo $severity === 'medium' ? 'selected' : ''; ?>>Medium</option>
                <option value="low" <?php echo $severity === 'low' ? 'selected' : ''; ?>>Low</option>
            </select>
        </div>
        
        <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
            <label>Attack Type</label>
            <input type="text" name="attack_type" value="<?php echo htmlspecialchars($attackType); ?>" placeholder="Search attack type">
        </div>
        
        <div style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="security.php" class="btn" style="background: var(--border-color); color: var(--text-primary);">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
    </form>
</div>

<div class="data-table-container">
    <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="color: var(--accent-teal); margin: 0;">Attack Logs (<?php echo number_format($total); ?>)</h3>
        <div style="color: var(--text-secondary); font-size: 0.9rem;">
            Page <?php echo $page; ?> of <?php echo $totalPages; ?>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Attack Type</th>
                    <th>Description</th>
                    <th>Page URL</th>
                    <th>Severity</th>
                    <th>Detected At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($attacks)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            No attacks logged
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($attacks as $attack): ?>
                        <tr>
                            <td>
                                <code style="color: var(--accent-teal);"><?php echo htmlspecialchars($attack['ip_address']); ?></code>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($attack['attack_type']); ?></strong>
                            </td>
                            <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($attack['description']); ?>
                            </td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($attack['page_url'] ?: 'N/A'); ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php 
                                    echo $attack['severity'] === 'critical' ? 'danger' : 
                                        ($attack['severity'] === 'high' ? 'warning' : 
                                        ($attack['severity'] === 'medium' ? 'info' : 'success')); 
                                ?>">
                                    <?php echo ucfirst($attack['severity']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y H:i:s', strtotime($attack['detected_at'])); ?></td>
                            <td>
                                <button onclick="blockIP('<?php echo htmlspecialchars($attack['ip_address']); ?>')" 
                                        class="btn btn-danger btn-sm" 
                                        title="Block IP">
                                    <i class="fas fa-ban"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
        <div style="padding: 20px; border-top: 1px solid var(--border-color); display: flex; justify-content: center; gap: 10px;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>&severity=<?php echo urlencode($severity); ?>&attack_type=<?php echo urlencode($attackType); ?>" 
                   class="btn" style="background: var(--border-color); color: var(--text-primary);">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&severity=<?php echo urlencode($severity); ?>&attack_type=<?php echo urlencode($attackType); ?>" 
                   class="btn <?php echo $i === $page ? 'btn-primary' : ''; ?>" 
                   style="<?php echo $i === $page ? '' : 'background: var(--border-color); color: var(--text-primary);'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&severity=<?php echo urlencode($severity); ?>&attack_type=<?php echo urlencode($attackType); ?>" 
                   class="btn" style="background: var(--border-color); color: var(--text-primary);">
                    Next <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
async function blockIP(ip) {
    if (!confirm(`Are you sure you want to block IP: ${ip}?`)) {
        return;
    }
    
    try {
        const response = await fetch('api/block-ip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `ip=${encodeURIComponent(ip)}&reason=Blocked from security logs`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('IP blocked successfully');
        } else {
            alert('Error: ' + (data.message || 'Failed to block IP'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

