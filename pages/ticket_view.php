<?php
// ticket_view.php — Pair A
// Single ticket detail view.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/functions.php';
require '../includes/header.php';

$ticket_id = $_GET['id'] ?? null;
if (!$ticket_id) {
    header('Location: tickets.php');
    exit();
}

try {
    // Fetch Ticket with Client and SLA info
    $stmt = $pdo->prepare("
        SELECT t.*, c.company_name as client, c.contact_person as contact, u.name as creator_name,
               sc.response_time_hrs,
               (SELECT SUM(hours_spent) FROM maintenance_logs WHERE ticket_id = t.id AND status = 'completed') as deducted_hours
        FROM tickets t
        JOIN clients c ON t.client_id = c.id
        JOIN users u ON t.created_by = u.id
        LEFT JOIN sla_contracts sc ON t.client_id = sc.client_id AND sc.is_active = 1
        WHERE t.id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        header('Location: tickets.php');
        exit();
    }

    // Role check: Client can only see their own tickets
    if ($_SESSION['role'] === 'client' && $ticket['client_id'] != $_SESSION['client_id']) {
        header('Location: tickets.php');
        exit();
    }

    // Calculate SLA Deadline
    $deadline = date('Y-m-d H:i:s', strtotime($ticket['created_at'] . ' + ' . ($ticket['response_time_hrs'] ?? 4) . ' hours'));

    // Fetch Activity Trail
    $stmt = $pdo->prepare("
        SELECT ta.*, u.name as actor
        FROM ticket_activities ta
        JOIN users u ON ta.user_id = u.id
        WHERE ta.ticket_id = ?
        ORDER BY ta.created_at DESC
    ");
    $stmt->execute([$ticket_id]);
    $activities = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$priority_map = ['critical'=>'badge-critical','high'=>'badge-high','low'=>'badge-low'];
$status_map   = ['open'=>'badge-open','in_progress'=>'badge-in-progress','resolved'=>'badge-resolved','closed'=>'badge-closed'];
?>

<!-- Breadcrumb -->
<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="tickets.php" class="text-navy">Tickets</a>
    <span style="margin:0 .4rem;">/</span>
    <span>#<?php echo $ticket['id']; ?></span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">#<?php echo $ticket['id']; ?> — <?php echo htmlspecialchars($ticket['subject']); ?></h1>
        <div style="display:flex;align-items:center;gap:.5rem;margin-top:.5rem;flex-wrap:wrap;">
            <span class="ts-badge <?php echo $priority_map[$ticket['priority']]; ?>">
                <?php echo ucfirst($ticket['priority']); ?>
            </span>
            <span class="ts-badge <?php echo $status_map[$ticket['status']]; ?>">
                <?php echo ucfirst(str_replace('_',' ',$ticket['status'])); ?>
            </span>
            <span style="font-size:.8125rem;color:var(--text-muted);">
                SLA Deadline: <strong style="color:var(--priority-critical-text);"><?php echo formatDate($deadline); ?></strong>
            </span>
        </div>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;">
        <a href="tickets.php" class="btn-ts-secondary">← Back to Tickets</a>
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'],['admin','technician'])): ?>
        <a href="ticket_edit.php?id=<?php echo $ticket['id']; ?>" class="btn-ts-primary">Edit Ticket</a>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">

    <!-- Left: Details + Description -->
    <div class="col-lg-8">

        <div class="ts-card mb-3">
            <div class="ts-card-header"><h5 class="ts-card-title">Description</h5></div>
            <div class="ts-card-body">
                <p style="font-size:.9rem;line-height:1.7;color:var(--text-primary);margin:0;">
                    <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
                </p>
            </div>
        </div>

        <!-- Activity Trail -->
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Activity Trail</h5></div>
            <div class="ts-card-body" style="padding-bottom:.5rem;">
                <ul class="activity-trail">
                    <?php foreach ($activities as $a): ?>
                    <li class="activity-item">
                        <div class="activity-dot <?php 
                            if (strpos($a['action'], 'resolved') !== false) echo 'green';
                            elseif (strpos($a['action'], 'Progress') !== false) echo 'blue';
                            elseif (strpos($a['action'], 'critical') !== false) echo 'red';
                            else echo 'silver';
                        ?>"></div>
                        <div class="activity-body">
                            <div class="activity-text">
                                <strong><?php echo htmlspecialchars($a['actor']); ?></strong> —
                                <?php echo htmlspecialchars($a['action']); ?>
                                <?php if ($a['note']): ?>
                                    <p class="mt-1 mb-0 text-muted" style="font-size: 0.85rem; font-style: italic;">"<?php echo htmlspecialchars($a['note']); ?>"</p>
                                <?php endif; ?>
                            </div>
                            <div class="activity-meta"><?php echo formatDate($a['created_at']); ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($activities)): ?>
                        <li class="activity-item">
                            <div class="activity-dot silver"></div>
                            <div class="activity-body">
                                <div class="activity-text text-muted">No activity recorded yet.</div>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

    </div>

    <!-- Right: Meta Panel -->
    <div class="col-lg-4">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Ticket Details</h5></div>
            <div class="ts-card-body">
                <?php
                $meta = [
                    'Client'        => $ticket['client'],
                    'Contact'       => $ticket['contact'],
                    'Created By'    => $ticket['creator_name'],
                    'Created Date'  => formatDate($ticket['created_at']),
                    'SLA Deduction' => isset($ticket['deducted_hours']) ? number_format($ticket['deducted_hours'], 1) . ' hours' : null,
                ];
                foreach ($meta as $label => $val): ?>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.625rem 0;border-bottom:1px solid var(--border-color);">
                    <span style="font-size:.8125rem;font-weight:600;color:var(--text-muted);min-width:110px;"><?php echo $label; ?></span>
                    <span style="font-size:.875rem;color:<?php echo $label === 'SLA Deduction' ? '#059669' : 'var(--text-primary)'; ?>;text-align:right; font-weight:<?php echo $label === 'SLA Deduction' ? '700' : '400'; ?>;">
                        <?php echo htmlspecialchars($val); ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php require '../includes/footer.php'; ?>
