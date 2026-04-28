<?php
// maintenance_edit.php — Pair A
// Edit an existing maintenance log.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: dashboard.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: maintenance.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM maintenance_logs WHERE id = ?");
    $stmt->execute([$id]);
    $log = $stmt->fetch();

    if (!$log) {
        header('Location: maintenance.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT id, company_name FROM clients ORDER BY company_name ASC");
    $stmt->execute();
    $clients = $stmt->fetchAll();

    $stmt = $pdo->prepare("SELECT id, subject, client_id FROM tickets ORDER BY id DESC");
    $stmt->execute();
    $tickets = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Maintenance Log</h1>
        <p class="page-subtitle">Update maintenance log details or mark as completed to deduct SLA hours.</p>
    </div>
    <a href="maintenance_view.php?id=<?php echo $log['id']; ?>" class="btn-ts-secondary">← Back to View</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Maintenance Details</h5></div>
            <div class="ts-card-body">

                <form action="../api/maintenance/update.php" method="POST" id="maintenanceForm">
                    <input type="hidden" name="id" value="<?php echo $log['id']; ?>">

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainTitle">
                            Title <span class="required-star">*</span>
                        </label>
                        <input type="text" id="mainTitle" name="title" class="ts-form-control"
                            value="<?php echo htmlspecialchars($log['title']); ?>" required>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainTicket">
                            Related Ticket <span class="required-star">*</span>
                        </label>
                        <select id="mainTicket" name="ticket_id" class="ts-form-control ts-form-select" required>
                            <option value="">-- Select Ticket --</option>
                            <?php foreach ($tickets as $t): ?>
                            <option value="<?php echo $t['id']; ?>" data-client="<?php echo $t['client_id']; ?>" <?php if ($t['id'] == $log['ticket_id']) echo 'selected'; ?>>
                                #<?php echo $t['id']; ?> - <?php echo htmlspecialchars($t['subject']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainClient">
                            Client <span class="required-star">*</span>
                        </label>
                        <select id="mainClient" name="client_id" class="ts-form-control ts-form-select" required>
                            <option value="">-- Select Client --</option>
                            <?php foreach ($clients as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if ($c['id'] == $log['client_id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($c['company_name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div style="font-size:.8125rem;color:var(--text-muted);margin-top:.3rem;">
                            Auto-selected when you choose a ticket.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainType">Activity Type</label>
                                <select id="mainType" name="activity_type" class="ts-form-control ts-form-select">
                                    <?php
                                    $types = ['patch','backup','network_audit','hardware_repair','software_install','site_visit','remote_support','other'];
                                    foreach ($types as $type) {
                                        $sel = ($type === $log['activity_type']) ? 'selected' : '';
                                        $lbl = ucfirst(str_replace('_',' ',$type));
                                        echo "<option value=\"$type\" $sel>$lbl</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="mainHours">Hours Spent (SLA Deduction)</label>
                                <input type="number" id="mainHours" name="hours_spent" class="ts-form-control" 
                                       step="0.25" min="0" value="<?php echo $log['hours_spent']; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainStatus">Status</label>
                        <select id="mainStatus" name="status" class="ts-form-control ts-form-select">
                            <option value="scheduled" <?php if ($log['status'] === 'scheduled') echo 'selected'; ?>>Scheduled</option>
                            <option value="in_progress" <?php if ($log['status'] === 'in_progress') echo 'selected'; ?>>In Progress</option>
                            <option value="completed" <?php if ($log['status'] === 'completed') echo 'selected'; ?>>Completed</option>
                            <option value="cancelled" <?php if ($log['status'] === 'cancelled') echo 'selected'; ?>>Cancelled</option>
                        </select>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="mainDesc">Description & Scope of Work</label>
                        <textarea id="mainDesc" name="description" class="ts-form-control" rows="4"><?php echo htmlspecialchars($log['description']); ?></textarea>
                    </div>

                    <div style="display:flex;justify-content:flex-end;gap:.625rem;margin-top:1.5rem;">
                        <a href="maintenance_view.php?id=<?php echo $log['id']; ?>" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary">Save Changes</button>
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
    successTitle:   'Maintenance Updated!',
    successMessage: 'The maintenance entry has been updated. SLA hours have been automatically adjusted if necessary.',
    redirectUrl:    'maintenance_view.php?id=<?php echo $log['id']; ?>',
    errorTitle:     'Could Not Save Changes',
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
