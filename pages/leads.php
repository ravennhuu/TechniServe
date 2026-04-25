<?php
// leads.php — Pair A
// Incoming access requests (leads). Approve / Reject UI. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

// ── Dummy Data ──
$leads = [
    ['id'=>1,'company'=>'Eastern Telecoms','contact'=>'Ramon Villanueva','email'=>'ramon@etel.ph',    'phone'=>'+63 917 111 2233','plan'=>'Professional','submitted'=>'2026-04-25','status'=>'pending'],
    ['id'=>2,'company'=>'Jollibee Foods',  'contact'=>'Lisa Tan',        'email'=>'ltan@jfc.ph',     'phone'=>'+63 918 222 3344','plan'=>'Enterprise',  'submitted'=>'2026-04-24','status'=>'pending'],
    ['id'=>3,'company'=>'PhilHealth',      'contact'=>'Carlos Reyes',    'email'=>'c.reyes@philhealth.ph','phone'=>'+63 922 444 5566','plan'=>'Basic','submitted'=>'2026-04-23','status'=>'approved'],
    ['id'=>4,'company'=>'PAGCOR',          'contact'=>'Ana Cruz',        'email'=>'a.cruz@pagcor.ph','phone'=>'+63 917 555 6677','plan'=>'Enterprise',  'submitted'=>'2026-04-22','status'=>'rejected'],
    ['id'=>5,'company'=>'Unionbank',       'contact'=>'Paolo Mendoza',   'email'=>'p.mendoza@ub.ph', 'phone'=>'+63 919 666 7788','plan'=>'Professional','submitted'=>'2026-04-21','status'=>'pending'],
];

$s_map = ['pending'=>'badge-open','approved'=>'badge-resolved','rejected'=>'badge-closed'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Leads</h1>
        <p class="page-subtitle">Access requests from the landing page. Review and approve or reject each lead.</p>
    </div>
</div>

<!-- Summary Badges -->
<div style="display:flex;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <?php
    $pending  = count(array_filter($leads, fn($l)=>$l['status']==='pending'));
    $approved = count(array_filter($leads, fn($l)=>$l['status']==='approved'));
    $rejected = count(array_filter($leads, fn($l)=>$l['status']==='rejected'));
    ?>
    <div class="kpi-card" style="padding:.75rem 1.25rem;flex:0 0 auto;">
        <div class="kpi-data">
            <div class="kpi-value" style="font-size:1.375rem;"><?php echo $pending; ?></div>
            <div class="kpi-label">Pending Review</div>
        </div>
    </div>
    <div class="kpi-card" style="padding:.75rem 1.25rem;flex:0 0 auto;">
        <div class="kpi-data">
            <div class="kpi-value" style="font-size:1.375rem;color:#059669;"><?php echo $approved; ?></div>
            <div class="kpi-label">Approved</div>
        </div>
    </div>
    <div class="kpi-card" style="padding:.75rem 1.25rem;flex:0 0 auto;">
        <div class="kpi-data">
            <div class="kpi-value" style="font-size:1.375rem;color:#DC2626;"><?php echo $rejected; ?></div>
            <div class="kpi-label">Rejected</div>
        </div>
    </div>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table">
            <thead>
                <tr>
                    <th class="col-id">#</th>
                    <th>Company</th>
                    <th>Contact Person</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Plan</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($leads as $lead): ?>
                <tr>
                    <td class="col-id"><?php echo $lead['id']; ?></td>
                    <td style="font-weight:600;"><?php echo htmlspecialchars($lead['company']); ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($lead['contact']); ?></td>
                    <td style="font-size:.8125rem;"><a href="mailto:<?php echo $lead['email']; ?>" class="text-navy"><?php echo htmlspecialchars($lead['email']); ?></a></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);"><?php echo htmlspecialchars($lead['phone']); ?></td>
                    <td><span class="ts-badge badge-navy"><?php echo $lead['plan']; ?></span></td>
                    <td style="font-size:.8125rem;color:var(--text-muted);white-space:nowrap;"><?php echo $lead['submitted']; ?></td>
                    <td>
                        <span class="ts-badge <?php echo $s_map[$lead['status']]; ?>">
                            <?php echo ucfirst($lead['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($lead['status'] === 'pending'): ?>
                        <div style="display:flex;gap:.375rem;">
                            <button class="btn-ts-success btn-ts-sm" onclick="alert('Approve lead #<?php echo $lead['id']; ?> — backend integration pending.')">Approve</button>
                            <button class="btn-ts-danger btn-ts-sm" onclick="alert('Reject lead #<?php echo $lead['id']; ?> — backend integration pending.')">Reject</button>
                        </div>
                        <?php else: ?>
                        <span style="font-size:.8125rem;color:var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
