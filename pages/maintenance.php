<?php
// maintenance.php — Pair A
// Preventive maintenance log table.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/functions.php';
require '../includes/header.php';

$is_client = (isset($_SESSION['role']) && $_SESSION['role'] === 'client');

try {
    if ($is_client) {
        $client_id = $_SESSION['client_id'] ?? null;
        if (!$client_id) {
            $cstmt = $pdo->prepare("SELECT id FROM clients WHERE user_id = ?");
            $cstmt->execute([$_SESSION['user_id']]);
            $client = $cstmt->fetch();
            if ($client) {
                $client_id = $client['id'];
                $_SESSION['client_id'] = $client_id;
            }
        }
        
        // Logs for this client
        $stmt = $pdo->prepare("
            SELECT m.*, c.company_name as client, u.name as technician
            FROM maintenance_logs m
            JOIN tickets t ON m.ticket_id = t.id
            JOIN clients c ON t.client_id = c.id
            JOIN users u ON m.performed_by = u.id
            WHERE t.client_id = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$client_id]);
        $logs = $stmt->fetchAll();

        // SLA Pool Status
        $stmt = $pdo->prepare("SELECT monthly_hours_pool, hours_used, hours_remaining FROM v_sla_usage WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $pool = $stmt->fetch();
        $pool_total = $pool['monthly_hours_pool'] ?? 0;
        $pool_used = $pool['hours_used'] ?? 0;
    } else {
        // All logs for admin
        $stmt = $pdo->prepare("
            SELECT m.*, c.id as client_id, c.company_name as client, u.name as technician
            FROM maintenance_logs m
            JOIN tickets t ON m.ticket_id = t.id
            JOIN clients c ON t.client_id = c.id
            JOIN users u ON m.performed_by = u.id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute();
        $logs = $stmt->fetchAll();

        // Combined SLA Pool Status for Admin view
        $stmt = $pdo->prepare("SELECT SUM(monthly_hours_pool) as total, SUM(hours_used) as used FROM v_sla_usage");
        $stmt->execute();
        $pool = $stmt->fetch();
        $pool_total = $pool['total'] ?? 0;
        $pool_used = $pool['used'] ?? 0;

        // Fetch all clients for filter dropdown
        $cstmt = $pdo->query("SELECT id, company_name FROM clients ORDER BY company_name ASC");
        $all_clients = $cstmt->fetchAll();
    }
} catch (PDOException $e) {
    $logs = [];
    $pool_total = 0;
    $pool_used = 0;
}

$pool_remaining = $pool_total - $pool_used;
$s_map = ['completed'=>'badge-resolved','scheduled'=>'badge-open','cancelled'=>'badge-closed'];
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
    <?php if (!$is_client): ?>
    <select id="filterMaintenanceClient" class="ts-form-control ts-form-select">
        <option value="">All Clients</option>
        <?php foreach ($all_clients as $c): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['company_name']); ?></option>
        <?php endforeach; ?>
    </select>
    <?php endif; ?>
    <select id="filterMaintenanceStatus" class="ts-form-control ts-form-select">
        <option value="">All Statuses</option>
        <option value="completed">Completed</option>
        <option value="scheduled">Scheduled</option>
        <option value="cancelled">Cancelled</option>
    </select>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table" id="maintenanceTable" style="table-layout: auto;">
            <thead>
                <tr>
                    <th>Log Details</th>
                    <th>Client / Technician</th>
                    <th>Maintenance Scope</th>
                    <th>SLA Hours</th>
                    <th>Status</th>
                    <th style="width:140px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                <tr data-client-id="<?php echo isset($log['client_id']) ? $log['client_id'] : ''; ?>">
                    <td>
                        <div style="font-weight:700; color:var(--navy-deepest);">#<?php echo $log['id']; ?></div>
                        <div style="font-size:0.75rem; color:var(--text-muted); white-space:nowrap;"><?php echo date('M d, Y', strtotime($log['created_at'])); ?></div>
                    </td>
                    <td>
                        <div style="font-weight:600; color:var(--text-primary);"><?php echo htmlspecialchars($log['client']); ?></div>
                        <div style="font-size:0.75rem; color:var(--text-muted);">Tech: <?php echo htmlspecialchars($log['technician']); ?></div>
                    </td>
                    <td>
                        <div style="font-weight:500;"><?php echo htmlspecialchars($log['title']); ?></div>
                        <div style="font-size:0.75rem; color:var(--text-muted); text-transform:capitalize; margin-top: 2px;">
                            <?php echo str_replace('_', ' ', $log['activity_type']); ?>
                        </div>
                    </td>
                    <td>
                        <div style="display:flex; flex-direction:column;">
                            <span style="font-weight:600; color:var(--navy-deepest);">
                                <?php echo number_format($log['hours_spent'], 1) . 'h'; ?>
                            </span>
                            <?php if ($log['status'] === 'completed'): ?>
                            <span style="font-size:0.65rem; color:#059669; font-weight:700; text-transform:uppercase; letter-spacing:0.02em;">
                                <svg width="8" height="8" fill="currentColor" viewBox="0 0 20 20" style="margin-right:2px;"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                Deducted
                            </span>
                            <?php else: ?>
                            <span style="font-size:0.65rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:0.02em;">
                                Pending
                            </span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
                        <span class="ts-badge <?php echo $s_map[$log['status']] ?? 'badge-silver'; ?>">
                            <?php echo ucfirst($log['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;gap:.375rem;">
                            <a href="maintenance_view.php?id=<?php echo $log['id']; ?>" class="btn-ts-secondary btn-ts-sm">View</a>
                            <?php if (!$is_client): ?>
                            <a href="maintenance_edit.php?id=<?php echo $log['id']; ?>" class="btn-ts-secondary btn-ts-sm">Edit</a>
                            <button type="button" class="btn-ts-secondary btn-ts-sm maintenance-delete-btn" data-id="<?php echo $log['id']; ?>">
                                Delete
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../public/js/maintenance.js"></script>
<?php require '../includes/footer.php'; ?>
