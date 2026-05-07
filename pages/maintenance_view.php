<?php
// maintenance_view.php — Pair A
// Single maintenance log detail view.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/functions.php';
require '../includes/header.php';

$log_id = $_GET['id'] ?? null;
if (!$log_id) {
    header('Location: maintenance.php');
    exit();
}

try {
    // Fetch Maintenance Log with Client, Technician, and Ticket info
    $stmt = $pdo->prepare("
        SELECT m.*, t.client_id, c.company_name as client, u.name as technician, t.subject as ticket_subject
        FROM maintenance_logs m
        JOIN tickets t ON m.ticket_id = t.id
        JOIN clients c ON t.client_id = c.id
        JOIN users u ON m.performed_by = u.id
        WHERE m.id = ?
    ");
    $stmt->execute([$log_id]);
    $log = $stmt->fetch();

    if (!$log) {
        header('Location: maintenance.php');
        exit();
    }

    // Role check: Client can only see their own logs
    if ($_SESSION['role'] === 'client' && $log['client_id'] != $_SESSION['client_id']) {
        header('Location: maintenance.php');
        exit();
    }

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$status_map = ['scheduled'=>'badge-open','in_progress'=>'badge-in-progress','completed'=>'badge-resolved','cancelled'=>'badge-closed'];
?>

<!-- Breadcrumb -->
<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="maintenance.php" class="text-navy">Maintenance Logs</a>
    <span style="margin:0 .4rem;">/</span>
    <span>#<?php echo $log['id']; ?></span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">#<?php echo $log['id']; ?> — <?php echo htmlspecialchars($log['title']); ?></h1>
        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;flex-wrap:wrap;">
            <span class="ts-badge badge-silver">
                <?php echo ucfirst(str_replace('_', ' ', $log['activity_type'])); ?>
            </span>
            <span class="ts-badge <?php echo $status_map[$log['status']]; ?>">
                <?php echo ucfirst(str_replace('_',' ',$log['status'])); ?>
            </span>
            <span style="font-size:.8125rem;color:var(--text-muted);">
                Scheduled: <strong style="color:var(--navy-deepest);"><?php echo $log['scheduled_at'] ? formatDate($log['scheduled_at']) : 'Not Scheduled'; ?></strong>
            </span>
        </div>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;">
        <a href="maintenance.php" class="btn-ts-secondary">← Back to Logs</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <a href="maintenance_edit.php?id=<?php echo $log['id']; ?>" class="btn-ts-primary">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:4px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Edit Log
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">

    <!-- Left: Details + Description -->
    <div class="col-lg-8">

        <div class="ts-card mb-3">
            <div class="ts-card-header"><h5 class="ts-card-title">Description & Scope</h5></div>
            <div class="ts-card-body">
                <?php if (!empty($log['description'])): ?>
                <p style="font-size:.9rem;line-height:1.7;color:var(--text-primary);margin:0;">
                    <?php echo nl2br(htmlspecialchars($log['description'])); ?>
                </p>
                <?php else: ?>
                <p style="font-size:.9rem;color:var(--text-muted);font-style:italic;margin:0;">No description provided.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Right: Meta Panel -->
    <div class="col-lg-4">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Maintenance Details</h5></div>
            <div class="ts-card-body">
                <?php
                $meta = [
                    'Client'         => $log['client'],
                    'Technician'     => $log['technician'],
                    'Related Ticket' => '#' . $log['ticket_id'] . ' - ' . $log['ticket_subject'],
                    'Created Date'   => formatDate($log['created_at']),
                    'Completed Date' => $log['completed_at'] ? formatDate($log['completed_at']) : '—',
                    'SLA Deduction'  => number_format($log['hours_spent'], 1) . ' hours',
                ];
                foreach ($meta as $label => $val): ?>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.625rem 0;border-bottom:1px solid var(--border-color);">
                    <span style="font-size:.8125rem;font-weight:600;color:var(--text-muted);min-width:110px;"><?php echo $label; ?></span>
                    <span style="font-size:.875rem;color:<?php echo $label === 'SLA Deduction' ? '#059669' : 'var(--text-primary)'; ?>;text-align:right; font-weight:<?php echo $label === 'SLA Deduction' ? '700' : '400'; ?>;">
                        <?php echo htmlspecialchars($val); ?>
                    </span>
                </div>
                <?php endforeach; ?>
                
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <div style="margin-top: 1.25rem;">
                    <a href="ticket_view.php?id=<?php echo $log['ticket_id']; ?>" class="btn-ts-secondary w-100 text-center" style="display:block;">View Related Ticket</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php require '../includes/footer.php'; ?>
