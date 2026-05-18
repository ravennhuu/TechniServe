// tickets.js — Pair A
// Client-side filter/search for the tickets table

document.addEventListener('DOMContentLoaded', function () {
    var search   = document.getElementById('ticketSearch');
    var priority = document.getElementById('filterPriority');
    var status   = document.getElementById('filterStatus');
    var rows     = document.querySelectorAll('.ts-table tbody tr');

    function filterTable() {
        var q  = (search   ? search.value.toLowerCase()   : '');
        var p  = (priority ? priority.value.toLowerCase() : '');
        var s  = (status   ? status.value.toLowerCase()   : '');

        rows.forEach(function (row) {
            var text     = row.textContent.toLowerCase();
            var badges   = row.querySelectorAll('.ts-badge');
            var pBadge   = badges[0] ? badges[0].textContent.trim().toLowerCase() : '';
            var sBadge   = badges[1] ? badges[1].textContent.trim().toLowerCase().replace(/\s+/g,'_') : '';

            var matchQ = !q || text.includes(q);
            var matchP = !p || pBadge === p;
            var matchS = !s || sBadge === s;

            row.style.display = (matchQ && matchP && matchS) ? '' : 'none';
        });
    }

    if (search)   search.addEventListener('input',  filterTable);
    if (priority) priority.addEventListener('change', filterTable);
    if (status)   status.addEventListener('change',  filterTable);
});