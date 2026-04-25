<?php
// reports.php — Pair A
// Business intelligence charts. Uses Chart.js via charts.js.
require '../includes/auth.php';
require '../includes/header.php';
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
                <div class="kpi-value">98.2%</div>
                <div class="kpi-label">SLA Compliance Rate</div>
                <div class="kpi-trend up">↑ 1.4% vs last period</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon amber">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value">4.6h</div>
                <div class="kpi-label">Avg. Resolution Time</div>
                <div class="kpi-trend down">↓ 0.5h improvement</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="kpi-card">
            <div class="kpi-icon blue">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
            </div>
            <div class="kpi-data">
                <div class="kpi-value">111</div>
                <div class="kpi-label">Total Tickets Filed</div>
                <div class="kpi-trend" style="color:var(--steel-blue);">This period</div>
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

<script src="../public/js/chart.min.js"></script>
<script src="../public/js/charts.js"></script>
<?php require '../includes/footer.php'; ?>
