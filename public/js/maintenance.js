// maintenance.js — Pair A
// Client-side search/filter for the maintenance table

document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('maintenanceSearch');
    var status = document.getElementById('filterMaintenanceStatus');
    var rows   = document.querySelectorAll('#maintenanceTable tbody tr');

    function filterTable() {
        var q = search ? search.value.toLowerCase() : '';
        var s = status ? status.value.toLowerCase()  : '';

        rows.forEach(function (row) {
            var text   = row.textContent.toLowerCase();
            var badge  = row.querySelector('.ts-badge');
            var sBadge = badge ? badge.textContent.trim().toLowerCase() : '';

            var matchQ = !q || text.includes(q);
            var matchS = !s || sBadge === s;

            row.style.display = (matchQ && matchS) ? '' : 'none';
        });
    }

    if (search) search.addEventListener('input',  filterTable);
    if (status) status.addEventListener('change', filterTable);
});