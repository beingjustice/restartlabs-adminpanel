<?php
$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Get statistics
$today = date('Y-m-d');
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Today's stats
$stmt = $GLOBALS['pdo']->prepare("SELECT * FROM daily_stats WHERE date = ?");
$stmt->execute([$today]);
$todayStats = $stmt->fetch() ?: [
    'total_visits' => 0,
    'unique_visitors' => 0,
    'bot_visits' => 0,
    'real_visits' => 0,
    'blocked_attempts' => 0,
    'contact_submissions' => 0
];

// Yesterday's stats
$stmt = $GLOBALS['pdo']->prepare("SELECT * FROM daily_stats WHERE date = ?");
$stmt->execute([$yesterday]);
$yesterdayStats = $stmt->fetch() ?: [
    'total_visits' => 0,
    'unique_visitors' => 0,
    'bot_visits' => 0,
    'real_visits' => 0,
    'blocked_attempts' => 0,
    'contact_submissions' => 0
];

// Total stats
$stmt = $GLOBALS['pdo']->query("SELECT 
    COUNT(*) as total_visits,
    COUNT(DISTINCT ip_address) as unique_ips,
    SUM(is_bot) as total_bots,
    SUM(is_suspicious) as total_suspicious
    FROM visitors");
$totalStats = $stmt->fetch();

// Recent visitors
$stmt = $GLOBALS['pdo']->query("SELECT * FROM visitors ORDER BY visit_time DESC LIMIT 10");
$recentVisitors = $stmt->fetchAll();

// New contact submissions
$stmt = $GLOBALS['pdo']->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'");
$newContacts = $stmt->fetch()['count'];

// Recent attacks
$stmt = $GLOBALS['pdo']->query("SELECT * FROM attack_logs ORDER BY detected_at DESC LIMIT 5");
$recentAttacks = $stmt->fetchAll();

// Last 7 days stats for chart
$stmt = $GLOBALS['pdo']->query("SELECT date, total_visits, real_visits, bot_visits 
                                FROM daily_stats 
                                WHERE date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
                                ORDER BY date ASC");
$chartData = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Today's Visits</div>
            <div class="stat-card-icon">
                <i class="fas fa-eye"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($todayStats['total_visits']); ?></div>
        <div class="stat-card-change <?php echo $todayStats['total_visits'] >= $yesterdayStats['total_visits'] ? 'positive' : 'negative'; ?>">
            <?php 
            $change = $todayStats['total_visits'] - $yesterdayStats['total_visits'];
            echo ($change >= 0 ? '+' : '') . number_format($change) . ' from yesterday';
            ?>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Real Visitors</div>
            <div class="stat-card-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($todayStats['real_visits']); ?></div>
        <div class="stat-card-change positive">
            <?php echo number_format(($todayStats['real_visits'] / max($todayStats['total_visits'], 1)) * 100, 1); ?>% of total
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Bot Visits</div>
            <div class="stat-card-icon">
                <i class="fas fa-robot"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($todayStats['bot_visits']); ?></div>
        <div class="stat-card-change negative">
            <?php echo number_format(($todayStats['bot_visits'] / max($todayStats['total_visits'], 1)) * 100, 1); ?>% of total
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">New Contacts</div>
            <div class="stat-card-icon">
                <i class="fas fa-envelope"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($newContacts); ?></div>
        <div class="stat-card-change">
            <a href="contacts.php" style="color: var(--accent-teal); text-decoration: none;">View all →</a>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Blocked Attempts</div>
            <div class="stat-card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($todayStats['blocked_attempts']); ?></div>
        <div class="stat-card-change">
            Today's security blocks
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-card-header">
            <div class="stat-card-title">Total Visitors</div>
            <div class="stat-card-icon">
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($totalStats['total_visits']); ?></div>
        <div class="stat-card-change">
            All time
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
    <div class="data-table-container">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="color: var(--accent-teal); margin-bottom: 10px;">Visits Overview (Last 7 Days)</h3>
        </div>
        <div style="padding: 20px;">
            <canvas id="visitsChart" style="max-height: 300px;"></canvas>
        </div>
    </div>
    
    <div class="data-table-container">
        <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="color: var(--accent-teal); margin-bottom: 10px;">Recent Attacks</h3>
        </div>
        <div style="padding: 20px;">
            <?php if (empty($recentAttacks)): ?>
                <p style="color: var(--text-secondary); text-align: center; padding: 20px;">No recent attacks</p>
            <?php else: ?>
                <?php foreach ($recentAttacks as $attack): ?>
                    <div style="padding: 10px 0; border-bottom: 1px solid var(--border-color);">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($attack['attack_type']); ?></strong>
                            <span class="badge badge-<?php echo $attack['severity'] === 'critical' ? 'danger' : ($attack['severity'] === 'high' ? 'warning' : 'info'); ?>">
                                <?php echo ucfirst($attack['severity']); ?>
                            </span>
                        </div>
                        <div style="color: var(--text-secondary); font-size: 0.85rem;">
                            IP: <?php echo htmlspecialchars($attack['ip_address']); ?> | 
                            <?php echo date('M d, H:i', strtotime($attack['detected_at'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="data-table-container">
    <div style="padding: 20px; border-bottom: 1px solid var(--border-color);">
        <h3 style="color: var(--accent-teal);">Recent Visitors</h3>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Location</th>
                <th>Device</th>
                <th>Browser</th>
                <th>Page</th>
                <th>Type</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentVisitors)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        No visitors yet
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($recentVisitors as $visitor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($visitor['ip_address']); ?></td>
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
                        <td><?php echo htmlspecialchars($visitor['page_visited']); ?></td>
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
                        <td><?php echo date('M d, H:i', strtotime($visitor['visit_time'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Visits Chart
const ctx = document.getElementById('visitsChart').getContext('2d');
const chartData = {
    labels: [<?php echo implode(',', array_map(function($d) { return "'" . date('M d', strtotime($d['date'])) . "'"; }, $chartData)); ?>],
    datasets: [{
        label: 'Total Visits',
        data: [<?php echo implode(',', array_column($chartData, 'total_visits')); ?>],
        borderColor: '#1cd4c2',
        backgroundColor: 'rgba(28, 212, 194, 0.1)',
        tension: 0.4
    }, {
        label: 'Real Visitors',
        data: [<?php echo implode(',', array_column($chartData, 'real_visits')); ?>],
        borderColor: '#0d9488',
        backgroundColor: 'rgba(13, 148, 136, 0.1)',
        tension: 0.4
    }, {
        label: 'Bot Visits',
        data: [<?php echo implode(',', array_column($chartData, 'bot_visits')); ?>],
        borderColor: '#ff4444',
        backgroundColor: 'rgba(255, 68, 68, 0.1)',
        tension: 0.4
    }]
};

new Chart(ctx, {
    type: 'line',
    data: chartData,
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#cccccc'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#cccccc'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            },
            x: {
                ticks: {
                    color: '#cccccc'
                },
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                }
            }
        }
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>

