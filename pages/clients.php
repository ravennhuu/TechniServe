<?php
// clients.php — Pair A
// Client list table. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

$clients = [
    ['id'=>1,'company'=>'Acme Corp',    'contact'=>'Maria Santos',   'email'=>'m.santos@acme.ph',   'phone'=>'+63 917 100 2000','plan'=>'Enterprise',  'status'=>'active',  'since'=>'2024-01-15'],
    ['id'=>2,'company'=>'Globe BPO',    'contact'=>'Jose Dela Cruz', 'email'=>'j.delacruz@globe.ph','phone'=>'+63 918 200 3000','plan'=>'Professional','status'=>'active',  'since'=>'2024-03-01'],
    ['id'=>3,'company'=>'BPI Office',   'contact'=>'Ana Reyes',      'email'=>'a.reyes@bpi.ph',     'phone'=>'+63 922 300 4000','plan'=>'Basic',       'status'=>'active',  'since'=>'2024-06-10'],
    ['id'=>4,'company'=>'SM Supermall', 'contact'=>'Carlo Tan',      'email'=>'c.tan@sm.ph',        'phone'=>'+63 919 400 5000','plan'=>'Professional','status'=>'expiring','since'=>'2023-07-01'],
    ['id'=>5,'company'=>'Robinsons',    'contact'=>'Luz Cruz',       'email'=>'l.cruz@robinsons.ph','phone'=>'+63 917 500 6000','plan'=>'Basic',       'status'=>'inactive','since'=>'2023-04-01'],
    ['id'=>6,'company'=>'Ayala Land',   'contact'=>'Ramon Torres',   'email'=>'r.torres@ayala.ph',  'phone'=>'+63 917 600 7000','plan'=>'Enterprise',  'status'=>'active',  'since'=>'2025-01-20'],
];

$s_map = ['active'=>'badge-resolved','expiring'=>'badge-high','inactive'=>'badge-closed'];
$p_map = ['Enterprise'=>'badge-navy','Professional'=>'badge-in-progress','Basic'=>'badge-silver'];
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
                    <td style="font-weight:600;"><?php echo htmlspecialchars($c['company']); ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($c['contact']); ?></td>
                    <td style="font-size:.8125rem;"><a href="mailto:<?php echo $c['email']; ?>" class="text-navy"><?php echo htmlspecialchars($c['email']); ?></a></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);"><?php echo htmlspecialchars($c['phone']); ?></td>
                    <td><span class="ts-badge <?php echo $p_map[$c['plan']] ?? 'badge-silver'; ?>"><?php echo $c['plan']; ?></span></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);"><?php echo $c['since']; ?></td>
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
