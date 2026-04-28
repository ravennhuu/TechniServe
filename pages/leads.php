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
                            <button type="button" class="btn-ts-success btn-ts-sm"
                                onclick="actionLead(<?php echo $lead['id']; ?>, 'approved', this)">Approve</button>
                            <button type="button" class="btn-ts-danger btn-ts-sm"
                                onclick="actionLead(<?php echo $lead['id']; ?>, 'rejected', this)">Reject</button>
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
<script>
function actionLead(id, status, btn) {
    var label  = status === 'approved' ? 'approve' : 'reject';
    var capLabel = status === 'approved' ? 'Approve' : 'Reject';

    showConfirm(
        capLabel + ' Lead?',
        'Are you sure you want to ' + label + ' this lead? This action cannot be undone.',
        function () {
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '…';

            var fd = new FormData();
            fd.append('id', id);
            fd.append('status', status);

            fetch('../api/leads/update.php', { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    btn.disabled = false;
                    btn.innerHTML = original;
                    if (json.success) {
                        showSuccess(capLabel + 'd!', json.message, null);
                        /* After modal closes, reload to reflect new status */
                        var closeBtn = document.getElementById('tsModalCloseBtn');
                        if (closeBtn) {
                            closeBtn.addEventListener('click', function () {
                                window.location.reload();
                            }, { once: true });
                        }
                    } else {
                        showError('Action Failed', json.message || 'Could not update lead status.');
                    }
                })
                .catch(function () {
                    btn.disabled = false;
                    btn.innerHTML = original;
                    showError('Connection Error', 'Could not reach the server. Please try again.');
                });
        }
    );
}
</script>

<?php require '../includes/footer.php'; ?>
