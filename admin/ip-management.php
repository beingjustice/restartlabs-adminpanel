<?php
$pageTitle = 'IP Management';
$currentPage = 'ip-management';
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Get blocked IPs
$stmt = $GLOBALS['pdo']->query("SELECT bi.*, a.username as blocked_by_username 
                                FROM blocked_ips bi 
                                LEFT JOIN admins a ON bi.blocked_by = a.id 
                                WHERE bi.is_active = 1 
                                ORDER BY bi.blocked_at DESC");
$blockedIPs = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div class="data-table-container">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="color: var(--accent-teal); margin: 0;">Block New IP</h3>
        </div>
        <div style="padding: 20px;">
            <form id="blockIPForm">
                <div class="form-group">
                    <label for="ip_address">IP Address</label>
                    <input type="text" id="ip_address" name="ip_address" required 
                           placeholder="192.168.1.1" pattern="^([0-9]{1,3}\.){3}[0-9]{1,3}$">
                </div>
                
                <div class="form-group">
                    <label for="reason">Reason</label>
                    <textarea id="reason" name="reason" placeholder="Reason for blocking this IP"></textarea>
                </div>
                
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-ban"></i> Block IP
                </button>
            </form>
        </div>
    </div>
    
    <div class="data-table-container">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="color: var(--accent-teal); margin: 0;">Quick Stats</h3>
        </div>
        <div style="padding: 20px;">
            <?php
            $stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as total FROM blocked_ips WHERE is_active = 1");
            $totalBlocked = $stmt->fetch()['total'];
            
            $stmt = $GLOBALS['pdo']->query("SELECT COUNT(DISTINCT ip_address) as unique_ips FROM visitors WHERE visit_time >= DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $uniqueIPs = $stmt->fetch()['unique_ips'];
            ?>
            <div style="margin-bottom: 15px;">
                <div style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 5px;">Total Blocked IPs</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--danger);"><?php echo number_format($totalBlocked); ?></div>
            </div>
            <div>
                <div style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 5px;">Unique IPs (Last 24h)</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--accent-teal);"><?php echo number_format($uniqueIPs); ?></div>
            </div>
        </div>
    </div>
</div>

<div class="data-table-container">
    <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
        <h3 style="color: var(--accent-teal); margin: 0;">Blocked IP Addresses (<?php echo count($blockedIPs); ?>)</h3>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Reason</th>
                <th>Blocked By</th>
                <th>Blocked At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($blockedIPs)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        No blocked IPs
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($blockedIPs as $blocked): ?>
                    <tr>
                        <td>
                            <code style="color: var(--danger);"><?php echo htmlspecialchars($blocked['ip_address']); ?></code>
                        </td>
                        <td><?php echo htmlspecialchars($blocked['reason'] ?: 'No reason provided'); ?></td>
                        <td><?php echo htmlspecialchars($blocked['blocked_by_username'] ?: 'System'); ?></td>
                        <td><?php echo date('M d, Y H:i', strtotime($blocked['blocked_at'])); ?></td>
                        <td>
                            <button onclick="unblockIP('<?php echo htmlspecialchars($blocked['ip_address']); ?>')" 
                                    class="btn btn-success btn-sm" 
                                    title="Unblock IP">
                                <i class="fas fa-unlock"></i> Unblock
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('blockIPForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const ip = document.getElementById('ip_address').value;
    const reason = document.getElementById('reason').value;
    
    try {
        const response = await fetch('api/block-ip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `ip=${encodeURIComponent(ip)}&reason=${encodeURIComponent(reason)}`
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
});

async function unblockIP(ip) {
    if (!confirm(`Are you sure you want to unblock IP: ${ip}?`)) {
        return;
    }
    
    try {
        const response = await fetch('api/unblock-ip.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `ip=${encodeURIComponent(ip)}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('IP unblocked successfully');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to unblock IP'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>

