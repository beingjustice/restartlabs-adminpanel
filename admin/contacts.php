<?php
$pageTitle = 'Contact Forms';
$currentPage = 'contacts';
require_once 'includes/auth.php';
require_once __DIR__ . '/../config/database.php';

// Get filter parameters
$status = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Build query
$where = [];
$params = [];

if (!empty($status)) {
    $where[] = "status = ?";
    $params[] = $status;
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Get total count
try {
    $countQuery = "SELECT COUNT(*) as total FROM contact_submissions $whereClause";
    $stmt = $GLOBALS['pdo']->prepare($countQuery);
    $stmt->execute($params);
    $result = $stmt->fetch();
    $total = $result ? (int)$result['total'] : 0;
    $totalPages = max(1, ceil($total / $perPage));
} catch(PDOException $e) {
    $total = 0;
    $totalPages = 1;
    error_log("Contacts page count error: " . $e->getMessage());
}

// Get submissions
try {
    $query = "SELECT * FROM contact_submissions $whereClause ORDER BY submitted_at DESC LIMIT $perPage OFFSET $offset";
    $stmt = $GLOBALS['pdo']->prepare($query);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll() ?: [];
} catch(PDOException $e) {
    $submissions = [];
    error_log("Contacts page query error: " . $e->getMessage());
}

// Get selected submission details
$selectedId = $_GET['view'] ?? 0;
$selectedSubmission = null;
if ($selectedId) {
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM contact_submissions WHERE id = ?");
    $stmt->execute([$selectedId]);
    $selectedSubmission = $stmt->fetch();
    
    // Mark as read
    if ($selectedSubmission && $selectedSubmission['status'] === 'new') {
        $stmt = $GLOBALS['pdo']->prepare("UPDATE contact_submissions SET status = 'read' WHERE id = ?");
        $stmt->execute([$selectedId]);
    }
}

require_once 'includes/header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px;">
    <div>
        <div style="margin-bottom: 20px;">
            <form method="GET" style="display: flex; gap: 10px;">
                <select name="status" class="form-group" style="flex: 1; margin-bottom: 0;">
                    <option value="">All Status</option>
                    <option value="new" <?php echo $status === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo $status === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $status === 'replied' ? 'selected' : ''; ?>>Replied</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="contacts.php" class="btn" style="background: var(--border-color); color: var(--text-primary);">Clear</a>
            </form>
        </div>
        
        <div class="data-table-container">
            <div style="padding: 15px; border-bottom: 1px solid var(--border-color);">
                <h3 style="color: var(--accent-teal); margin: 0; font-size: 1rem;">Submissions (<?php echo number_format($total); ?>)</h3>
            </div>
            <div style="max-height: 600px; overflow-y: auto;">
                <?php if (empty($submissions)): ?>
                    <div style="padding: 40px; text-align: center; color: var(--text-secondary);">
                        No submissions found
                    </div>
                <?php else: ?>
                    <?php foreach ($submissions as $sub): ?>
                        <a href="?view=<?php echo $sub['id']; ?>&status=<?php echo urlencode($status); ?>&page=<?php echo $page; ?>" 
                           style="display: block; padding: 15px; border-bottom: 1px solid var(--border-color); 
                                  text-decoration: none; color: inherit;
                                  <?php echo $selectedId == $sub['id'] ? 'background: rgba(28, 212, 194, 0.1); border-left: 3px solid var(--accent-teal);' : ''; ?>
                                  <?php echo $sub['status'] === 'new' ? 'font-weight: 600;' : ''; ?>">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <strong style="color: var(--text-primary);"><?php echo htmlspecialchars($sub['name']); ?></strong>
                                <span class="badge badge-<?php echo $sub['status'] === 'new' ? 'success' : ($sub['status'] === 'replied' ? 'info' : 'warning'); ?>">
                                    <?php echo ucfirst($sub['status']); ?>
                                </span>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 0.85rem;">
                                <?php echo htmlspecialchars($sub['email']); ?>
                            </div>
                            <div style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 5px;">
                                <?php echo date('M d, Y H:i', strtotime($sub['submitted_at'])); ?>
                            </div>
                            <?php if ($sub['whatsapp_sent']): ?>
                                <div style="color: var(--accent-teal); font-size: 0.75rem; margin-top: 5px;">
                                    <i class="fab fa-whatsapp"></i> WhatsApp sent
                                </div>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <div style="padding: 15px; border-top: 1px solid var(--border-color); display: flex; justify-content: center; gap: 5px;">
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>" 
                           class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : ''; ?>" 
                           style="<?php echo $i === $page ? '' : 'background: var(--border-color); color: var(--text-primary);'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="data-table-container">
        <?php if ($selectedSubmission): ?>
            <div style="padding: 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: var(--accent-teal); margin: 0;">Submission Details</h3>
                <div>
                    <span class="badge badge-<?php echo $selectedSubmission['status'] === 'new' ? 'success' : ($selectedSubmission['status'] === 'replied' ? 'info' : 'warning'); ?>">
                        <?php echo ucfirst($selectedSubmission['status']); ?>
                    </span>
                </div>
            </div>
            
            <div style="padding: 20px;">
                <div style="margin-bottom: 20px;">
                    <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 5px;">Name</label>
                    <div style="color: var(--text-primary); font-size: 1.1rem; font-weight: 600;">
                        <?php echo htmlspecialchars($selectedSubmission['name']); ?>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 5px;">Email</label>
                    <div style="color: var(--text-primary);">
                        <a href="mailto:<?php echo htmlspecialchars($selectedSubmission['email']); ?>" 
                           style="color: var(--accent-teal); text-decoration: none;">
                            <?php echo htmlspecialchars($selectedSubmission['email']); ?>
                        </a>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 5px;">Phone</label>
                    <div style="color: var(--text-primary);">
                        <a href="tel:<?php echo htmlspecialchars($selectedSubmission['phone']); ?>" 
                           style="color: var(--accent-teal); text-decoration: none;">
                            <?php echo htmlspecialchars($selectedSubmission['phone']); ?>
                        </a>
                    </div>
                </div>
                
                <?php if (!empty($selectedSubmission['company'])): ?>
                    <div style="margin-bottom: 20px;">
                        <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 5px;">Company</label>
                        <div style="color: var(--text-primary);">
                            <?php echo htmlspecialchars($selectedSubmission['company']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div style="margin-bottom: 20px;">
                    <label style="color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 5px;">Message</label>
                    <div style="color: var(--text-primary); background: rgba(255, 255, 255, 0.05); padding: 15px; border-radius: 8px; white-space: pre-wrap;">
                        <?php echo htmlspecialchars($selectedSubmission['message']); ?>
                    </div>
                </div>
                
                <div style="margin-bottom: 20px; padding: 15px; background: rgba(255, 255, 255, 0.05); border-radius: 8px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 0.9rem;">
                        <div>
                            <div style="color: var(--text-secondary); margin-bottom: 5px;">IP Address</div>
                            <div style="color: var(--text-primary);">
                                <code><?php echo htmlspecialchars($selectedSubmission['ip_address']); ?></code>
                            </div>
                        </div>
                        <div>
                            <div style="color: var(--text-secondary); margin-bottom: 5px;">Submitted At</div>
                            <div style="color: var(--text-primary);">
                                <?php echo date('M d, Y H:i:s', strtotime($selectedSubmission['submitted_at'])); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button onclick="updateStatus(<?php echo $selectedSubmission['id']; ?>, 'replied')" 
                            class="btn btn-success">
                        <i class="fas fa-check"></i> Mark as Replied
                    </button>
                    <button onclick="blockIP('<?php echo htmlspecialchars($selectedSubmission['ip_address']); ?>')" 
                            class="btn btn-danger">
                        <i class="fas fa-ban"></i> Block IP
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 40px; text-align: center; color: var(--text-secondary);">
                <i class="fas fa-envelope-open" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                <p>Select a submission to view details</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function updateStatus(id, status) {
    try {
        const response = await fetch('api/update-contact-status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&status=${status}`
        });
        
        const data = await response.json();
        
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update status'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

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
            body: `ip=${encodeURIComponent(ip)}&reason=Blocked from contact form`
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

