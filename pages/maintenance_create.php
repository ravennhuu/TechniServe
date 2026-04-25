<?php
// maintenance_create.php — Pair A
// Log a new maintenance entry form. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

$clients     = ['Acme Corp','Globe BPO','BPI Office','SM Supermall','Robinsons','Ayala Land','PLDT','Meralco'];
$technicians = ['J. Reyes','M. Santos','R. Cruz','A. dela Rosa'];
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
                <form action="api/maintenance/create.php" method="POST" id="maintenanceForm">

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainClient">
                                    Client <span class="required-star">*</span>
                                </label>
                                <select id="mainClient" name="client_id" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select Client —</option>
                                    <?php foreach ($clients as $i => $c): ?>
                                    <option value="<?php echo $i+1; ?>"><?php echo htmlspecialchars($c); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainTech">
                                    Technician <span class="required-star">*</span>
                                </label>
                                <select id="mainTech" name="technician_id" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select Technician —</option>
                                    <?php foreach ($technicians as $i => $t): ?>
                                    <option value="<?php echo $i+1; ?>"><?php echo htmlspecialchars($t); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainType">
                            Maintenance Type <span class="required-star">*</span>
                        </label>
                        <select id="mainType" name="type" class="ts-form-control ts-form-select" required>
                            <option value="">— Select Type —</option>
                            <option value="quarterly_server_check">Quarterly Server Check</option>
                            <option value="network_audit">Network Audit</option>
                            <option value="ups_battery">UPS Battery Replacement</option>
                            <option value="cctv_inspection">CCTV System Inspection</option>
                            <option value="firewall_update">Firewall Firmware Update</option>
                            <option value="workstation_cleanup">Workstation Cleanup</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainDate">
                                    Date <span class="required-star">*</span>
                                </label>
                                <input type="date" id="mainDate" name="date" class="ts-form-control" required
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainStatus">
                                    Status <span class="required-star">*</span>
                                </label>
                                <select id="mainStatus" name="status" class="ts-form-control ts-form-select" required>
                                    <option value="scheduled">Scheduled</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainNotes">Notes</label>
                        <textarea id="mainNotes" name="notes" class="ts-form-control" rows="4"
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

<?php require '../includes/footer.php'; ?>
