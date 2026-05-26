<?php
// Pair A
// login.php — Login form HTML only. No PHP session logic. Form POSTs to api/auth/login.php.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TechniServe Client Portal Login — secure access for authorised clients, technicians, and administrators.">
    <title>Login — TechniServe Portal</title>
    <link rel="icon" href="public/assets/images/TechniServeLogo2.png" type="image/png">
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body style="margin:0;padding:0;background:#fff;">

<div class="container-fluid min-vh-100 p-0">
    <div class="row g-0 min-vh-100">
        
        <!-- Left Side: Branding and Features -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5 text-white" style="background-color: var(--navy-deepest);">
            <div style="max-width: 440px; width: 100%;">
                
                <div class="mb-4" style="margin-left: -20px; overflow: hidden; height: 120px; display: flex; align-items: center; justify-content: flex-start; width: 380px;">
                    <img src="public/assets/images/TechniServeLogo.png" alt="TechniServe" style="width: 100%; height: auto; display:block; transform: scale(1.8); transform-origin: center;">
                </div>
                
                <ul class="list-unstyled mb-0">
                    <li class="d-flex gap-3 mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0" style="width: 40px; height: 40px; background: rgba(141,169,196,0.1); color: var(--steel-blue);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <strong class="d-block text-white mb-1" style="font-size: 0.9375rem;">Track Support Tickets</strong>
                            <span style="font-size: 0.8125rem; color: var(--steel-blue); line-height: 1.5;">Submit and monitor all your IT support requests</span>
                        </div>
                    </li>
                    <li class="d-flex gap-3 mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0" style="width: 40px; height: 40px; background: rgba(141,169,196,0.1); color: var(--steel-blue);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <strong class="d-block text-white mb-1" style="font-size: 0.9375rem;">SLA Compliance</strong>
                            <span style="font-size: 0.8125rem; color: var(--steel-blue); line-height: 1.5;">Real-time visibility into service level agreements</span>
                        </div>
                    </li>
                    <li class="d-flex gap-3 mb-4">
                        <div class="d-flex align-items-center justify-content-center rounded flex-shrink-0" style="width: 40px; height: 40px; background: rgba(141,169,196,0.1); color: var(--steel-blue);">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <strong class="d-block text-white mb-1" style="font-size: 0.9375rem;">Hours & Visits Tracking</strong>
                            <span style="font-size: 0.8125rem; color: var(--steel-blue); line-height: 1.5;">Monitor your monthly service allocation</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4 p-md-5" style="background-color: var(--sage-white);">
            <div style="max-width: 400px; width: 100%;">
                <h2 class="fs-4 fw-bold mb-2" style="color: var(--navy-deepest);">Welcome Back</h2>
                <p class="mb-5" style="font-size: 0.875rem; color: var(--steel-blue);">Sign in to access the TechniServe portal</p>

                <div class="login-error" id="loginError" role="alert"></div>

                <form action="api/auth/login.php" method="POST" id="loginForm">

                    <div class="mb-3 ts-form-group">
                        <label class="ts-form-label" for="loginEmail">Email Address</label>
                        <input
                            type="email"
                            id="loginEmail"
                            name="email"
                            class="ts-form-control"
                            placeholder="you@company.com"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="mb-3 ts-form-group">
                        <label class="ts-form-label" for="loginPassword">Password</label>
                        <div class="position-relative">
                            <input type="password" id="loginPassword" name="password" class="ts-form-control pe-5" placeholder="Enter your password" required autocomplete="current-password">
                            <button type="button" class="pwd-toggle" id="togglePwd" aria-label="Toggle password visibility">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4 ts-form-group">
                        <label class="ts-form-label" for="loginRole">Sign in as</label>
                        <select id="loginRole" name="role" class="ts-form-control ts-form-select">
                            <option value="client">Client</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-ts-primary w-100 d-flex justify-content-center py-2 mt-2 rounded-3" id="loginBtn">
                        Sign In
                    </button>

                </form>

                <div class="mt-4" style="font-size: 0.8125rem;">
                    &larr; <a href="index.php" class="text-decoration-none" style="color: var(--steel-blue);">Back to website</a>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="public/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    /* Password visibility toggle */
    var toggleBtn = document.getElementById('togglePwd');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var input = document.getElementById('loginPassword');
            var isText = input.getAttribute('type') === 'text';
            input.setAttribute('type', isText ? 'password' : 'text');
            this.innerHTML = isText
                ? '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>'
                : '<svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>';
        });
    }

    /* Login form AJAX */
    var form    = document.getElementById('loginForm');
    var errBox  = document.getElementById('loginError');
    var loginBtn = document.getElementById('loginBtn');

    function showErr(msg) {
        if (!errBox) return;
        errBox.textContent = msg;
        errBox.style.display = 'block';
        errBox.style.padding = '.75rem 1rem';
        errBox.style.background = '#fee2e2';
        errBox.style.color = '#991b1b';
        errBox.style.border = '1px solid #fca5a5';
        errBox.style.borderRadius = '8px';
        errBox.style.fontSize = '.875rem';
        errBox.style.marginBottom = '1rem';
    }

    function hideErr() {
        if (!errBox) return;
        errBox.textContent = '';
        errBox.style.display = 'none';
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            hideErr();

            var originalHtml = loginBtn ? loginBtn.innerHTML : 'Sign In';
            if (loginBtn) {
                loginBtn.disabled = true;
                loginBtn.innerHTML = '<svg style="animation:ts-spin .7s linear infinite;display:inline-block;vertical-align:middle;margin-right:.3rem;" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg> Signing in…';
            }

            fetch(form.action, { method: 'POST', body: new FormData(form) })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (loginBtn) { loginBtn.disabled = false; loginBtn.innerHTML = originalHtml; }
                    if (json.success) {
                        window.location.href = json.redirect || 'pages/dashboard.php';
                    } else {
                        showErr(json.message || 'Invalid credentials. Please try again.');
                    }
                })
                .catch(function () {
                    if (loginBtn) { loginBtn.disabled = false; loginBtn.innerHTML = originalHtml; }
                    showErr('Could not connect to the server. Please check your connection.');
                });
        });
    }
})();
</script>
<style>
@keyframes ts-spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
</style>
</body>
</html>
