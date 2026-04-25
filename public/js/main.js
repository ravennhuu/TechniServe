// main.js — Pair A
// Portal-wide JS: sidebar toggle, active nav link highlight

document.addEventListener('DOMContentLoaded', function () {

    /* ── Sidebar mobile toggle ── */
    var sidebar  = document.getElementById('tsSidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var toggler  = document.getElementById('sidebarToggle');

    function openSidebar()  { if (sidebar) sidebar.classList.add('open');    if (overlay) overlay.classList.add('active'); }
    function closeSidebar() { if (sidebar) sidebar.classList.remove('open'); if (overlay) overlay.classList.remove('active'); }

    if (toggler)  toggler.addEventListener('click', openSidebar);
    if (overlay)  overlay.addEventListener('click', closeSidebar);

    /* ── Active nav link (match current URL) ── */
    var currentPath = window.location.pathname.split('/').pop();
    var navLinks    = document.querySelectorAll('.sidebar-nav .nav-link');

    navLinks.forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && href.split('/').pop() === currentPath) {
            link.classList.add('active');
        }
    });

});