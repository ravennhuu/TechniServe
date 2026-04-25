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
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Maintenance Logs</h1>
        <p class="page-subtitle">Scheduled and completed preventive maintenance records.</p>
    </div>
    <a href="maintenance_create.php" class="btn-ts-primary" id="newMaintenanceBtn">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Log Maintenance
    </a>
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
                    <th>Status</th>
                    <th>Notes</th>
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
                        <span class="ts-badge <?php echo $s_map[$log['status']] ?? 'badge-silver'; ?>">
                            <?php echo ucfirst($log['status']); ?>
                        </span>
                    </td>
                    <td style="font-size:.8125rem;color:var(--text-muted);max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                        <?php echo htmlspecialchars($log['notes']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../public/js/maintenance.js"></script>
<?php require '../includes/footer.php'; ?>
