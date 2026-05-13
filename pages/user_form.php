<?php
// user_form.php — Pair A
// Create / Edit user form.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin can manage users
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$editing = isset($_GET['id']);
$user_data = null;

try {
    $stmt = $pdo->prepare("SELECT id, company_name FROM clients ORDER BY company_name ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll();

    if ($editing) {
        $stmt = $pdo->prepare("
            SELECT u.*, c.id AS client_id 
            FROM users u 
            LEFT JOIN clients c ON u.id = c.user_id 
            WHERE u.id = ?");
        $stmt->execute([$_GET['id']]);
        $user_data = $stmt->fetch();
        
        if (!$user_data) {
            header('Location: users.php');
            exit();
        }
    }
} catch (PDOException $e) {
    $clients = [];
}
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
                <?php $user_action = $editing ? '../api/users/update.php' : '../api/users/create.php'; ?>
                <form action="<?php echo $user_action; ?>" method="POST" id="userForm">
                    <?php if ($editing): ?>
                    <input type="hidden" name="user_id" value="<?php echo (int)$_GET['id']; ?>">
                    <?php endif; ?>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="userName">Full Name <span class="required-star">*</span></label>
                        <input type="text" id="userName" name="name" class="ts-form-control" 
                            value="<?php echo $user_data ? htmlspecialchars($user_data['name']) : ''; ?>"
                            placeholder="e.g. Joel Reyes" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userEmail">Email Address <span class="required-star">*</span></label>
                                <input type="email" id="userEmail" name="email" class="ts-form-control" 
                                    value="<?php echo $user_data ? htmlspecialchars($user_data['email']) : ''; ?>"
                                    placeholder="user@techniServe.ph" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="userRole">Role <span class="required-star">*</span></label>
                                <select id="userRole" name="role" class="ts-form-control ts-form-select" required onchange="toggleClientField()">
                                    <option value="">— Select Role —</option>
                                    <option value="admin" <?php echo ($user_data && $user_data['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    <option value="client" <?php echo ($user_data && $user_data['role'] === 'client') ? 'selected' : ''; ?>>Client</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Only shown when role = client -->
                    <div class="ts-form-group" id="clientField" style="display:<?php echo ($user_data && $user_data['role'] === 'client') ? 'block' : 'none'; ?>;">
                        <label class="ts-form-label" for="userClient">Client Account <span class="required-star">*</span></label>
                        <select id="userClient" name="client_id" class="ts-form-control ts-form-select">
                            <option value="">— Select Client —</option>
                            <?php foreach ($clients as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($user_data && $user_data['client_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['company_name']); ?>
                            </option>
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
                        <select id="userStatus" name="is_active" class="ts-form-control ts-form-select">
                            <option value="1" <?php echo ($user_data && $user_data['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($user_data && $user_data['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
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
    var role  = document.getElementById('userRole').value;
    var field = document.getElementById('clientField');
    field.style.display = (role === 'client') ? 'block' : 'none';
    document.getElementById('userClient').required = (role === 'client');
}

var isEditing = <?php echo $editing ? 'true' : 'false'; ?>;

submitFormAjax('#userForm', {
    successTitle:   isEditing ? 'User Updated!' : 'User Created!',
    successMessage: isEditing
        ? 'The user account has been updated successfully.'
        : 'New user account has been created successfully.',
    redirectUrl:    'users.php',
    errorTitle:     'Could Not Save User',
    validate: function(form) {
        var pwd     = form.querySelector('#userPassword');
        var confirm = form.querySelector('#userPasswordConfirm');
        if (pwd && confirm && pwd.value && pwd.value !== confirm.value) {
            showError('Password Mismatch', 'The passwords you entered do not match. Please try again.');
            return false;
        }
        if (pwd && pwd.value && pwd.value.length < 8) {
            showError('Password Too Short', 'Password must be at least 8 characters long.');
            return false;
        }
        var role = form.querySelector('#userRole');
        if (role && !role.value) {
            showError('Role Required', 'Please select a role for this user.');
            return false;
        }
    }
});
</script>

<?php require '../includes/footer.php'; ?>
