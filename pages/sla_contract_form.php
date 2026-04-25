<?php
// sla_contract_form.php — Pair A
// Create / Edit SLA contract form. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

$clients = ['Acme Corp','Globe BPO','BPI Office','SM Supermall','Robinsons','Ayala Land','PLDT','Meralco'];
$editing = isset($_GET['id']); // true when editing
?>

<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="sla_contracts.php" class="text-navy">SLA Contracts</a>
    <span style="margin:0 .4rem;">/</span>
    <span><?php echo $editing ? 'Edit Contract' : 'New Contract'; ?></span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title"><?php echo $editing ? 'Edit SLA Contract' : 'New SLA Contract'; ?></h1>
        <p class="page-subtitle">Define the service level terms for a client account.</p>
    </div>
    <a href="sla_contracts.php" class="btn-ts-secondary">← Cancel</a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Contract Details</h5></div>
            <div class="ts-card-body">
                <form action="api/sla/save.php" method="POST" id="slaForm">
                    <?php if ($editing): ?>
                    <input type="hidden" name="contract_id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <?php endif; ?>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="slaClient">
                            Client <span class="required-star">*</span>
                        </label>
                        <select id="slaClient" name="client_id" class="ts-form-control ts-form-select" required>
                            <option value="">— Select Client —</option>
                            <?php foreach ($clients as $i => $c): ?>
                            <option value="<?php echo $i+1; ?>"><?php echo htmlspecialchars($c); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="slaPlan">
                            Service Plan <span class="required-star">*</span>
                        </label>
                        <select id="slaPlan" name="plan" class="ts-form-control ts-form-select" required>
                            <option value="">— Select Plan —</option>
                            <option value="basic">Basic</option>
                            <option value="professional">Professional</option>
                            <option value="enterprise">Enterprise</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaStart">Start Date <span class="required-star">*</span></label>
                                <input type="date" id="slaStart" name="start_date" class="ts-form-control" required>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaEnd">End Date <span class="required-star">*</span></label>
                                <input type="date" id="slaEnd" name="end_date" class="ts-form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaHours">Monthly Support Hours</label>
                                <input type="number" id="slaHours" name="support_hours" class="ts-form-control"
                                    placeholder="e.g. 60 (leave blank for unlimited)">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaVisits">Site Visits per Year</label>
                                <input type="number" id="slaVisits" name="site_visits" class="ts-form-control"
                                    placeholder="e.g. 6 (leave blank for unlimited)">
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="slaResponse">Response Time SLA (hours) <span class="required-star">*</span></label>
                        <select id="slaResponse" name="response_hours" class="ts-form-control ts-form-select" required>
                            <option value="">— Select —</option>
                            <option value="1">1 Hour (Critical Enterprise)</option>
                            <option value="4">4 Hours (Professional)</option>
                            <option value="8">8 Hours (Basic)</option>
                            <option value="24">Next Business Day</option>
                        </select>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="slaTerms">Special Terms / Notes</label>
                        <textarea id="slaTerms" name="terms" class="ts-form-control" rows="4"
                            placeholder="Any additional terms, custom SLAs, or exclusions for this contract…"></textarea>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                        <a href="sla_contracts.php" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary">
                            <?php echo $editing ? 'Save Changes' : 'Create Contract'; ?>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
