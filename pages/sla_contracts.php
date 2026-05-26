<?php
// sla_contracts.php — Pair A
// SLA contract list table.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

try {
    if ($_SESSION['role'] === 'admin') {
        $stmt = $pdo->prepare("
            SELECT s.*, c.company_name as client, COALESCE(v.hours_used, 0) as hours_used
            FROM sla_contracts s
            JOIN clients c ON s.client_id = c.id
            LEFT JOIN v_sla_usage v ON v.client_id = s.client_id AND v.contract_id = s.id
            ORDER BY c.company_name ASC
        ");
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare("
            SELECT s.*, c.company_name as client, COALESCE(v.hours_used, 0) as hours_used
            FROM sla_contracts s
            JOIN clients c ON s.client_id = c.id
            LEFT JOIN v_sla_usage v ON v.client_id = s.client_id AND v.contract_id = s.id
            WHERE s.client_id = ?
            ORDER BY s.id DESC
        ");
        $stmt->execute([$_SESSION['client_id']]);
    }
    $contracts = $stmt->fetchAll();
} catch (PDOException $e) {
    $contracts = [];
}

$s_map = ['active'=>'badge-resolved','expiring'=>'badge-high','expired'=>'badge-closed','inactive'=>'badge-silver'];

function getPlanName($hours) {
    if ($hours < 20) return 'Basic';
    if ($hours < 40) return 'Professional';
    return 'Enterprise';
}
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
                    <td style="font-weight:600;color:var(--navy-action);">#<?php echo $c['id']; ?></td>
                    <td><?php echo htmlspecialchars($c['client']); ?></td>
                    <?php $plan = getPlanName($c['monthly_hours_pool']); ?>
                    <td><span class="ts-badge <?php echo $p_map[$plan] ?? 'badge-silver'; ?>"><?php echo $plan; ?></span></td>
                    <td style="font-size:.8125rem;white-space:nowrap;"><?php echo $c['start_date']; ?></td>
                    <td style="font-size:.8125rem;white-space:nowrap;"><?php echo $c['end_date']; ?></td>
                    <td style="font-size:.875rem;"><?php echo $c['response_time_hrs']; ?> hours</td>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:2px;">
                            <span style="font-size:.8125rem; font-weight:600; color:var(--navy-deepest);">
                                <?php echo number_format($c['monthly_hours_pool'] - $c['hours_used'], 1); ?> / <?php echo $c['monthly_hours_pool']; ?>h
                            </span>
                            <div style="width:100%; height:4px; background:#E2E8F0; border-radius:2px; overflow:hidden;">
                                <?php 
                                    $remaining = $c['monthly_hours_pool'] - $c['hours_used'];
                                    $percent = ($c['monthly_hours_pool'] > 0) ? ($remaining / $c['monthly_hours_pool']) * 100 : 0;
                                    $color = $percent > 50 ? '#059669' : ($percent > 20 ? '#D97706' : '#DC2626');
                                ?>
                                <div style="width:<?php echo max(0, min(100, $percent)); ?>%; height:100%; background:<?php echo $color; ?>;"></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php
                            $status = 'active';
                            if (!$c['is_active']) $status = 'inactive';
                            elseif (strtotime($c['end_date']) < time()) $status = 'expired';
                            elseif (strtotime($c['end_date']) < strtotime('+30 days')) $status = 'expiring';
                        ?>
                        <span class="ts-badge <?php echo $s_map[$status] ?? 'badge-silver'; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </td>
                    <td>
                        <a href="sla_contract_form.php?id=<?php echo $c['id']; ?>" class="btn-ts-secondary btn-ts-sm">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>
