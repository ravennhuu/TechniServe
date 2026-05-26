<?php
// clients.php — Pair A
// Client list table.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin can access clients list
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT c.*, s.monthly_hours_pool, s.end_date,
               CASE 
                 WHEN s.id IS NULL OR s.is_active = 0 THEN 'inactive'
                 WHEN s.end_date < DATE_ADD(CURDATE(), INTERVAL 30 DAY) THEN 'expiring'
                 ELSE 'active'
               END as status
        FROM clients c
        LEFT JOIN sla_contracts s ON c.id = s.client_id AND s.is_active = 1
        ORDER BY c.company_name ASC
    ");
    $stmt->execute();
    $clients = $stmt->fetchAll();
} catch (PDOException $e) {
    $clients = [];
    $error = "Failed to fetch clients: " . $e->getMessage();
}

$s_map = ['active'=>'badge-resolved','expiring'=>'badge-high','inactive'=>'badge-closed'];

function getPlanName($hours) {
    if (!$hours) return 'None';
    if ($hours < 20) return 'Basic';
    if ($hours < 40) return 'Professional';
    return 'Enterprise';
}

$p_map = ['Enterprise'=>'badge-navy','Professional'=>'badge-in-progress','Basic'=>'badge-silver','None'=>'badge-closed'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Clients</h1>
        <p class="page-subtitle">All managed corporate client accounts.</p>
    </div>
    <a href="client_form.php" class="btn-ts-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Add Client
    </a>
</div>

<!-- Search -->
<div class="filter-bar">
    <div class="search-wrap">
        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="clientSearch" class="ts-form-control" placeholder="Search clients…" oninput="filterClients()">
    </div>
    <select id="filterClientStatus" class="ts-form-control ts-form-select" onchange="filterClients()">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="expiring">Expiring</option>
        <option value="inactive">Inactive</option>
    </select>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table" id="clientsTable">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Company</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Plan</th>
                    <th>Client Since</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $c): ?>
                <tr>
                    <td class="col-id"><?php echo $c['id']; ?></td>
                    <td style="font-weight:600;"><?php echo htmlspecialchars($c['company_name']); ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($c['contact_person']); ?></td>
                    <td style="font-size:.8125rem;"><a href="mailto:<?php echo $c['contact_email']; ?>" class="text-navy"><?php echo htmlspecialchars($c['contact_email']); ?></a></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);"><?php echo htmlspecialchars($c['contact_phone']); ?></td>
                    <?php $plan = getPlanName($c['monthly_hours_pool'] ?? 0); ?>
                    <td><span class="ts-badge <?php echo $p_map[$plan] ?? 'badge-silver'; ?>"><?php echo $plan; ?></span></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);"><?php echo date('Y-m-d', strtotime($c['created_at'])); ?></td>
                    <td><span class="ts-badge <?php echo $s_map[$c['status']] ?? 'badge-silver'; ?>"><?php echo ucfirst($c['status']); ?></span></td>
                    <td>
                        <a href="client_form.php?id=<?php echo $c['id']; ?>" class="btn-ts-secondary btn-ts-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterClients() {
    var q = document.getElementById('clientSearch').value.toLowerCase();
    var s = document.getElementById('filterClientStatus').value.toLowerCase();
    document.querySelectorAll('#clientsTable tbody tr').forEach(function(row) {
        var text  = row.textContent.toLowerCase();
        var badge = row.querySelector('.ts-badge:last-of-type');
        var sBadge = badge ? badge.textContent.trim().toLowerCase() : '';
        row.style.display = ((!q || text.includes(q)) && (!s || sBadge === s)) ? '' : 'none';
    });
}
</script>

<?php require '../includes/footer.php'; ?>
