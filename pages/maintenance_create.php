<?php
// maintenance_create.php — Pair A
// Log a new maintenance entry form.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin can log maintenance
if ($_SESSION['role'] !== 'admin') {
    header('Location: maintenance.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, company_name FROM clients ORDER BY company_name ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'admin' ORDER BY name ASC");
    $stmt->execute();
    $technicians = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT id, subject, client_id FROM tickets WHERE status != 'closed' ORDER BY id DESC");
    $stmt->execute();
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    $clients = [];
    $technicians = [];
    $tickets = [];
}
?>

<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="maintenance.php" class="text-navy">Maintenance</a>
    <span style="margin:0 .4rem;">/</span>
    <span>Log Entry</span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">Log Maintenance Entry</h1>
        <p class="page-subtitle">Record a completed or scheduled preventive maintenance visit.</p>
    </div>
    <a href="maintenance.php" class="btn-ts-secondary">← Cancel</a>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Maintenance Details</h5></div>
            <div class="ts-card-body">
                <form action="../api/maintenance/create.php" method="POST" id="maintenanceForm">

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainTitle">
                            Title <span class="required-star">*</span>
                        </label>
                        <input type="text" id="mainTitle" name="title" class="ts-form-control"
                            placeholder="e.g. Monthly patch deployment on Acme servers" required>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainTicket">
                            Related Ticket <span class="required-star">*</span>
                        </label>
                        <select id="mainTicket" name="ticket_id" class="ts-form-control ts-form-select" required>
                            <option value="">— Select Ticket —</option>
                            <?php foreach ($tickets as $t): ?>
                            <option value="<?php echo $t['id']; ?>" data-client="<?php echo $t['client_id']; ?>">
                                #<?php echo $t['id']; ?>: <?php echo htmlspecialchars($t['subject']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainClient">
                                    Client <span class="required-star">*</span>
                                </label>
                                <select id="mainClient" name="client_id" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select Client —</option>
                                    <?php foreach ($clients as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['company_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainTech">
                                    Technician <span class="required-star">*</span>
                                </label>
                                <select id="mainTech" name="performed_by" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select Admin —</option>
                                    <?php foreach ($technicians as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainType">
                            Activity Type <span class="required-star">*</span>
                        </label>
                        <select id="mainType" name="activity_type" class="ts-form-control ts-form-select" required>
                            <option value="">— Select Type —</option>
                            <option value="patch">Patching / Updates</option>
                            <option value="backup">Data Backup</option>
                            <option value="network_audit">Network Audit</option>
                            <option value="hardware_repair">Hardware Repair</option>
                            <option value="software_install">Software Installation</option>
                            <option value="site_visit">Site Visit</option>
                            <option value="remote_support">Remote Support</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainDate">Date <span class="required-star">*</span></label>
                                <input type="date" id="mainDate" name="date" class="ts-form-control" required
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainHours">Hours Spent</label>
                                <input type="number" step="0.5" id="mainHours" name="hours_spent" class="ts-form-control"
                                    value="1.0" min="0">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainStatus">Status <span class="required-star">*</span></label>
                                <select id="mainStatus" name="status" class="ts-form-control ts-form-select" required>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainDesc">Description</label>
                        <textarea id="mainDesc" name="description" class="ts-form-control" rows="4"
                            placeholder="Summary of work performed, equipment checked, issues found…"></textarea>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                        <a href="maintenance.php" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary">Save Entry</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
/* Auto-populate client when a ticket is selected */
var ticketSelect = document.getElementById('mainTicket');
var clientSelect = document.getElementById('mainClient');
if (ticketSelect && clientSelect) {
    ticketSelect.addEventListener('change', function () {
        var opt = this.options[this.selectedIndex];
        var cid = opt ? opt.getAttribute('data-client') : '';
        if (cid) {
            for (var i = 0; i < clientSelect.options.length; i++) {
                if (clientSelect.options[i].value === cid) {
                    clientSelect.selectedIndex = i;
                    break;
                }
            }
        }
    });
}

submitFormAjax('#maintenanceForm', {
    successTitle:   'Maintenance Logged!',
    successMessage: 'The maintenance entry has been saved. SLA hours will be deducted if the status is Completed.',
    redirectUrl:    'maintenance.php',
    errorTitle:     'Could Not Save Entry',
    validate: function(form) {
        var hours = form.querySelector('#mainHours');
        if (hours && parseFloat(hours.value) < 0) {
            showError('Invalid Hours', 'Hours spent cannot be negative.');
            return false;
        }
    }
});
</script>

<?php require '../includes/footer.php'; ?>
