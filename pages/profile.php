<?php
// profile.php — Pair A
// Current user profile view / edit. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">My Profile</h1>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'client'): ?>
        <p class="page-subtitle">Your account information.</p>
        <?php else: ?>
        <p class="page-subtitle">View and update your account information.</p>
        <?php endif; ?>
    </div>
</div>

<div class="row g-3">

    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="ts-card text-center h-100" style="padding:2rem 1.5rem;">
            <div class="avatar-initials" style="width:72px;height:72px;font-size:1.75rem;margin:0 auto 1rem;">
                <?php echo strtoupper(substr($_SESSION['name'] ?? 'U', 0, 1)); ?>
            </div>
            <div style="font-size:1.0625rem;font-weight:700;color:var(--navy-deepest);">
                <?php echo htmlspecialchars($_SESSION['name'] ?? 'User'); ?>
            </div>
            <div style="margin-top:.375rem;">
                <span class="role-badge"><?php echo ucfirst($_SESSION['role'] ?? 'user'); ?></span>
            </div>
            <div class="divider"></div>
            <div style="font-size:.875rem;color:var(--text-muted);text-align:left;">
                <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border-color);">
                    <span style="font-weight:600;">Email</span>
                    <span><?php echo htmlspecialchars($_SESSION['email'] ?? 'user@techniServe.ph'); ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.4rem 0;border-bottom:1px solid var(--border-color);">
                    <span style="font-weight:600;">Role</span>
                    <span><?php echo ucfirst($_SESSION['role'] ?? '—'); ?></span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:.4rem 0;">
                    <span style="font-weight:600;">Status</span>
                    <span class="ts-badge badge-resolved">Active</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form / Read-only for Client -->
    <div class="col-lg-8">
        <div class="ts-card">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'client'): ?>
                <div class="ts-card-header"><h5 class="ts-card-title">Account Access</h5></div>
                <div class="ts-card-body">
                    <div class="ts-alert ts-alert-info">
                        Profile editing is restricted for client accounts. Please contact the administrator for any information updates.
                    </div>
                    <div style="padding:1rem 0;">
                        <p style="font-weight:600; color:var(--text-muted); font-size:.8125rem; text-transform:uppercase;">Account Permissions</p>
                        <ul style="list-style:none; padding:0; font-size:.875rem; color:var(--text-primary);">
                            <li style="padding:.5rem 0; border-bottom:1px solid var(--border-color);">✓ View dashboard and active tickets</li>
                            <li style="padding:.5rem 0; border-bottom:1px solid var(--border-color);">✓ Access ticket history</li>
                            <li style="padding:.5rem 0; border-bottom:1px solid var(--border-color);">✓ Submit new support requests</li>
                            <li style="padding:.5rem 0;">✓ Download monthly reports</li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <div class="ts-card-header"><h5 class="ts-card-title">Edit Profile</h5></div>
                <div class="ts-card-body">
                    <form action="../api/users/update_profile.php" method="POST" id="profileForm">

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="ts-form-group">
                                    <label class="ts-form-label" for="profileName">Full Name <span class="required-star">*</span></label>
                                    <input type="text" id="profileName" name="name" class="ts-form-control"
                                        value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ts-form-group">
                                    <label class="ts-form-label" for="profileEmail">Email Address <span class="required-star">*</span></label>
                                    <input type="email" id="profileEmail" name="email" class="ts-form-control"
                                        value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="ts-form-group">
                            <label class="ts-form-label" for="profilePhone">Phone Number</label>
                            <input type="tel" id="profilePhone" name="phone" class="ts-form-control" placeholder="+63 917 000 0000">
                        </div>

                        <div class="divider"></div>
                        <p style="font-size:.8125rem;font-weight:700;color:var(--text-primary);margin-bottom:1rem;">Change Password</p>

                        <div class="ts-alert ts-alert-info" style="margin-bottom:1rem;">
                            Leave blank to keep your current password.
                        </div>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="ts-form-group">
                                    <label class="ts-form-label" for="currentPass">Current Password</label>
                                    <input type="password" id="currentPass" name="current_password" class="ts-form-control" placeholder="Enter current password">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="ts-form-group">
                                    <label class="ts-form-label" for="newPass">New Password</label>
                                    <input type="password" id="newPass" name="new_password" class="ts-form-control" placeholder="Min. 8 characters">
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                            <button type="reset" class="btn-ts-secondary">Reset</button>
                            <button type="submit" class="btn-ts-primary">Save Changes</button>
                        </div>

                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<script>
submitFormAjax('#profileForm', {
    successTitle:   'Profile Updated!',
    successMessage: 'Your profile information has been saved successfully.',
    redirectUrl:    null,   /* Stay on page */
    errorTitle:     'Update Failed',
    validate: function(form) {
        var newPass  = form.querySelector('#newPass');
        var currPass = form.querySelector('#currentPass');
        if (newPass && newPass.value) {
            if (!currPass || !currPass.value) {
                showError('Current Password Required', 'Please enter your current password to set a new one.');
                return false;
            }
            if (newPass.value.length < 8) {
                showError('Password Too Short', 'New password must be at least 8 characters long.');
                return false;
            }
        }
    },
    onSuccess: function(data) {
        /* Refresh the displayed name in the topbar avatar without reload */
        var nameInput = document.getElementById('profileName');
        if (nameInput) {
            var displayName = nameInput.value;
            document.querySelectorAll('.topbar-user div[style*="font-weight:600"]').forEach(function(el) {
                el.textContent = displayName;
            });
            var avatarEl = document.querySelector('.avatar');
            if (avatarEl) avatarEl.textContent = displayName.charAt(0).toUpperCase();
            var avatarInit = document.querySelector('.avatar-initials');
            if (avatarInit) avatarInit.textContent = displayName.charAt(0).toUpperCase();
        }
    }
});
</script>

<?php require '../includes/footer.php'; ?>
