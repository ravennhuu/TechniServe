<?php
// leads.php — Pair A
// Incoming access requests (leads).
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin can access leads
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM leads ORDER BY created_at DESC");
    $stmt->execute();
    $leads = $stmt->fetchAll();
} catch (PDOException $e) {
    $leads = [];
    $error = "Failed to fetch leads: " . $e->getMessage();
}

$s_map = ['pending'=>'badge-open', 'approved'=>'badge-resolved', 'rejected'=>'badge-closed'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Leads</h1>
        <p class="page-subtitle">Access requests from the landing page. Review and approve or reject each lead.</p>
    </div>
</div>

<!-- Summary Badges -->
<div style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <?php
    $pending  = 0;
    $approved = 0;
    $rejected = 0;
    foreach ($leads as $l) {
        if ($l['status'] === 'pending') $pending++;
        elseif ($l['status'] === 'approved') $approved++;
        elseif ($l['status'] === 'rejected') $rejected++;
    }
    ?>
    <div class="kpi-card" style="padding:.75rem 1.25rem;flex:0 0 auto;">
        <div class="kpi-data">
            <div class="kpi-value" style="font-size:1.375rem;"><?php echo $pending; ?></div>
            <div class="kpi-label">Pending Review</div>
        </div>
    </div>
    <div class="kpi-card" style="padding:.75rem 1.25rem;flex:0 0 auto;">
        <div class="kpi-data">
            <div class="kpi-value" style="font-size:1.375rem;color:#059669;"><?php echo $approved; ?></div>
            <div class="kpi-label">Approved</div>
        </div>
    </div>
    <div class="kpi-card" style="padding:.75rem 1.25rem;flex:0 0 auto;">
        <div class="kpi-data">
            <div class="kpi-value" style="font-size:1.375rem;color:#DC2626;"><?php echo $rejected; ?></div>
            <div class="kpi-label">Rejected</div>
        </div>
    </div>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Company</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Plan</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead): ?>
                <tr>
                    <td class="col-id"><?php echo $lead['id']; ?></td>
                    <td style="font-weight:600;"><?php echo htmlspecialchars($lead['company_name']); ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($lead['contact_person']); ?></td>
                    <td style="font-size:.8125rem;"><a href="mailto:<?php echo $lead['email']; ?>" class="text-navy"><?php echo htmlspecialchars($lead['email']); ?></a></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);"><?php echo htmlspecialchars($lead['phone']); ?></td>
                    <td><span class="ts-badge badge-navy"><?php echo htmlspecialchars($lead['preferred_plan'] ?? 'Not Selected'); ?></span></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);white-space:nowrap;"><?php echo date('Y-m-d', strtotime($lead['created_at'])); ?></td>
                    <td>
                        <span class="ts-badge <?php echo $s_map[$lead['status']]; ?>">
                            <?php echo ucfirst($lead['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($lead['status'] === 'pending'): ?>
                        <div style="display:flex;gap:.375rem;">
                            <form action="../api/leads/update.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $lead['id']; ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn-ts-success btn-ts-sm">Approve</button>
                            </form>
                            <form action="../api/leads/update.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $lead['id']; ?>">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn-ts-danger btn-ts-sm">Reject</button>
                            </form>
                        </div>
                        <?php else: ?>
                        <span style="font-size:.8125rem;color:var(--text-muted);">Actioned</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
