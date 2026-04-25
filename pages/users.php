<?php
// users.php — Pair A
// User list table. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

$users = [
    ['id'=>1,'name'=>'Raven Villanueva','email'=>'raven@techniServe.ph',   'role'=>'admin',      'client'=>'—',           'status'=>'active',  'last_login'=>'2026-04-25 08:14'],
    ['id'=>2,'name'=>'Joel Reyes',      'email'=>'j.reyes@techniServe.ph', 'role'=>'technician', 'client'=>'—',           'status'=>'active',  'last_login'=>'2026-04-25 07:55'],
    ['id'=>3,'name'=>'Marco Santos',    'email'=>'m.santos@techniServe.ph','role'=>'technician', 'client'=>'—',           'status'=>'active',  'last_login'=>'2026-04-24 17:32'],
    ['id'=>4,'name'=>'Maria Santos',    'email'=>'m.santos@acme.ph',       'role'=>'client',     'client'=>'Acme Corp',   'status'=>'active',  'last_login'=>'2026-04-25 09:02'],
    ['id'=>5,'name'=>'Jose Dela Cruz',  'email'=>'j.delacruz@globe.ph',    'role'=>'client',     'client'=>'Globe BPO',   'status'=>'active',  'last_login'=>'2026-04-23 14:11'],
    ['id'=>6,'name'=>'Ana Reyes',       'email'=>'a.reyes@bpi.ph',         'role'=>'client',     'client'=>'BPI Office',  'status'=>'inactive','last_login'=>'2026-04-10 10:00'],
];

$r_map = [
    'admin'      => 'badge-critical',
    'technician' => 'badge-in-progress',
    'client'     => 'badge-silver',
];
$s_map = ['active'=>'badge-resolved','inactive'=>'badge-closed'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Users</h1>
        <p class="page-subtitle">All system accounts — admins, technicians, and client users.</p>
    </div>
    <a href="user_form.php" class="btn-ts-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        Add User
    </a>
</div>

<!-- Filter Bar -->
<div class="filter-bar">
    <div class="search-wrap">
        <svg class="search-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="userSearch" class="ts-form-control" placeholder="Search users…" oninput="filterUsers()">
    </div>
    <select id="filterUserRole" class="ts-form-control ts-form-select" onchange="filterUsers()">
        <option value="">All Roles</option>
        <option value="admin">Admin</option>
        <option value="technician">Technician</option>
        <option value="client">Client</option>
    </select>
    <select id="filterUserStatus" class="ts-form-control ts-form-select" onchange="filterUsers()">
        <option value="">All Statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
    </select>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table" id="usersTable">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th style="width: 20%;">Name</th>
                    <th style="width: 25%;">Email</th>
                    <th>Role</th>
                    <th>Client Account</th>
                    <th>Last Login</th>
                    <th>Status</th>
                    <th style="width: 80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td class="col-id"><?php echo $u['id']; ?></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:.625rem;">
                            <div class="avatar-initials" style="width:30px;height:30px;font-size:.75rem;">
                                <?php echo strtoupper(substr($u['name'],0,1)); ?>
                            </div>
                            <span style="font-weight:600;"><?php echo htmlspecialchars($u['name']); ?></span>
                        </div>
                    </td>
                    <td style="font-size:.8125rem;"><a href="mailto:<?php echo $u['email']; ?>" class="text-navy"><?php echo htmlspecialchars($u['email']); ?></a></td>
                    <td><span class="ts-badge <?php echo $r_map[$u['role']] ?? 'badge-silver'; ?>"><?php echo ucfirst($u['role']); ?></span></td>
                    <td style="font-size:.875rem;color:var(--text-muted);"><?php echo htmlspecialchars($u['client']); ?></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);white-space:nowrap;"><?php echo $u['last_login']; ?></td>
                    <td><span class="ts-badge <?php echo $s_map[$u['status']] ?? 'badge-silver'; ?>"><?php echo ucfirst($u['status']); ?></span></td>
                    <td>
                        <a href="user_form.php?id=<?php echo $u['id']; ?>" class="btn-ts-secondary btn-ts-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filterUsers() {
    var q  = document.getElementById('userSearch').value.toLowerCase();
    var r  = document.getElementById('filterUserRole').value.toLowerCase();
    var s  = document.getElementById('filterUserStatus').value.toLowerCase();
    document.querySelectorAll('#usersTable tbody tr').forEach(function(row) {
        var text    = row.textContent.toLowerCase();
        var badges  = row.querySelectorAll('.ts-badge');
        var rBadge  = badges[0] ? badges[0].textContent.trim().toLowerCase() : '';
        var sBadge  = badges[1] ? badges[1].textContent.trim().toLowerCase() : '';
        var matchQ  = !q || text.includes(q);
        var matchR  = !r || rBadge === r;
        var matchS  = !s || sBadge === s;
        row.style.display = (matchQ && matchR && matchS) ? '' : 'none';
    });
}
</script>

<?php require '../includes/footer.php'; ?>
