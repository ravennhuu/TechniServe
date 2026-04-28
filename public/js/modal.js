// modal.js — TechniServe
// Provides showSuccess(), showError(), showConfirm(), and submitFormAjax()
// Automatically initialises on DOMContentLoaded.

(function () {
    'use strict';

    /* ── Internal helpers ──────────────────────────────────────────── */

    function getModal()    { return document.getElementById('tsModal'); }
    function getOverlay()  { return document.getElementById('tsModalOverlay'); }

    function _open() {
        var m = getModal(), o = getOverlay();
        if (!m || !o) return;
        o.classList.add('active');
        m.classList.add('active');
    }

    function _close() {
        var m = getModal(), o = getOverlay();
        if (!m || !o) return;
        o.classList.remove('active');
        m.classList.remove('active');
        // Clear pending redirect
        if (window._tsModalRedirect) {
            clearTimeout(window._tsModalRedirect);
            window._tsModalRedirect = null;
        }
    }

    function _set(type, title, message) {
        var icon  = document.getElementById('tsModalIcon');
        var ttl   = document.getElementById('tsModalTitle');
        var msg   = document.getElementById('tsModalMessage');
        var hdr   = document.getElementById('tsModalHeader');

        if (!icon || !ttl || !msg || !hdr) return;

        // Reset classes
        hdr.className = 'ts-modal-header';
        icon.innerHTML = '';

        if (type === 'success') {
            hdr.classList.add('ts-modal-success');
            icon.innerHTML = '<svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        } else if (type === 'error') {
            hdr.classList.add('ts-modal-error');
            icon.innerHTML = '<svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        } else if (type === 'warning') {
            hdr.classList.add('ts-modal-warning');
            icon.innerHTML = '<svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
        } else if (type === 'confirm') {
            hdr.classList.add('ts-modal-warning');
            icon.innerHTML = '<svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
        }

        ttl.textContent = title;
        msg.textContent = message;
    }

    /* ── Public API ────────────────────────────────────────────────── */

    /**
     * Show a success modal.
     * @param {string} title
     * @param {string} message
     * @param {string|null} redirectUrl  If set, auto-redirect after 2 s.
     * @param {number} delay             Redirect delay in ms (default 1800).
     */
    window.showSuccess = function (title, message, redirectUrl, delay) {
        _set('success', title, message);
        var footer = document.getElementById('tsModalFooter');
        if (footer) {
            if (redirectUrl) {
                footer.innerHTML = '<span style="font-size:.8125rem;color:var(--text-muted);">Redirecting…</span>';
            } else {
                footer.innerHTML = '<button class="btn-ts-primary btn-ts-sm" id="tsModalCloseBtn">Done</button>';
                var btn = document.getElementById('tsModalCloseBtn');
                if (btn) btn.addEventListener('click', _close);
            }
        }
        _open();
        if (redirectUrl) {
            window._tsModalRedirect = setTimeout(function () {
                window.location.href = redirectUrl;
            }, delay || 1800);
        }
    };

    /**
     * Show an error modal.
     * @param {string} title
     * @param {string} message
     */
    window.showError = function (title, message) {
        _set('error', title, message);
        var footer = document.getElementById('tsModalFooter');
        if (footer) {
            footer.innerHTML = '<button class="btn-ts-primary btn-ts-sm" id="tsModalCloseBtn">OK, Got it</button>';
            var btn = document.getElementById('tsModalCloseBtn');
            if (btn) btn.addEventListener('click', _close);
        }
        _open();
    };

    /**
     * Show a warning/info modal.
     */
    window.showWarning = function (title, message) {
        _set('warning', title, message);
        var footer = document.getElementById('tsModalFooter');
        if (footer) {
            footer.innerHTML = '<button class="btn-ts-primary btn-ts-sm" id="tsModalCloseBtn">Understood</button>';
            var btn = document.getElementById('tsModalCloseBtn');
            if (btn) btn.addEventListener('click', _close);
        }
        _open();
    };

    /**
     * Show a confirmation modal with Yes/No buttons.
     * @param {string}   title
     * @param {string}   message
     * @param {Function} onConfirm  Callback when user clicks Yes.
     */
    window.showConfirm = function (title, message, onConfirm) {
        _set('confirm', title, message);
        var footer = document.getElementById('tsModalFooter');
        if (footer) {
            footer.innerHTML =
                '<button class="btn-ts-secondary btn-ts-sm" id="tsModalCancelBtn" style="margin-right:.5rem;">Cancel</button>' +
                '<button class="btn-ts-primary btn-ts-sm" id="tsModalConfirmBtn">Yes, Proceed</button>';
            document.getElementById('tsModalCancelBtn').addEventListener('click', _close);
            document.getElementById('tsModalConfirmBtn').addEventListener('click', function () {
                _close();
                if (typeof onConfirm === 'function') onConfirm();
            });
        }
        _open();
    };

    /**
     * Wire a <form> element to submit via fetch() and show modals.
     *
     * @param {string|HTMLFormElement} formOrSelector
     * @param {Object} options
     *   - successTitle   {string}   Modal title on success
     *   - successMessage {string}   Modal body on success (overridden by API message if present)
     *   - redirectUrl    {string}   Auto-redirect URL after success (null = stay)
     *   - redirectDelay  {number}   ms before redirect (default 1800)
     *   - errorTitle     {string}   Modal title on error
     *   - onSuccess      {Function} Extra callback on success (data)
     *   - onError        {Function} Extra callback on error (data)
     *   - validate       {Function} Client-side validator, return false to abort
     */
    window.submitFormAjax = function (formOrSelector, options) {
        var form = typeof formOrSelector === 'string'
            ? document.querySelector(formOrSelector)
            : formOrSelector;

        if (!form) return;

        var opts = Object.assign({
            successTitle:   'Success',
            successMessage: 'Operation completed successfully.',
            redirectUrl:    null,
            redirectDelay:  1800,
            errorTitle:     'Action Failed',
            onSuccess:      null,
            onError:        null,
            validate:       null
        }, options || {});

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            // Client-side validation hook
            if (typeof opts.validate === 'function' && opts.validate(form) === false) {
                return;
            }

            var submitBtn = form.querySelector('[type="submit"]');
            var originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML =
                    '<svg class="ts-spinner" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">' +
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>' +
                    '</svg> Processing…';
            }

            var data = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: data
            })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
                if (json.success) {
                    var msg = json.message || opts.successMessage;
                    if (typeof opts.onSuccess === 'function') opts.onSuccess(json);
                    window.showSuccess(opts.successTitle, msg, opts.redirectUrl, opts.redirectDelay);
                } else {
                    var errMsg = json.message || 'An unexpected error occurred. Please try again.';
                    if (typeof opts.onError === 'function') opts.onError(json);
                    window.showError(opts.errorTitle, errMsg);
                }
            })
            .catch(function (err) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
                window.showError('Connection Error', 'Could not reach the server. Please check your connection and try again.');
            });
        });
    };

    /* ── Close on overlay click ─────────────────────────────────────── */
    document.addEventListener('DOMContentLoaded', function () {
        var overlay = getOverlay();
        if (overlay) {
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) _close();
            });
        }
        // Keyboard ESC
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') _close();
        });
    });

}());
