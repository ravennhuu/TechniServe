<?php
// dashboard.php — Pair A
// Main hub for authorised users. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

// ── Dummy Data ──
$kpi = [
    'open_tickets'    => 14,
    'in_progress'     => 6,
    'resolved_today'  => 3,
    'sla_compliance'  => '98.2%',
];

$recent_tickets = [
    ['id'=>1021,'subject'=>'Network switch failure in Server Room B','priority'=>'critical','status'=>'open',       'client'=>'Acme Corp',    'created'=>'2026-04-25'],
    ['id'=>1020,'subject'=>'Outlook not syncing for 5 users',        'priority'=>'high',    'status'=>'in_progress','client'=>'Globe BPO',    'created'=>'2026-04-25'],
    ['id'=>1019,'subject'=>'Printer offline — Finance floor',         'priority'=>'low',     'status'=>'open',       'client'=>'BPI Office',   'created'=>'2026-04-24'],
    ['id'=>1018,'subject'=>'WiFi intermittent — Conference Room 3',   'priority'=>'low',     'status'=>'resolved',   'client'=>'SM Supermall','created'=>'2026-04-24'],
    ['id'=>1017,'subject'=>'Laptop battery replacement request',      'priority'=>'low',     'status'=>'resolved',   'client'=>'Robinsons',   'created'=>'2026-04-23'],
];

$upcoming_maintenance = [
    ['date'=>'2026-04-28','client'=>'Acme Corp',    'type'=>'Quarterly Server Check',      'technician'=>'J. Reyes'],
    ['date'=>'2026-04-30','client'=>'Globe BPO',    'type'=>'Network Audit',               'technician'=>'M. Santos'],
    ['date'=>'2026-05-02','client'=>'BPI Office',   'type'=>'UPS Battery Replacement',    'technician'=>'J. Reyes'],
];

// Filter data if client
if (isset($_SESSION['role']) && $_SESSION['role'] === 'client') {
    $my_client_name = 'Acme Corp'; // Dummy: in real app, get from session/DB
    $recent_tickets = array_filter($recent_tickets, function($t) use ($my_client_name) {
        return $t['client'] === $my_client_name;
    });
    $upcoming_maintenance = array_filter($upcoming_maintenance, function($m) use ($my_client_name) {
        return $m['client'] === $my_client_name;
    });
    $kpi = [
        'open_tickets'    => 2,
        'in_progress'     => 1,
        'resolved_today'  => 0,
        'sla_pool'        => '74.5',
        'sla_total'       => '100',
    ];
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>. Here's your overview.</p>
    </div>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="ticket_create.php" class="btn-ts-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Ticket
    </a>
    <?php endif; ?>
</div>

<!-- ── KPI Cards ── -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $kpi['open_tickets']; ?></div>
                <div class="kpi-label">Open Tickets</div>
                <div class="kpi-trend up">↑ 3 since yesterday</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $kpi['in_progress']; ?></div>
                <div class="kpi-label">In Progress</div>
                <div class="kpi-trend" style="color:var(--steel-blue)">Assigned to technicians</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon green">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $kpi['resolved_today']; ?></div>
                <div class="kpi-label">Resolved Today</div>
                <div class="kpi-trend up">↑ On track</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon navy">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="kpi-data">
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'client'): ?>
                <div class="kpi-value">
                    <?php echo $kpi['sla_pool']; ?>
                    <span style="font-size: 0.9rem; font-weight: 400; opacity: 0.7;">/ <?php echo $kpi['sla_total']; ?>h</span>
                </div>
                <div class="kpi-label">Remaining SLA Pool</div>
                <div class="kpi-trend up">↑ Health: Good</div>
                <?php else: ?>
                <div class="kpi-value"><?php echo $kpi['sla_compliance']; ?></div>
                <div class="kpi-label">SLA Compliance</div>
                <div class="kpi-trend up">↑ Above 98% target</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">

    <!-- Recent Tickets -->
    <div class="col-lg-8">
        <div class="ts-card">
            <div class="ts-card-header">
                <h5 class="ts-card-title">Recent Tickets</h5>
                <a href="tickets.php" class="btn-ts-secondary btn-ts-sm">View All</a>
            </div>
            <div class="ts-table-wrap">
                <table class="ts-table">
                    <thead>
                        <tr>
                            <th class="col-id">#</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Client</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_tickets as $t): ?>
                        <tr>
                            <td class="col-id">
                                <a href="ticket_view.php?id=<?php echo $t['id']; ?>" class="text-navy">#<?php echo $t['id']; ?></a>
                            </td>
                            <td style="max-width:260px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                <?php echo htmlspecialchars($t['subject']); ?>
                            </td>
                            <td>
                                <?php
                                $pMap = ['critical'=>'badge-critical','high'=>'badge-high','low'=>'badge-low'];
                                $cls  = $pMap[$t['priority']] ?? 'badge-silver';
                                ?>
                                <span class="ts-badge <?php echo $cls; ?>"><?php echo ucfirst($t['priority']); ?></span>
                            </td>
                            <td>
                                <?php
                                $sMap = ['open'=>'badge-open','in_progress'=>'badge-in-progress','resolved'=>'badge-resolved','closed'=>'badge-closed'];
                                $scls = $sMap[$t['status']] ?? 'badge-silver';
                                $slbl = ucfirst(str_replace('_',' ',$t['status']));
                                ?>
                                <span class="ts-badge <?php echo $scls; ?>"><?php echo $slbl; ?></span>
                            </td>
                            <td class="text-muted-ts"><?php echo htmlspecialchars($t['client']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Upcoming Maintenance -->
    <div class="col-lg-4">
        <div class="ts-card">
            <div class="ts-card-header">
                <h5 class="ts-card-title">Upcoming Maintenance</h5>
                <a href="maintenance.php" class="btn-ts-secondary btn-ts-sm">View All</a>
            </div>
            <div class="ts-card-body" style="padding:0;">
                <ul class="activity-trail" style="padding:0 1.25rem;">
                    <?php foreach ($upcoming_maintenance as $m): ?>
                    <li class="activity-item">
                        <div class="activity-dot navy"></div>
                        <div class="activity-body">
                            <div class="activity-text" style="font-weight:600;"><?php echo htmlspecialchars($m['type']); ?></div>
                            <div class="activity-meta">
                                <?php echo htmlspecialchars($m['client']); ?> &middot;
                                <?php echo htmlspecialchars($m['date']); ?> &middot;
                                <?php echo htmlspecialchars($m['technician']); ?>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

</div>

<?php require '../includes/footer.php'; ?>
