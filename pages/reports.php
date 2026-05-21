<?php
// reports.php — Pair A
// Business intelligence charts.
require '../includes/auth.php';
require '../includes/db.php';
require '../includes/header.php';

try {
    if ($_SESSION['role'] === 'admin') {
        // Overall stats for admin
        $stmt = $pdo->prepare("SELECT AVG(compliance_pct) FROM v_monthly_report");
        $stmt->execute();
        $overall_compliance = round($stmt->fetchColumn() ?? 100, 1) . '%';

        $stmt = $pdo->prepare("SELECT AVG(avg_response_hrs) FROM v_monthly_report");
        $stmt->execute();
        $avg_resolution = round($stmt->fetchColumn() ?? 0, 1) . 'h';

        $stmt = $pdo->prepare("SELECT SUM(total_tickets) FROM v_monthly_report");
        $stmt->execute();
        $total_tickets = $stmt->fetchColumn() ?? 0;

        $stmt = $pdo->prepare("
            SELECT *
            FROM v_monthly_report
            ORDER BY year DESC, month DESC
        ");
        $stmt->execute();
        $history = $stmt->fetchAll();

        // Chart Data: Avg Resolution by Month (Admin)
        $stmt = $pdo->prepare("
            SELECT MONTH(resolved_at) as m, AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hrs
            FROM tickets 
            WHERE resolved_at IS NOT NULL AND YEAR(resolved_at) = YEAR(CURDATE())
            GROUP BY MONTH(resolved_at)
        ");
        $stmt->execute();
        $chart_res_data = $stmt->fetchAll();

        // Chart Data: Peak Days (Admin)
        $stmt = $pdo->prepare("
            SELECT DAYOFWEEK(created_at) as d, COUNT(*) as cnt
            FROM tickets
            WHERE YEAR(created_at) = YEAR(CURDATE())
            GROUP BY DAYOFWEEK(created_at)
        ");
        $stmt->execute();
        $chart_days_data = $stmt->fetchAll();

    } else {
        // Client specific stats
        $client_id = $_SESSION['client_id'];
        
        $stmt = $pdo->prepare("SELECT AVG(compliance_pct) FROM v_monthly_report WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $overall_compliance = round($stmt->fetchColumn() ?? 100, 1) . '%';

        $stmt = $pdo->prepare("SELECT AVG(avg_response_hrs) FROM v_monthly_report WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $avg_resolution = round($stmt->fetchColumn() ?? 0, 1) . 'h';

        $stmt = $pdo->prepare("SELECT SUM(total_tickets) FROM v_monthly_report WHERE client_id = ?");
        $stmt->execute([$client_id]);
        $total_tickets = $stmt->fetchColumn() ?? 0;

        // History
        $stmt = $pdo->prepare("
            SELECT *
            FROM v_monthly_report
            WHERE client_id = ?
            ORDER BY year DESC, month DESC
        ");
        $stmt->execute([$client_id]);
        $history = $stmt->fetchAll();

        // Chart Data: Avg Resolution by Month (Client)
        $stmt = $pdo->prepare("
            SELECT MONTH(resolved_at) as m, AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hrs
            FROM tickets 
            WHERE resolved_at IS NOT NULL AND YEAR(resolved_at) = YEAR(CURDATE()) AND client_id = ?
            GROUP BY MONTH(resolved_at)
        ");
        $stmt->execute([$client_id]);
        $chart_res_data = $stmt->fetchAll();

        // Chart Data: Peak Days (Client)
        $stmt = $pdo->prepare("
            SELECT DAYOFWEEK(created_at) as d, COUNT(*) as cnt
            FROM tickets
            WHERE YEAR(created_at) = YEAR(CURDATE()) AND client_id = ?
            GROUP BY DAYOFWEEK(created_at)
        ");
        $stmt->execute([$client_id]);
        $chart_days_data = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    $overall_compliance = '0%';
    $avg_resolution = '0h';
    $total_tickets = 0;
    $history = [];
    $chart_res_data = [];
    $chart_days_data = [];
}

// Process Chart Data
$chart_res = array_fill(1, 12, 0);
foreach ($chart_res_data as $row) {
    $chart_res[$row['m']] = round($row['avg_hrs'], 1);
}
$res_values = array_values($chart_res);

// DAYOFWEEK in MySQL: 1=Sun, 2=Mon... 7=Sat.
$chart_days = [2=>0, 3=>0, 4=>0, 5=>0, 6=>0, 7=>0, 1=>0]; 
foreach ($chart_days_data as $row) {
    $chart_days[$row['d']] = $row['cnt'];
}
$days_values = array_values($chart_days);


function getMonthName($n) {
    return date('F', mktime(0, 0, 0, $n, 10));
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Reports</h1>
        <p class="page-subtitle">SLA compliance and support performance analytics.</p>
    </div>
    <div style="display:flex;gap:.625rem;flex-wrap:wrap;align-items:center;">
        <select class="ts-form-control ts-form-select" id="reportPeriod" style="width:auto;">
            <option value="30">Last 30 Days</option>
            <option value="90">Last 90 Days</option>
            <option value="180">Last 6 Months</option>
            <option value="365">Last 12 Months</option>
        </select>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] !== 'client'): ?>
        <button class="btn-ts-primary" id="generateReportBtn" onclick="openGenerateModal()">
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

</div>

<!-- Monthly Reports Archive -->
<div class="ts-card mt-4">
    <div class="ts-card-header">
        <h5 class="ts-card-title">Monthly Service Report History</h5>
        <span class="ts-badge badge-silver">Auto-Generated Summaries</span>
    </div>
    <div class="ts-table-wrap">
        <table class="ts-table" id="reportHistoryTable">
            <thead>
                <tr>
                    <th>Report Period</th>
                    <th>Client</th>
                    <th>Tickets</th>
                    <th>Resolved</th>
                    <th>Avg Response</th>
                    <th>Compliance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($history as $r): ?>
                <tr>
                    <td style="font-weight:600;"><?php echo getMonthName($r['month']) . ' ' . $r['year']; ?></td>
                    <td class="text-muted-ts"><?php echo htmlspecialchars($r['company_name']); ?></td>
                    <td><?php echo $r['total_tickets']; ?></td>
                    <td><?php echo $r['resolved_tickets']; ?></td>
                    <td><?php echo $r['avg_response_hrs'] ? number_format($r['avg_response_hrs'], 1) . 'h' : '—'; ?></td>
                    <td>
                        <?php if ($r['compliance_pct'] >= 95): ?>
                        <span class="ts-badge badge-resolved">Compliant <?php echo $r['compliance_pct']; ?>%</span>
                        <?php else: ?>
                        <span class="ts-badge badge-critical">Non-Compliant <?php echo $r['compliance_pct']; ?>%</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($history)): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No report history found. Generate a report to get started.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Generate Report Mini-Modal -->
<div id="genReportOverlay" style="display:none;position:fixed;inset:0;background:rgba(10,20,40,.5);backdrop-filter:blur(4px);z-index:9998;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;padding:2rem;max-width:380px;width:calc(100% - 2rem);box-shadow:0 20px 60px rgba(10,20,40,.2);">
        <h5 style="font-size:1rem;font-weight:700;color:var(--navy-deepest);margin:0 0 .5rem;">Generate Monthly Report</h5>
        <p style="font-size:.875rem;color:var(--text-muted);margin:0 0 1.25rem;">Select a month and year. Existing data for the same period will be updated.</p>
        <div style="display:flex;gap:.75rem;margin-bottom:1.25rem;">
            <div style="flex:1;">
                <label class="ts-form-label">Month</label>
                <select id="genMonth" class="ts-form-control ts-form-select">
                    <?php for ($m=1; $m<=12; $m++): ?>
                    <option value="<?php echo $m; ?>" <?php echo ($m == date('n')) ? 'selected' : ''; ?>>
                        <?php echo date('F', mktime(0,0,0,$m,10)); ?>
                    </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div style="flex:1;">
                <label class="ts-form-label">Year</label>
                <select id="genYear" class="ts-form-control ts-form-select">
                    <?php for ($y=date('Y'); $y>=date('Y')-3; $y--): ?>
                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:.625rem;justify-content:flex-end;">
            <button class="btn-ts-secondary btn-ts-sm" onclick="closeGenerateModal()">Cancel</button>
            <button class="btn-ts-primary btn-ts-sm" id="genReportConfirmBtn" onclick="runGenerate()">
                Generate Report
            </button>
        </div>
    </div>
</div>

<!-- Report Output Modal -->
<div id="reportOutputOverlay" class="d-none" style="position:fixed;inset:0;background:rgba(10,20,40,.6);backdrop-filter:blur(6px);z-index:9999;align-items:center;justify-content:center;padding:2rem;">
    <div id="printableReport" style="background:#fff;border-radius:16px;padding:2.5rem;max-width:1000px;width:100%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 80px rgba(10,20,40,.25);display:flex;flex-direction:column;position:relative;">
        <div class="d-flex justify-content-between align-items-center mb-4 no-print" style="border-bottom: 2px solid var(--gray-100); padding-bottom: 1rem; position: relative; z-index: 10;">
            <h5 style="font-size:1.5rem;font-weight:700;color:var(--navy-deepest);margin:0;">Monthly Performance Report</h5>
            <div style="display:flex;gap:.75rem;">
                <button class="btn-ts-secondary btn-ts-sm" onclick="exportReportCSV()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg> Export CSV
                </button>
                <button class="btn-ts-primary btn-ts-sm" onclick="printReport()">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right:6px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg> Print
                </button>
                <button class="btn-ts-secondary btn-ts-sm" onclick="closeReportOutputModal()" style="border:none;background:var(--gray-100);color:var(--gray-700);">Close</button>
            </div>
        </div>
        
        <div id="reportContent" style="flex:1;">
            <div style="background: var(--navy-deepest); border-radius: 12px; padding: 1.25rem 2rem; display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; -webkit-print-color-adjust: exact; print-color-adjust: exact;" class="report-header-banner">
                <!-- Height-bounded container using overflow:hidden to crop the huge transparent PNG padding -->
                <div style="height: 60px; width: 220px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                    <img src="../public/assets/images/TechniServeLogo.png" alt="TechniServe Logo" style="width: 220px; height: auto; display: block; transform: scale(1.8); pointer-events: none;" class="print-logo" />
                </div>
                <div style="text-align: right; color: white;">
                    <h2 style="color: white; margin: 0 0 0.15rem 0; font-weight: 800; font-size: 1.6rem; letter-spacing: -0.5px;">SLA Performance Report</h2>
                    <h5 id="reportPeriodText" style="color: rgba(255,255,255,0.8); margin: 0; font-weight: 500; font-size: 0.95rem;"></h5>
                </div>
            </div>
            
            <div class="table-responsive" style="border-radius:12px;overflow:hidden;border:1px solid var(--gray-200);">
                <table class="table table-striped mb-0" style="width:100%;font-size:0.95rem;">
                    <thead style="background:var(--navy-deepest);color:white;">
                        <tr>
                            <th style="font-weight:600;padding:1rem;border:none;">Client</th>
                            <th class="text-center" style="font-weight:600;padding:1rem;border:none;">Tickets</th>
                            <th class="text-center" style="font-weight:600;padding:1rem;border:none;">Resolved</th>
                            <th class="text-center" style="font-weight:600;padding:1rem;border:none;">Closed</th>
                            <th class="text-center" style="font-weight:600;padding:1rem;border:none;">Breaches</th>
                            <th class="text-center" style="font-weight:600;padding:1rem;border:none;">Compliance</th>
                            <th class="text-center" style="font-weight:600;padding:1rem;border:none;">Avg Response</th>
                        </tr>
                    </thead>
                    <tbody id="reportTableBody" style="border-top:none;">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
            
            <div class="mt-5 d-flex justify-content-between align-items-end" style="border-top:1px solid var(--gray-200);padding-top:1.5rem;">
                <div style="font-size:0.85rem;color:var(--text-muted);">
                    <p style="margin-bottom:0.25rem;"><strong>Generated on:</strong> <span id="reportGeneratedDate"></span></p>
                    <p style="margin-bottom:0;"><strong>Generated by:</strong> TechniServe Admin Portal</p>
                </div>
                <div style="text-align:right;">
                    <p style="font-size:0.85rem;color:var(--text-muted);margin:0;">&copy; <?php echo date('Y'); ?> TechniServe IT Managed Services</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    @page {
        margin: 0;
    }
    body {
        margin: 1.6cm !important;
        background: #fff !important;
    }

    /* Hide ALL layout components */
    .ts-sidebar, .ts-topbar, .page-header, .row, .ts-card, footer, #genReportOverlay {
        display: none !important;
    }
    
    /* Make sure parents don't constrain the height or width */
    .ts-wrapper, .ts-page, .ts-content {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    /* Style the report overlay so it's a normal block element on the page, not fixed */
    #reportOutputOverlay {
        position: static !important;
        display: block !important;
        background: transparent !important;
        backdrop-filter: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        height: auto !important;
        min-height: 0 !important;
        overflow: visible !important;
    }

    /* Make sure printable element fills page exactly and has no scrollbars or constrained height */
    #printableReport {
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        max-height: none !important;
        overflow: visible !important;
        border-radius: 0 !important;
        background: white !important;
    }

    .no-print {
        display: none !important;
    }

    .table th {
        background: #0B2545 !important; /* matches sidebar (--navy-deepest) */
        color: white !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    
    .table td, .table th {
        border-color: #ddd !important;
        padding: 0.75rem !important;
    }
}
</style>

<script src="../public/js/chart.min.js"></script>
<script>
    var dynamicChartResData = <?php echo json_encode($res_values); ?>;
    var dynamicChartDaysData = <?php echo json_encode($days_values); ?>;
</script>
<script src="../public/js/charts.js"></script>
<script>
function openGenerateModal() {
    var o = document.getElementById('genReportOverlay');
    if (o) { o.style.display = 'flex'; }
}
function closeGenerateModal() {
    var o = document.getElementById('genReportOverlay');
    if (o) { o.style.display = 'none'; }
}

let currentReportData = [];
let currentReportPeriod = '';

function closeReportOutputModal() {
    var o = document.getElementById('reportOutputOverlay');
    if (o) { 
        o.classList.add('d-none'); 
        o.style.display = 'none';
        window.location.reload();
    }
}

function printReport() {
    window.print();
}

function exportReportCSV() {
    if (!currentReportData || currentReportData.length === 0) {
        alert("No data to export.");
        return;
    }
    
    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += "Client,Total Tickets,Resolved Tickets,Closed Tickets,SLA Breaches,Compliance (%),Avg Response (hrs)\n";
    
    currentReportData.forEach(function(rowArray) {
        let row = [
            `"${rowArray.company_name}"`,
            rowArray.total_tickets,
            rowArray.resolved_tickets,
            rowArray.closed_tickets !== undefined ? rowArray.closed_tickets : 0,
            rowArray.sla_breaches,
            rowArray.compliance_pct,
            rowArray.avg_response_hrs
        ].join(",");
        csvContent += row + "\r\n";
    });
    
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `SLA_Report_${currentReportPeriod.replace(' ', '_')}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function showReportOutput(data, month, year) {
    const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const monthName = months[month - 1];
    currentReportPeriod = `${monthName} ${year}`;
    currentReportData = data;
    
    document.getElementById('reportPeriodText').textContent = `Period: ${currentReportPeriod}`;
    
    // Formatting date
    const d = new Date();
    document.getElementById('reportGeneratedDate').textContent = d.toLocaleDateString() + ' ' + d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    
    const tbody = document.getElementById('reportTableBody');
    tbody.innerHTML = '';
    
    if(data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4">No data available for this period.</td></tr>';
    } else {
        data.forEach(r => {
            let tr = document.createElement('tr');
            let complianceColor = r.compliance_pct >= 95 ? 'color: var(--resolved); font-weight:700;' : 'color: var(--critical); font-weight:700;';
            tr.innerHTML = `
                <td style="font-weight:600;padding:1rem;">${r.company_name}</td>
                <td class="text-center" style="padding:1rem;">${r.total_tickets}</td>
                <td class="text-center" style="padding:1rem;">${r.resolved_tickets}</td>
                <td class="text-center" style="padding:1rem;">${r.closed_tickets !== undefined ? r.closed_tickets : 0}</td>
                <td class="text-center" style="padding:1rem;${r.sla_breaches > 0 ? 'color:var(--critical);font-weight:600;' : ''}">${r.sla_breaches}</td>
                <td class="text-center" style="padding:1rem;${complianceColor}">${r.compliance_pct}%</td>
                <td class="text-center" style="padding:1rem;">${r.avg_response_hrs ? Number(r.avg_response_hrs).toFixed(1) + 'h' : '—'}</td>
            `;
            tbody.appendChild(tr);
        });
    }
    
    var o = document.getElementById('reportOutputOverlay');
    if (o) { 
        o.classList.remove('d-none');
        o.style.display = 'flex'; 
    }
}

function runGenerate() {
    var btn   = document.getElementById('genReportConfirmBtn');
    var month = document.getElementById('genMonth').value;
    var year  = document.getElementById('genYear').value;

    var originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="ts-spinner" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Generating…';

    var fd = new FormData();
    fd.append('month', month);
    fd.append('year', year);

    fetch('../api/reports/generate.php', { method: 'POST', body: fd })
        .then(function(r) { return r.json(); })
        .then(function(json) {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            closeGenerateModal();
            if (json.success) {
                if (json.data) {
                    showReportOutput(json.data, json.month, json.year);
                } else {
                    showSuccess('Reports Generated!', json.message, null);
                    var closeBtn = document.getElementById('tsModalCloseBtn');
                    if (closeBtn) {
                        closeBtn.addEventListener('click', function () {
                            window.location.reload();
                        }, { once: true });
                    }
                }
            } else {
                showError('Generation Failed', json.message || 'Could not generate reports.');
            }
        })
        .catch(function() {
            btn.disabled = false;
            btn.innerHTML = originalHtml;
            closeGenerateModal();
            showError('Connection Error', 'Could not reach the server. Please try again.');
        });
}
</script>

<?php require '../includes/footer.php'; ?>
