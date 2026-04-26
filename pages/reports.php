<?php
// reports.php — Pair A
// Business intelligence charts.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

try {
    if ($_SESSION['role'] === 'admin') {
        // Overall stats for admin
        $stmt = $pdo->prepare("SELECT AVG(compliance_pct) FROM reports");
        $stmt->execute();
        $overall_compliance = round($stmt->fetchColumn() ?? 100, 1) . '%';

        $stmt = $pdo->prepare("SELECT AVG(avg_response_hrs) FROM reports");
        $stmt->execute();
        $avg_resolution = round($stmt->fetchColumn() ?? 0, 1) . 'h';

        $stmt = $pdo->prepare("SELECT SUM(total_tickets) FROM reports");
        $stmt->execute();
        $total_tickets = $stmt->fetchColumn() ?? 0;

        // History
        $stmt = $pdo->prepare("
            SELECT r.*, c.company_name
            FROM reports r
            JOIN clients c ON r.client_id = c.id
            ORDER BY r.year DESC, r.month DESC
        ");
        $stmt->execute();
        $history = $stmt->fetchAll();
    } else {
        // Client specific stats
        $client_id = $_SESSION['client_id'];
        
        $stmt = $pdo->prepare("SELECT AVG(compliance_pct) FROM reports WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $overall_compliance = round($stmt->fetchColumn() ?? 100, 1) . '%';

        $stmt = $pdo->prepare("SELECT AVG(avg_response_hrs) FROM reports WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $avg_resolution = round($stmt->fetchColumn() ?? 0, 1) . 'h';

        $stmt = $pdo->prepare("SELECT SUM(total_tickets) FROM reports WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $total_tickets = $stmt->fetchColumn() ?? 0;

        // History
        $stmt = $pdo->prepare("
            SELECT r.*, c.company_name
            FROM reports r
            JOIN clients c ON r.client_id = c.id
            WHERE r.client_id = ?
            ORDER BY r.year DESC, r.month DESC
        ");
        $stmt->execute([$client_id]);
        $history = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $overall_compliance = '0%';
    $avg_resolution = '0h';
    $total_tickets = 0;
    $history = [];
}

function getMonthName($n) {
    return date('F', mktime(0, 0, 0, $n, 10));
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Reports</h1>
        <p class="page-subtitle">SLA compliance and support performance analytics.</p>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;">
        <select class="ts-form-control ts-form-select" id="reportPeriod" style="width:auto;">
            <option value="30">Last 30 Days</option>
            <option value="90">Last 90 Days</option>
            <option value="180">Last 6 Months</option>
            <option value="365">Last 12 Months</option>
        </select>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] !== 'client'): ?>
        <button class="btn-ts-primary" onclick="alert('Auto-generating formal summary of resolved tickets and uptime...')">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m3.243-9.743a4 4 0 115.657 0L12 10l-.914-.914a4 4 0 115.657 0M12 14v7m-3-3l3 3 3-3"/></svg>
            Generate Monthly Report
        </button>
        <?php endif; ?>
    </div>
</div>

<!-- KPI Summary Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon navy">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $overall_compliance; ?></div>
                <div class="kpi-label">SLA Compliance Rate</div>
                <div class="kpi-trend up">↑ On track</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $avg_resolution; ?></div>
                <div class="kpi-label">Avg. Resolution Time</div>
                <div class="kpi-trend">System average</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value"><?php echo $total_tickets; ?></div>
                <div class="kpi-label">Total Tickets Filed</div>
                <div class="kpi-trend" style="color:var(--steel-blue);">All time</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon green">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value">94%</div>
                <div class="kpi-label">First-Contact Resolution</div>
                <div class="kpi-trend up">↑ Strong performance</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3">

    <div class="col-lg-6">
        <div class="ts-card">
            <div class="ts-card-header">
                <h5 class="ts-card-title">Avg. Ticket Resolution Time</h5>
                <span class="ts-badge badge-navy">By Month (hrs)</span>
            </div>
            <div class="ts-card-body">
                <div class="chart-wrap">
                    <canvas id="resolutionChart" width="400" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="ts-card">
            <div class="ts-card-header">
                <h5 class="ts-card-title">Peak Support Request Days</h5>
                <span class="ts-badge badge-resolved">By Day of Week</span>
            </div>
            <div class="ts-card-body">
                <div class="chart-wrap">
                    <canvas id="peakDaysChart" width="400" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

<!-- Monthly Reports Archive -->
<div class="ts-card mt-4">
    <div class="ts-card-header">
        <h5 class="ts-card-title">Monthly Service Report History</h5>
        <span class="ts-badge badge-silver">Auto-Generated Summaries</span>
    </div>
    <div class="ts-table-wrap">
        <table class="ts-table">
            <thead>
                <tr>
                    <th>Report Period</th>
                    <th>Resolved Tickets</th>
                    <th>Avg. Uptime</th>
                    <th>Compliance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $r): ?>
                <tr>
                    <td><?php echo getMonthName($r['month']) . ' ' . $r['year']; ?></td>
                    <td><?php echo $r['total_tickets']; ?> Tickets</td>
                    <td style="font-weight:600;"><?php echo $r['avg_uptime_pct']; ?>%</td>
                    <td>
                        <?php if ($r['compliance_pct'] >= 95): ?>
                        <span class="ts-badge badge-resolved">Compliant</span>
                        <?php else: ?>
                        <span class="ts-badge badge-critical">Non-Compliant</span>
                        <?php endif; ?>
                    </td>
                    <td><button class="btn-ts-secondary btn-ts-sm" onclick="alert('PDF generation feature coming soon.')">Download PDF</button></td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($history)): ?>
                <tr><td colspan="5" class="text-center py-4 text-muted">No report history found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../public/js/chart.min.js"></script>
<script src="../public/js/charts.js"></script>
<?php require '../includes/footer.php'; ?>
