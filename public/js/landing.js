// landing.js — Pair A
// Handles: navbar scroll, smooth scroll, AJAX form submit

document.addEventListener('DOMContentLoaded', function () {

    /* ── Navbar: transparent → solid on scroll ── */
    const navbar = document.getElementById('lpNavbar');
    if (navbar) {
        function updateNavbar() {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
        window.addEventListener('scroll', updateNavbar, { passive: true });
        updateNavbar();
    }

    /* ── Mobile hamburger ── */
    const hamburger = document.getElementById('navHamburger');
    const navLinks  = document.getElementById('navLinks');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function () {
            navLinks.classList.toggle('open');
        });

        // Close on link click
        navLinks.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navLinks.classList.remove('open');
            });
        });
    }

    /* ── Smooth scroll for anchor links ── */
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            var target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    /* ── Request Access form: AJAX submit ── */
    const form       = document.getElementById('requestAccessForm');
    const formWrap   = document.getElementById('formWrapper');
    const successMsg = document.getElementById('form-success');
    const errorMsg   = document.getElementById('form-error');
    const submitBtn  = document.getElementById('submitBtn');

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Clear previous error
            if (errorMsg) { errorMsg.style.display = 'none'; }

            // Disable button
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Sending…';
            }

            var payload = new FormData(form);

            fetch('api/leads/submit.php', {
                method: 'POST',
                body: payload
            })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.success) {
                    // Hide form, show success
                    if (formWrap)   { formWrap.style.display = 'none'; }
                    if (successMsg) { successMsg.style.display = 'block'; }
                } else {
                    throw new Error(data.message || 'Submission failed.');
                }
            })
            .catch(function (err) {
                if (errorMsg) {
                    errorMsg.textContent = err.message || 'Something went wrong. Please try again.';
                    errorMsg.style.display = 'block';
                }
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Send Request';
                }
            });
        });
    }

});