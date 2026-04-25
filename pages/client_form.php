<?php
// client_form.php — Pair A
// Create / Edit client form. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

$editing = isset($_GET['id']);
?>

<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="clients.php" class="text-navy">Clients</a>
    <span style="margin:0 .4rem;">/</span>
    <span><?php echo $editing ? 'Edit Client' : 'New Client'; ?></span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo $editing ? 'Edit Client' : 'Add New Client'; ?></h1>
        <p class="page-subtitle">Manage corporate client profile and contact details.</p>
    </div>
    <a href="clients.php" class="btn-ts-secondary">← Cancel</a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Company Information</h5></div>
            <div class="ts-card-body">
                <form action="api/clients/save.php" method="POST" id="clientForm">
                    <?php if ($editing): ?>
                    <input type="hidden" name="client_id" value="<?php echo (int)$_GET['id']; ?>">
                    <?php endif; ?>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="companyName">Company Name <span class="required-star">*</span></label>
                        <input type="text" id="companyName" name="company_name" class="ts-form-control" placeholder="e.g. Acme Corporation" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="contactPerson">Contact Person <span class="required-star">*</span></label>
                                <input type="text" id="contactPerson" name="contact_name" class="ts-form-control" placeholder="Full name" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="contactEmail">Email Address <span class="required-star">*</span></label>
                                <input type="email" id="contactEmail" name="email" class="ts-form-control" placeholder="contact@company.com" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="contactPhone">Phone Number</label>
                                <input type="tel" id="contactPhone" name="phone" class="ts-form-control" placeholder="+63 917 000 0000">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="clientPlan">SLA Plan <span class="required-star">*</span></label>
                                <select id="clientPlan" name="sla_plan" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select Plan —</option>
                                    <option value="basic">Basic</option>
                                    <option value="professional">Professional</option>
                                    <option value="enterprise">Enterprise</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="clientAddress">Office Address</label>
                        <textarea id="clientAddress" name="address" class="ts-form-control" rows="3" placeholder="Building, street, city, province…"></textarea>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="clientStatus">Account Status</label>
                        <select id="clientStatus" name="status" class="ts-form-control ts-form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                        <a href="clients.php" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary">
                            <?php echo $editing ? 'Save Changes' : 'Create Client'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
