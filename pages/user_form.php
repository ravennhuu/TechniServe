<?php
// user_form.php — Pair A
// Create / Edit user form. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

$editing = isset($_GET['id']);
$clients = ['Acme Corp','Globe BPO','BPI Office','SM Supermall','Robinsons','Ayala Land','PLDT','Meralco'];
?>

<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="users.php" class="text-navy">Users</a>
    <span style="margin:0 .4rem;">/</span>
    <span><?php echo $editing ? 'Edit User' : 'Add User'; ?></span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo $editing ? 'Edit User' : 'Add New User'; ?></h1>
        <p class="page-subtitle">Manage system account credentials and role assignment.</p>
    </div>
    <a href="users.php" class="btn-ts-secondary">← Cancel</a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">User Details</h5></div>
            <div class="ts-card-body">
                <form action="api/users/save.php" method="POST" id="userForm">
                    <?php if ($editing): ?>
                    <input type="hidden" name="user_id" value="<?php echo (int)$_GET['id']; ?>">
                    <?php endif; ?>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="userName">Full Name <span class="required-star">*</span></label>
                        <input type="text" id="userName" name="name" class="ts-form-control" placeholder="e.g. Joel Reyes" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userEmail">Email Address <span class="required-star">*</span></label>
                                <input type="email" id="userEmail" name="email" class="ts-form-control" placeholder="user@techniServe.ph" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userRole">Role <span class="required-star">*</span></label>
                                <select id="userRole" name="role" class="ts-form-control ts-form-select" required onchange="toggleClientField()">
                                    <option value="">— Select Role —</option>
                                    <option value="admin">Admin</option>
                                    <option value="technician">Technician</option>
                                    <option value="client">Client</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Only shown when role = client -->
                    <div class="ts-form-group" id="clientField" style="display:none;">
                        <label class="ts-form-label" for="userClient">Client Account <span class="required-star">*</span></label>
                        <select id="userClient" name="client_id" class="ts-form-control ts-form-select">
                            <option value="">— Select Client —</option>
                            <?php foreach ($clients as $i => $c): ?>
                            <option value="<?php echo $i+1; ?>"><?php echo htmlspecialchars($c); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if (!$editing): ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userPassword">Password <span class="required-star">*</span></label>
                                <input type="password" id="userPassword" name="password" class="ts-form-control" placeholder="Min. 8 characters" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userPasswordConfirm">Confirm Password <span class="required-star">*</span></label>
                                <input type="password" id="userPasswordConfirm" name="password_confirm" class="ts-form-control" placeholder="Re-enter password" required>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="ts-alert ts-alert-info">
                        Leave password fields blank to keep the existing password.
                    </div>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userPassword">New Password</label>
                                <input type="password" id="userPassword" name="password" class="ts-form-control" placeholder="Leave blank to keep current">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userPasswordConfirm">Confirm New Password</label>
                                <input type="password" id="userPasswordConfirm" name="password_confirm" class="ts-form-control" placeholder="Leave blank to keep current">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="userStatus">Account Status</label>
                        <select id="userStatus" name="status" class="ts-form-control ts-form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                        <a href="users.php" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary">
                            <?php echo $editing ? 'Save Changes' : 'Create User'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleClientField() {
    var role = document.getElementById('userRole').value;
    var field = document.getElementById('clientField');
    field.style.display = (role === 'client') ? 'block' : 'none';
    document.getElementById('userClient').required = (role === 'client');
}
</script>

<?php require '../includes/footer.php'; ?>
