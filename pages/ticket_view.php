<?php
// ticket_view.php — Pair A
// Single ticket detail view with activity trail. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

// ── Dummy Data ──
$ticket = [
    'id'          => 1021,
    'subject'     => 'Network switch failure in Server Room B',
    'description' => 'The managed switch in Server Room B has stopped responding. Approximately 30 workstations on Floor 3 have lost network connectivity. Rebooting the switch did not resolve the issue. The issue began at approximately 09:15 AM today.',
    'priority'    => 'critical',
    'status'      => 'in_progress',
    'client'      => 'Acme Corp',
    'contact'     => 'Maria Santos',
    'assigned'    => 'J. Reyes',
    'created'     => '2026-04-25 09:22',
    'updated'     => '2026-04-25 10:45',
    'sla_deadline'=> '2026-04-25 11:22',
];

$activity = [
    ['actor'=>'J. Reyes',      'action'=>'Status changed to In Progress. On-site visit scheduled.',   'time'=>'2026-04-25 10:45','dot'=>'blue'],
    ['actor'=>'J. Reyes',      'action'=>'Ticket assigned by Admin.',                                  'time'=>'2026-04-25 09:35','dot'=>'navy'],
    ['actor'=>'Maria Santos',  'action'=>'Ticket submitted: Network switch failure in Server Room B.', 'time'=>'2026-04-25 09:22','dot'=>'silver'],
];

$priority_map = ['critical'=>'badge-critical','high'=>'badge-high','medium'=>'badge-medium','low'=>'badge-low'];
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
                SLA Deadline: <strong style="color:var(--priority-critical-text);"><?php echo $ticket['sla_deadline']; ?></strong>
            </span>
        </div>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;">
        <a href="tickets.php" class="btn-ts-secondary">← Back to Tickets</a>
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'],['admin','technician'])): ?>
        <button class="btn-ts-primary" onclick="alert('Edit form coming in integration phase.')">Edit Ticket</button>
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
                    <?php foreach ($activity as $a): ?>
                    <li class="activity-item">
                        <div class="activity-dot <?php echo $a['dot']; ?>"></div>
                        <div class="activity-body">
                            <div class="activity-text">
                                <strong><?php echo htmlspecialchars($a['actor']); ?></strong> —
                                <?php echo htmlspecialchars($a['action']); ?>
                            </div>
                            <div class="activity-meta"><?php echo $a['time']; ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
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
                    'Client'       => $ticket['client'],
                    'Contact'      => $ticket['contact'],
                    'Assigned To'  => $ticket['assigned'],
                    'Created'      => $ticket['created'],
                    'Last Updated' => $ticket['updated'],
                ];
                foreach ($meta as $label => $val): ?>
                <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:.625rem 0;border-bottom:1px solid var(--border-color);">
                    <span style="font-size:.8125rem;font-weight:600;color:var(--text-muted);min-width:110px;"><?php echo $label; ?></span>
                    <span style="font-size:.875rem;color:var(--text-primary);text-align:right;"><?php echo htmlspecialchars($val); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div>

<?php require '../includes/footer.php'; ?>
