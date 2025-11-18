<?php
$pageTitle = 'Visitors';
$currentPage = 'visitors';
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Get filter parameters
$filterIP = $_GET['ip'] ?? '';
$filterBot = $_GET['bot'] ?? '';
$filterDate = $_GET['date'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if (!empty($filterIP)) {
    $where[] = "ip_address LIKE ?";
    $params[] = "%$filterIP%";
}

if ($filterBot !== '') {
    $where[] = "is_bot = ?";
    $params[] = $filterBot;
}

if (!empty($filterDate)) {
    $where[] = "DATE(visit_time) = ?";
    $params[] = $filterDate;
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
$countQuery = "SELECT COUNT(*) as total FROM visitors $whereClause";
$stmt = $GLOBALS['pdo']->prepare($countQuery);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $perPage);

// Get visitors
$query = "SELECT * FROM visitors $whereClause ORDER BY visit_time DESC LIMIT $perPage OFFSET $offset";
$stmt = $GLOBALS['pdo']->prepare($query);
$stmt->execute($params);
$visitors = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div style="margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
        <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
            <label>Search IP</label>
            <input type="text" name="ip" value="<?php echo htmlspecialchars($filterIP); ?>" placeholder="IP address">
        </div>
        
        <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
            <label>Type</label>
            <select name="bot">
                <option value="">All</option>
                <option value="0" <?php echo $filterBot === '0' ? 'selected' : ''; ?>>Human</option>
                <option value="1" <?php echo $filterBot === '1' ? 'selected' : ''; ?>>Bot</option>
            </select>
        </div>
        
        <div class="form-group" style="flex: 1; min-width: 150px; margin-bottom: 0;">
            <label>Date</label>
            <input type="date" name="date" value="<?php echo htmlspecialchars($filterDate); ?>">
        </div>
        
        <div style="margin-bottom: 0;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Filter
            </button>
            <a href="visitors.php" class="btn" style="background: var(--border-color); color: var(--text-primary);">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
    </form>
</div>

<div class="data-table-container">
    <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="color: var(--accent-teal); margin: 0;">Visitors (<?php echo number_format($total); ?>)</h3>
        <div style="color: var(--text-secondary); font-size: 0.9rem;">
            Page <?php echo $page; ?> of <?php echo $totalPages; ?>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>IP Address</th>
                    <th>Location</th>
                    <th>Device</th>
                    <th>Browser</th>
                    <th>OS</th>
                    <th>Page</th>
                    <th>Referrer</th>
                    <th>Type</th>
                    <th>Time</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($visitors)): ?>
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            No visitors found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($visitors as $visitor): ?>
                        <tr>
                            <td>
                                <code style="color: var(--accent-teal);"><?php echo htmlspecialchars($visitor['ip_address']); ?></code>
                            </td>
                            <td>
                                <?php 
                                $location = [];
                                if (!empty($visitor['city'])) $location[] = $visitor['city'];
                                if (!empty($visitor['country'])) $location[] = $visitor['country'];
                                echo htmlspecialchars(implode(', ', $location) ?: 'Unknown');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($visitor['device_type'] ?: 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars($visitor['browser'] ?: 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars($visitor['os'] ?: 'Unknown'); ?></td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($visitor['page_visited']); ?>
                            </td>
                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                <?php echo htmlspecialchars($visitor['referrer'] ?: 'Direct'); ?>
                            </td>
                            <td>
                                <?php if ($visitor['is_bot']): ?>
                                    <span class="badge badge-bot">Bot</span>
                                <?php else: ?>
                                    <span class="badge badge-human">Human</span>
                                <?php endif; ?>
                                <?php if ($visitor['is_suspicious']): ?>
                                    <span class="badge badge-warning">Suspicious</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, H:i:s', strtotime($visitor['visit_time'])); ?></td>
                            <td>
                                <button onclick="blockIP('<?php echo htmlspecialchars($visitor['ip_address']); ?>')" 
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
                <a href="?page=<?php echo $page - 1; ?>&ip=<?php echo urlencode($filterIP); ?>&bot=<?php echo urlencode($filterBot); ?>&date=<?php echo urlencode($filterDate); ?>" 
                   class="btn" style="background: var(--border-color); color: var(--text-primary);">
                    <i class="fas fa-chevron-left"></i> Previous
                </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?php echo $i; ?>&ip=<?php echo urlencode($filterIP); ?>&bot=<?php echo urlencode($filterBot); ?>&date=<?php echo urlencode($filterDate); ?>" 
                   class="btn <?php echo $i === $page ? 'btn-primary' : ''; ?>" 
                   style="<?php echo $i === $page ? '' : 'background: var(--border-color); color: var(--text-primary);'; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>&ip=<?php echo urlencode($filterIP); ?>&bot=<?php echo urlencode($filterBot); ?>&date=<?php echo urlencode($filterDate); ?>" 
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
            body: `ip=${encodeURIComponent(ip)}&reason=Blocked from visitors page`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('IP blocked successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to block IP'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

