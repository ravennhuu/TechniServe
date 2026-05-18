<?php
// sla_contract_form.php — Pair A
// Create / Edit SLA contract form.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin can manage contracts
if ($_SESSION['role'] !== 'admin') {
    header('Location: sla_contracts.php');
    exit();
}

$editing = isset($_GET['id']);
$contract = null;

try {
    $stmt = $pdo->prepare("SELECT id, company_name FROM clients ORDER BY company_name ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll();

    if ($editing) {
        $stmt = $pdo->prepare("SELECT * FROM sla_contracts WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $contract = $stmt->fetch();
        
        if (!$contract) {
            header('Location: sla_contracts.php');
            exit();
        }
    }
} catch (PDOException $e) {
    $clients = [];
}
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
                <?php $sla_action = $editing ? '../api/sla/update.php' : '../api/sla/create.php'; ?>
                <form action="<?php echo $sla_action; ?>" method="POST" id="slaForm">
                    <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <?php endif; ?>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="slaClient">
                            Client <span class="required-star">*</span>
                        </label>
                        <select id="slaClient" name="client_id" class="ts-form-control ts-form-select" required>
                            <option value="">— Select Client —</option>
                            <?php foreach ($clients as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo ($contract && $contract['client_id'] == $c['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($c['company_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaStart">Start Date <span class="required-star">*</span></label>
                                <input type="date" id="slaStart" name="start_date" class="ts-form-control" required
                                    value="<?php echo $contract ? $contract['start_date'] : date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaEnd">End Date <span class="required-star">*</span></label>
                                <input type="date" id="slaEnd" name="end_date" class="ts-form-control" required
                                    value="<?php echo $contract ? $contract['end_date'] : date('Y-m-d', strtotime('+1 year')); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaHours">Monthly Support Hours</label>
                                <input type="number" step="0.5" id="slaHours" name="monthly_hours_pool" class="ts-form-control"
                                    placeholder="e.g. 20" value="<?php echo $contract ? $contract['monthly_hours_pool'] : '20'; ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaVisits">Site Visits per Year</label>
                                <input type="number" id="slaVisits" name="site_visits_included" class="ts-form-control"
                                    placeholder="e.g. 2" value="<?php echo $contract ? $contract['site_visits_included'] : '2'; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaResponse">Response Time SLA (hours) <span class="required-star">*</span></label>
                                <select id="slaResponse" name="response_time_hrs" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select —</option>
                                    <option value="1" <?php echo ($contract && $contract['response_time_hrs'] == 1) ? 'selected' : ''; ?>>1 Hour (Critical Enterprise)</option>
                                    <option value="4" <?php echo ($contract && $contract['response_time_hrs'] == 4) ? 'selected' : ''; ?>>4 Hours (Professional)</option>
                                    <option value="8" <?php echo ($contract && $contract['response_time_hrs'] == 8) ? 'selected' : ''; ?>>8 Hours (Basic)</option>
                                    <option value="24" <?php echo ($contract && $contract['response_time_hrs'] == 24) ? 'selected' : ''; ?>>Next Business Day</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaResolution">Resolution Time SLA (hours) <span class="required-star">*</span></label>
                                <select id="slaResolution" name="resolution_time_hrs" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select —</option>
                                    <option value="4"  <?php echo ($contract && $contract['resolution_time_hrs'] == 4) ? 'selected' : ''; ?>>4 Hours</option>
                                    <option value="12" <?php echo ($contract && $contract['resolution_time_hrs'] == 12) ? 'selected' : ''; ?>>12 Hours</option>
                                    <option value="24" <?php echo ($contract && $contract['resolution_time_hrs'] == 24) ? 'selected' : ''; ?>>24 Hours</option>
                                    <option value="48" <?php echo ($contract && $contract['resolution_time_hrs'] == 48) ? 'selected' : ''; ?>>48 Hours</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="slaStatus">Contract Status</label>
                        <select id="slaStatus" name="is_active" class="ts-form-control ts-form-select">
                            <option value="1" <?php echo ($contract && $contract['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo ($contract && $contract['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
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

<script>
var isEditing = <?php echo $editing ? 'true' : 'false'; ?>;

submitFormAjax('#slaForm', {
    successTitle:   isEditing ? 'Contract Updated!' : 'Contract Created!',
    successMessage: isEditing
        ? 'The SLA contract has been updated successfully.'
        : 'New SLA contract has been created and activated. Any previous active contract for this client has been deactivated.',
    redirectUrl:    'sla_contracts.php',
    errorTitle:     'Could Not Save Contract',
    validate: function(form) {
        var start = form.querySelector('#slaStart');
        var end   = form.querySelector('#slaEnd');
        if (start && end && start.value && end.value && start.value >= end.value) {
            showError('Invalid Dates', 'The end date must be after the start date.');
            return false;
        }
    }
});
</script>

<?php require '../includes/footer.php'; ?>
