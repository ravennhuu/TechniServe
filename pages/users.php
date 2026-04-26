<?php
// users.php — Pair A
// User list table.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin can access the users list
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT u.*, c.company_name as client,
               CASE WHEN u.is_active = 1 THEN 'active' ELSE 'inactive' END as status,
               u.created_at as last_login -- Placeholder: schema lacks last_login, using created_at
        FROM users u
        LEFT JOIN clients c ON u.client_id = c.id
        ORDER BY u.name ASC
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    $users = [];
    $error = "Failed to fetch users: " . $e->getMessage();
}

$r_map = [
    'admin'  => 'badge-critical',
    'client' => 'badge-silver',
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
