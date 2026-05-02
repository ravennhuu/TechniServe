<?php
// client_form.php — Pair A
// Create / Edit client form.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

// Guard: Only Admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: clients.php');
    exit();
}

$editing = isset($_GET['id']);
$client = null;
$unassigned_users = [];

try {
    if ($editing) {
        $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $client = $stmt->fetch();
        if (!$client) {
            header('Location: clients.php');
            exit();
        }
    } else {
        // Fetch users with role 'client' who don't have a client profile yet
        // OR users who are already linked but maybe we want to allow re-assignment?
        // Let's stick to unassigned client users.
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'client' AND client_id IS NULL AND is_active = 1");
        $stmt->execute();
        $unassigned_users = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
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
                <?php $client_action = $editing ? '../api/clients/update.php' : '../api/clients/create.php'; ?>
                <form action="<?php echo $client_action; ?>" method="POST" id="clientForm">
                    <?php if ($editing): ?>
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                    <?php endif; ?> 

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="companyName">Company Name <span class="required-star">*</span></label>
                        <input type="text" id="companyName" name="company_name" class="ts-form-control" 
                            placeholder="e.g. Acme Corporation" required
                            value="<?php echo $client ? htmlspecialchars($client['company_name']) : ''; ?>">
                    </div>


                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="contactPerson">Contact Person <span class="required-star">*</span></label>
                                <input type="text" id="contactPerson" name="contact_person" class="ts-form-control" 
                                    placeholder="Full name" required
                                    value="<?php echo $client ? htmlspecialchars($client['contact_person']) : ''; ?>">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="contactEmail">Email Address <span class="required-star">*</span></label>
                                <input type="email" id="contactEmail" name="contact_email" class="ts-form-control" 
                                    placeholder="contact@company.com" required
                                    value="<?php echo $client ? htmlspecialchars($client['contact_email']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <div class="ts-form-group">
                                <label class="ts-form-label" for="contactPhone">Phone Number</label>
                                <input type="tel" id="contactPhone" name="contact_phone" class="ts-form-control" 
                                    placeholder="+63 917 000 0000"
                                    value="<?php echo $client ? htmlspecialchars($client['contact_phone']) : ''; ?>">
                            </div>
                        </div>
                    </div>

                    <div class="ts-form-group">
                        <label class="ts-form-label" for="clientAddress">Office Address</label>
                        <textarea id="clientAddress" name="address" class="ts-form-control" rows="3" placeholder="Building, street, city, province…"><?php echo $client ? htmlspecialchars($client['address']) : ''; ?></textarea>
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

<script>
var isEditing = <?php echo $editing ? 'true' : 'false'; ?>;

submitFormAjax('#clientForm', {
    successTitle:   isEditing ? 'Client Updated!' : 'Client Created!',
    successMessage: isEditing
        ? 'The client profile has been updated successfully.'
        : 'New client profile has been created successfully.',
    redirectUrl:    'clients.php',
    errorTitle:     'Could Not Save Client'
});
</script>

<?php require '../includes/footer.php'; ?>
