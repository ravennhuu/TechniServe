// charts.js — Pair A
// Chart.js initialisation for reports.php

document.addEventListener('DOMContentLoaded', function () {

    /* ── Chart 1: Average Ticket Resolution Time ── */
    var ctx1 = document.getElementById('resolutionChart');
    if (ctx1) {
        new Chart(ctx1.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Avg Resolution (hrs)',
                    data: [5.2, 3.8, 6.1, 4.5, 3.2, 4.8],
                    backgroundColor: 'rgba(19,64,116,.75)',
                    borderColor: '#134074',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) { return ctx.parsed.y + ' hrs'; }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#8DA9C4', font: { size: 12 } },
                        grid: { color: '#E2E8F0' }
                    },
                    x: {
                        ticks: { color: '#8DA9C4', font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    /* ── Chart 2: Peak Support Request Days ── */
    var ctx2 = document.getElementById('peakDaysChart');
    if (ctx2) {
        new Chart(ctx2.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Tickets Filed',
                    data: [18, 22, 15, 20, 25, 8, 3],
                    backgroundColor: 'rgba(5,150,105,.72)',
                    borderColor: '#059669',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#8DA9C4', font: { size: 12 } },
                        grid: { color: '#E2E8F0' }
                    },
                    x: {
                        ticks: { color: '#8DA9C4', font: { size: 12 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

});