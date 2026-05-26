<?php
// ticket_edit.php — Pair A
// Edit support ticket form.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Prevent clients from accessing the edit page
if (isset($_SESSION['role']) && $_SESSION['role'] === 'client') {
    header("Location: tickets.php");
    exit;
}

$ticket_id = $_GET['id'] ?? null;
if (!$ticket_id) {
    header('Location: tickets.php');
    exit();
}

try {
    $stmt = $pdo->prepare("
        SELECT t.*, c.company_name as client
        FROM tickets t
        JOIN clients c ON t.client_id = c.id
        WHERE t.id = ?
    ");
    $stmt->execute([$ticket_id]);
    $ticket = $stmt->fetch();

    if (!$ticket) {
        header('Location: tickets.php');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="tickets.php" class="text-navy">Tickets</a>
    <span style="margin:0 .4rem;">/</span>
    <a href="ticket_view.php?id=<?php echo $ticket['id']; ?>" class="text-navy">#<?php echo $ticket['id']; ?></a>
    <span style="margin:0 .4rem;">/</span>
    <span>Edit</span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Ticket #<?php echo $ticket['id']; ?></h1>
        <p class="page-subtitle"><?php echo htmlspecialchars($ticket['subject']); ?></p>
    </div>
    <a href="tickets.php" class="btn-ts-secondary">← Back to Tickets</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Update Ticket Status & Notify Client</h5></div>
            <div class="ts-card-body">
                <form action="../api/tickets/update.php" method="POST" id="editTicketForm">
                    <input type="hidden" name="id" value="<?php echo $ticket['id']; ?>">
                    
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="ticketStatus">
                                    Status <span class="required-star">*</span>
                                </label>
                                <select id="ticketStatus" name="status" class="ts-form-control ts-form-select" required>
                                    <option value="open" <?php if($ticket['status'] == 'open') echo 'selected'; ?>>Open</option>
                                    <option value="in_progress" <?php if($ticket['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                                    <option value="resolved" <?php if($ticket['status'] == 'resolved') echo 'selected'; ?>>Resolved</option>
                                    <option value="closed" <?php if($ticket['status'] == 'closed') echo 'selected'; ?>>Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="ticketPriority">Priority</label>
                                <select id="ticketPriority" name="priority" class="ts-form-control ts-form-select">
                                    <option value="critical" <?php if($ticket['priority'] == 'critical') echo 'selected'; ?>>🔴 Critical</option>
                                    <option value="high" <?php if($ticket['priority'] == 'high') echo 'selected'; ?>>🟠 High</option>
                                    <option value="low" <?php if($ticket['priority'] == 'low') echo 'selected'; ?>>🟢 Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4" id="slaDeductionContainer" style="<?php echo in_array($ticket['status'], ['closed', 'resolved']) ? '' : 'display:none;'; ?>">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="slaDeduction">
                                    SLA Deduction (Hours)
                                </label>
                                <input type="number" id="slaDeduction" name="sla_deduction" class="ts-form-control" step="0.5" min="0" placeholder="e.g. 1.5">
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="clientUpdate">
                            Update Client (Message)
                        </label>
                        <textarea id="clientUpdate" name="note"
                            class="ts-form-control" rows="6"
                            placeholder="Type an update to send to the client..." required></textarea>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                        <a href="ticket_view.php?id=<?php echo $ticket['id']; ?>" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary" id="saveTicketBtn">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right Side Info -->
    <div class="col-lg-4">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Ticket Information</h5></div>
            <div class="ts-card-body">
                <ul style="list-style:none;padding:0;margin:0;font-size:.875rem;">
                    <li style="padding:.5rem 0;border-bottom:1px solid var(--border-color);">
                        <span style="color:var(--text-muted);font-weight:600;display:inline-block;width:90px;">Client:</span>
                        <span><?php echo htmlspecialchars($ticket['client']); ?></span>
                    </li>
                </ul>
                <div style="margin-top: 1rem;">
                    <p style="font-size: .8125rem; color: var(--text-muted);">
                        Updating the ticket status or adding a message will automatically record an entry in the activity trail and notify the client if applicable.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
submitFormAjax('#editTicketForm', {
    successTitle:   'Ticket Updated!',
    successMessage: 'The ticket status has been updated and the activity trail logged.',
    redirectUrl:    'ticket_view.php?id=<?php echo $ticket['id']; ?>',
    errorTitle:     'Update Failed',
    validate: function(form) {
        var note = form.querySelector('#clientUpdate');
        if (note && !note.value.trim()) {
            showError('Note Required', 'Please provide an update message before saving.');
            return false;
        }
    }
});

document.getElementById('ticketStatus').addEventListener('change', function() {
    var container = document.getElementById('slaDeductionContainer');
    if (this.value === 'closed' || this.value === 'resolved') {
        container.style.display = 'block';
    } else {
        container.style.display = 'none';
        document.getElementById('slaDeduction').value = '';
    }
});
</script>

<?php require '../includes/footer.php'; ?>
