<?php
// tickets.php — Pair A
// List and filter support tickets. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

// ── Dummy Data ──
$tickets = [
    ['id'=>1021,'subject'=>'Network switch failure in Server Room B',    'priority'=>'critical','status'=>'open',       'client'=>'Acme Corp',    'assigned'=>'J. Reyes',  'created'=>'2026-04-25'],
    ['id'=>1020,'subject'=>'Outlook not syncing for 5 users',            'priority'=>'high',   'status'=>'in_progress','client'=>'Globe BPO',    'assigned'=>'M. Santos', 'created'=>'2026-04-25'],
    ['id'=>1019,'subject'=>'Printer offline — Finance floor',             'priority'=>'medium', 'status'=>'open',       'client'=>'BPI Office',   'assigned'=>'Unassigned','created'=>'2026-04-24'],
    ['id'=>1018,'subject'=>'WiFi intermittent — Conference Room 3',       'priority'=>'low',    'status'=>'resolved',   'client'=>'SM Supermall','assigned'=>'J. Reyes',  'created'=>'2026-04-24'],
    ['id'=>1017,'subject'=>'Laptop battery replacement request',          'priority'=>'low',    'status'=>'resolved',   'client'=>'Robinsons',   'assigned'=>'M. Santos', 'created'=>'2026-04-23'],
    ['id'=>1016,'subject'=>'Email server latency spike',                  'priority'=>'high',   'status'=>'closed',     'client'=>'Acme Corp',    'assigned'=>'J. Reyes',  'created'=>'2026-04-22'],
    ['id'=>1015,'subject'=>'CCTV system not recording on Floor 2',        'priority'=>'critical','status'=>'in_progress','client'=>'Robinsons',   'assigned'=>'M. Santos', 'created'=>'2026-04-21'],
    ['id'=>1014,'subject'=>'VPN access issue for remote employee',        'priority'=>'medium', 'status'=>'open',       'client'=>'Globe BPO',    'assigned'=>'Unassigned','created'=>'2026-04-20'],
];

$priority_map = ['critical'=>'badge-critical','high'=>'badge-high','medium'=>'badge-medium','low'=>'badge-low'];
$status_map   = ['open'=>'badge-open','in_progress'=>'badge-in-progress','resolved'=>'badge-resolved','closed'=>'badge-closed'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Support Tickets</h1>
        <p class="page-subtitle">All client support requests — filter, search, and manage.</p>
    </div>
    <a href="ticket_create.php" class="btn-ts-primary" id="newTicketBtn">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Ticket
    </a>
</div>

<!-- Filter Bar -->
<div class="filter-bar" id="ticketFilterBar">
    <div class="search-wrap">
        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="ticketSearch" class="ts-form-control" placeholder="Search tickets…">
    </div>
    <select id="filterPriority" class="ts-form-control ts-form-select">
        <option value="">All Priorities</option>
        <option value="critical">Critical</option>
        <option value="high">High</option>
        <option value="medium">Medium</option>
        <option value="low">Low</option>
    </select>
    <select id="filterStatus" class="ts-form-control ts-form-select">
        <option value="">All Statuses</option>
        <option value="open">Open</option>
        <option value="in_progress">In Progress</option>
        <option value="resolved">Resolved</option>
        <option value="closed">Closed</option>
    </select>
</div>

<!-- Tickets Table -->
<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table" id="ticketsTable">
            <thead>
                <tr>
                    <th class="col-id">#ID</th>
                    <th style="width: 30%;">Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Client</th>
                    <th>Assigned To</th>
                    <th>Date</th>
                    <th style="width:80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $t): ?>
                <tr>
                    <td class="col-id">
                        <a href="ticket_view.php?id=<?php echo $t['id']; ?>" class="text-navy">#<?php echo $t['id']; ?></a>
                    </td>
                    <td style="max-width:280px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?php echo htmlspecialchars($t['subject']); ?>
                    </td>
                    <td>
                        <span class="ts-badge <?php echo $priority_map[$t['priority']] ?? 'badge-silver'; ?>">
                            <?php echo ucfirst($t['priority']); ?>
                        </span>
                    </td>
                    <td>
                        <span class="ts-badge <?php echo $status_map[$t['status']] ?? 'badge-silver'; ?>">
                            <?php echo ucfirst(str_replace('_',' ',$t['status'])); ?>
                        </span>
                    </td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($t['client']); ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($t['assigned']); ?></td>
                    <td class="text-muted-ts" style="font-size:.8125rem;"><?php echo $t['created']; ?></td>
                    <td>
                        <a href="ticket_view.php?id=<?php echo $t['id']; ?>" class="btn-ts-secondary btn-ts-sm">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../public/js/tickets.js"></script>
<?php require '../includes/footer.php'; ?>
