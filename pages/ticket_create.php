<?php
// ticket_create.php — Pair A
// Create new support ticket form. Dummy data only (no real submit logic).
require '../includes/auth.php';
require '../includes/header.php';

// Dummy clients & technicians for dropdowns
$clients = ['Acme Corp','Globe BPO','BPI Office','SM Supermall','Robinsons','Ayala Land','PLDT','Meralco'];
$technicians = ['J. Reyes','M. Santos','R. Cruz','A. dela Rosa'];
?>

<nav style="font-size:.8125rem;color:var(--text-muted);margin-bottom:1.25rem;">
    <a href="tickets.php" class="text-navy">Tickets</a>
    <span style="margin:0 .4rem;">/</span>
    <span>New Ticket</span>
</nav>

<div class="page-header">
    <div>
        <h1 class="page-title">Create New Ticket</h1>
        <p class="page-subtitle">Fill in the details below to open a new support ticket.</p>
    </div>
    <a href="tickets.php" class="btn-ts-secondary">← Cancel</a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Ticket Information</h5></div>
            <div class="ts-card-body">
                <form action="api/tickets/create.php" method="POST" id="createTicketForm">

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="ticketSubject">
                            Subject <span class="required-star">*</span>
                        </label>
                        <input type="text" id="ticketSubject" name="subject"
                            class="ts-form-control" placeholder="Brief description of the issue" required>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="ticketDescription">
                            Description <span class="required-star">*</span>
                        </label>
                        <textarea id="ticketDescription" name="description"
                            class="ts-form-control" rows="5"
                            placeholder="Describe the issue in detail — what happened, when it started, and what has been tried…" required></textarea>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="ticketPriority">
                                    Priority <span class="required-star">*</span>
                                </label>
                                <select id="ticketPriority" name="priority" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select —</option>
                                    <option value="critical">🔴 Critical</option>
                                    <option value="high">🟠 High</option>
                                    <option value="medium">🔵 Medium</option>
                                    <option value="low">🟢 Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="ticketClient">
                                    Client <span class="required-star">*</span>
                                </label>
                                <select id="ticketClient" name="client_id" class="ts-form-control ts-form-select" required>
                                    <option value="">— Select Client —</option>
                                    <?php foreach ($clients as $i => $c): ?>
                                    <option value="<?php echo $i+1; ?>"><?php echo htmlspecialchars($c); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="ticketAssigned">Assign To</label>
                                <select id="ticketAssigned" name="assigned_to" class="ts-form-control ts-form-select">
                                    <option value="">— Unassigned —</option>
                                    <?php foreach ($technicians as $i => $tech): ?>
                                    <option value="<?php echo $i+1; ?>"><?php echo htmlspecialchars($tech); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="ticketCategory">Category</label>
                                <select id="ticketCategory" name="category" class="ts-form-control ts-form-select">
                                    <option value="">— Select —</option>
                                    <option value="network">Network</option>
                                    <option value="hardware">Hardware</option>
                                    <option value="software">Software</option>
                                    <option value="email">Email / Communication</option>
                                    <option value="security">Security</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
                        <a href="tickets.php" class="btn-ts-secondary">Cancel</a>
                        <button type="submit" class="btn-ts-primary" id="submitTicketBtn">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                            Create Ticket
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="col-lg-4">
        <div class="ts-card">
            <div class="ts-card-header"><h5 class="ts-card-title">Priority Guide</h5></div>
            <div class="ts-card-body">
                <ul style="list-style:none;padding:0;margin:0;font-size:.875rem;">
                    <li style="padding:.5rem 0;border-bottom:1px solid var(--border-color);">
                        <span class="ts-badge badge-critical" style="margin-bottom:.25rem;">Critical</span>
                        <div style="color:var(--text-muted);font-size:.8125rem;margin-top:.25rem;">Total outage affecting business operations. Response SLA: 1 hour.</div>
                    </li>
                    <li style="padding:.5rem 0;border-bottom:1px solid var(--border-color);">
                        <span class="ts-badge badge-high" style="margin-bottom:.25rem;">High</span>
                        <div style="color:var(--text-muted);font-size:.8125rem;margin-top:.25rem;">Significant impact on a team or department. Response SLA: 4 hours.</div>
                    </li>
                    <li style="padding:.5rem 0;border-bottom:1px solid var(--border-color);">
                        <span class="ts-badge badge-medium" style="margin-bottom:.25rem;">Medium</span>
                        <div style="color:var(--text-muted);font-size:.8125rem;margin-top:.25rem;">Reduced functionality but workaround exists. Response SLA: 8 hours.</div>
                    </li>
                    <li style="padding:.5rem 0;">
                        <span class="ts-badge badge-low" style="margin-bottom:.25rem;">Low</span>
                        <div style="color:var(--text-muted);font-size:.8125rem;margin-top:.25rem;">Minor issue or request. Response SLA: Next business day.</div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
