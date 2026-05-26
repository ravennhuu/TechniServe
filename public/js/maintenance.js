// maintenance.js — Pair A
// Client-side search/filter for the maintenance table

document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('maintenanceSearch');
    var status = document.getElementById('filterMaintenanceStatus');
    var clientFilter = document.getElementById('filterMaintenanceClient');
    var rows   = document.querySelectorAll('#maintenanceTable tbody tr');

    function filterTable() {
        var q = search ? search.value.toLowerCase() : '';
        var s = status ? status.value.toLowerCase()  : '';
        var c = clientFilter ? clientFilter.value : '';

        rows.forEach(function (row) {
            var text   = row.textContent.toLowerCase();
            var badge  = row.querySelector('.ts-badge');
            var sBadge = badge ? badge.textContent.trim().toLowerCase() : '';
            var rowClient = row.getAttribute('data-client-id') || '';

            var matchQ = !q || text.includes(q);
            var matchS = !s || sBadge === s;
            var matchC = !c || rowClient === c;

            row.style.display = (matchQ && matchS && matchC) ? '' : 'none';
        });
    }

    if (search) search.addEventListener('input',  filterTable);
    if (status) status.addEventListener('change', filterTable);
    if (clientFilter) clientFilter.addEventListener('change', filterTable);

    var deleteButtons = document.querySelectorAll('.maintenance-delete-btn');
    deleteButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var logId = this.getAttribute('data-id');
            if (!logId) return;

            showConfirm('Delete Maintenance Log?', 'This action cannot be undone. Are you sure you want to delete this maintenance log?', function () {
                fetch('../api/maintenance/delete.php', {
                    method: 'POST',
                    body: new URLSearchParams({ id: logId })
                })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    if (json.success) {
                        showSuccess('Deleted!', json.message || 'Maintenance log deleted.', window.location.href, 1200);
                    } else {
                        showError('Delete Failed', json.message || 'Could not delete the maintenance log.');
                    }
                })
                .catch(function () {
                    showError('Connection Error', 'Could not reach the server. Please try again.');
                });
            });
        });
    });
});