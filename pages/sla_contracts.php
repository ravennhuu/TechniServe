<?php
// sla_contracts.php — Pair A
// SLA contract list table. Dummy data only.
require '../includes/auth.php';
require '../includes/header.php';

// ── Dummy Data ──
$contracts = [
    ['id'=>'SLA-001','client'=>'Acme Corp',    'plan'=>'Enterprise',    'start'=>'2026-01-01','end'=>'2026-12-31','response_sla'=>'1 hour',  'visits'=>'Unlimited','pool'=>'80 / 80h', 'status'=>'active'],
    ['id'=>'SLA-002','client'=>'Globe BPO',    'plan'=>'Professional',  'start'=>'2026-02-01','end'=>'2027-01-31','response_sla'=>'4 hours', 'visits'=>'6 / year', 'pool'=>'32 / 40h', 'status'=>'active'],
    ['id'=>'SLA-003','client'=>'BPI Office',   'plan'=>'Basic',         'start'=>'2026-03-01','end'=>'2027-02-28','response_sla'=>'8 hours', 'visits'=>'2 / year', 'pool'=>'18 / 20h', 'status'=>'active'],
    ['id'=>'SLA-004','client'=>'SM Supermall', 'plan'=>'Professional',  'start'=>'2025-07-01','end'=>'2026-06-30','response_sla'=>'4 hours', 'visits'=>'6 / year', 'pool'=>'5 / 40h',  'status'=>'expiring'],
    ['id'=>'SLA-005','client'=>'Robinsons',    'plan'=>'Basic',         'start'=>'2025-04-01','end'=>'2026-03-31','response_sla'=>'8 hours', 'visits'=>'2 / year', 'pool'=>'0 / 20h',  'status'=>'expired'],
];

$s_map = ['active'=>'badge-resolved','expiring'=>'badge-high','expired'=>'badge-closed'];
$p_map = ['Enterprise'=>'badge-navy','Professional'=>'badge-in-progress','Basic'=>'badge-silver'];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">SLA Contracts</h1>
        <p class="page-subtitle">Formal service level agreements per client account.</p>
    </div>
    <a href="sla_contract_form.php" class="btn-ts-primary">
        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
        New Contract
    </a>
</div>

<div class="ts-card">
    <div class="ts-table-wrap">
        <table class="ts-table">
            <thead>
                <tr>
                    <th>Contract ID</th>
                    <th>Client</th>
                    <th>Plan</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Response SLA</th>
                    <th>Pool Balance</th>
                    <th>Status</th>
                    <th style="width:80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contracts as $c): ?>
                <tr>
                    <td style="font-weight:600;color:var(--navy-action);"><?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['client']); ?></td>
                    <td><span class="ts-badge <?php echo $p_map[$c['plan']] ?? 'badge-silver'; ?>"><?php echo $c['plan']; ?></span></td>
                    <td style="font-size:.8125rem;white-space:nowrap;"><?php echo $c['start']; ?></td>
                    <td style="font-size:.8125rem;white-space:nowrap;"><?php echo $c['end']; ?></td>
                    <td style="font-size:.875rem;"><?php echo $c['response_sla']; ?></td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <span style="font-size:.8125rem; font-weight:600; color:var(--navy-deepest);"><?php echo $c['pool']; ?></span>
                            <div style="width:100%; height:4px; background:#E2E8F0; border-radius:2px; overflow:hidden;">
                                <?php 
                                    $parts = explode(' / ', str_replace('h','',$c['pool']));
                                    $percent = ($parts[1] > 0) ? ($parts[0] / $parts[1]) * 100 : 0;
                                    $color = $percent > 50 ? '#059669' : ($percent > 20 ? '#D97706' : '#DC2626');
                                ?>
                                <div style="width:<?php echo $percent; ?>%; height:100%; background:<?php echo $color; ?>;"></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="ts-badge <?php echo $s_map[$c['status']] ?? 'badge-silver'; ?>"><?php echo ucfirst($c['status']); ?></span></td>
                    <td>
                        <a href="sla_contract_form.php?id=<?php echo urlencode($c['id']); ?>" class="btn-ts-secondary btn-ts-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
