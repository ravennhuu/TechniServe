<?php
// maintenance.php — Pair A
// Preventive maintenance log table. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

// ── Dummy Data ──
$logs = [
    ['id'=>201,'date'=>'2026-04-25','client'=>'Acme Corp',   'type'=>'Quarterly Server Check',   'technician'=>'J. Reyes',  'status'=>'completed','notes'=>'All servers healthy. RAM and HDD checked.'],
    ['id'=>200,'date'=>'2026-04-22','client'=>'Globe BPO',   'type'=>'Network Audit',             'technician'=>'M. Santos', 'status'=>'completed','notes'=>'Found unused open ports. Closed and documented.'],
    ['id'=>199,'date'=>'2026-04-20','client'=>'BPI Office',  'type'=>'UPS Battery Replacement',   'technician'=>'J. Reyes',  'status'=>'completed','notes'=>'Replaced 3 UPS units on Floor 2.'],
    ['id'=>198,'date'=>'2026-04-28','client'=>'SM Supermall','type'=>'CCTV System Inspection',    'technician'=>'R. Cruz',   'status'=>'scheduled','notes'=>'—'],
    ['id'=>197,'date'=>'2026-04-30','client'=>'Robinsons',   'type'=>'Firewall Firmware Update',  'technician'=>'M. Santos', 'status'=>'scheduled','notes'=>'—'],
    ['id'=>196,'date'=>'2026-04-18','client'=>'Ayala Land',  'type'=>'Workstation Cleanup',       'technician'=>'A. dela Rosa','status'=>'completed','notes'=>'50 workstations cleaned and updated.'],
];

$s_map = ['completed'=>'badge-resolved','scheduled'=>'badge-open','cancelled'=>'badge-closed'];

// ── Role-based Filtering ──
$is_client = (isset($_SESSION['role']) && $_SESSION['role'] === 'client');
$my_client = 'Acme Corp'; // Dummy

if ($is_client) {
    $logs = array_filter($logs, function($l) use ($my_client) {
        return $l['client'] === $my_client;
    });
    // Client-specific SLA pool dummy data
    $pool_total = 100;
    $pool_used = 25.5;
} else {
    $pool_total = 160;
    $pool_used = 42.5;
}
$pool_remaining = $pool_total - $pool_used;
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Maintenance Logs</h1>
        <p class="page-subtitle">Scheduled and completed preventive maintenance records.</p>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;">
        <?php if (!$is_client): ?>
        <a href="maintenance_create.php" class="btn-ts-primary" id="newMaintenanceBtn">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Log Maintenance
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- SLA Pool Status (Automated Maintenance Logging Feature) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $pool_total; ?>h</div>
                <div class="kpi-label"><?php echo $is_client ? 'Total Plan Hours' : 'Total SLA Pool (Monthly)'; ?></div>
                <div class="kpi-trend" style="color:var(--steel-blue);"><?php echo $is_client ? 'Allocated for your account' : 'Shared across all clients'; ?></div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-icon navy">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $pool_used; ?>h</div>
                <div class="kpi-label">Utilized Hours</div>
                <div class="kpi-trend red">↓ Deducted from pool</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-4">
        <div class="kpi-card">
            <div class="kpi-icon green">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $pool_remaining; ?>h</div>
                <div class="kpi-label">Remaining Balance</div>
                <div class="kpi-trend up">↑ Healthy status</div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="search-wrap">
        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="maintenanceSearch" class="ts-form-control" placeholder="Search logs…">
    </div>
    <select id="filterMaintenanceStatus" class="ts-form-control ts-form-select">
        <option value="">All Statuses</option>
        <option value="completed">Completed</option>
        <option value="scheduled">Scheduled</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table" id="maintenanceTable">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Maintenance Type</th>
                    <th>Technician</th>
                    <th>SLA Hours</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr>
                    <td class="col-id"><?php echo $log['id']; ?></td>
                    <td style="font-size:.8125rem;white-space:nowrap;"><?php echo $log['date']; ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($log['client']); ?></td>
                    <td><?php echo htmlspecialchars($log['type']); ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($log['technician']); ?></td>
                    <td>
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-weight:600; color:var(--navy-deepest);">
                                <?php echo (rand(2, 8)) . '.0h'; ?>
                            </span>
                            <span style="font-size:0.65rem; color:#059669; font-weight:700; text-transform:uppercase; letter-spacing:0.02em;">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 20 20" style="margin-right:2px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                Auto-Deducted
                            </span>
                        </div>
                    </td>
                    <td>
                        <span class="ts-badge <?php echo $s_map[$log['status']] ?? 'badge-silver'; ?>">
                            <?php echo ucfirst($log['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../public/js/maintenance.js"></script>
<?php require '../includes/footer.php'; ?>
