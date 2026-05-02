<?php
// footer.php — Pair A
// Shared closing tags and scripts. Included at the bottom of every portal page.
?>
        </main>
        
        <footer class="ts-footer">
            <div class="container-fluid px-0">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between text-muted" style="font-size: 0.8125rem; padding: 1.5rem 2rem; border-top: 1px solid var(--border-color);">
                    <div>&copy; <?php echo date('Y'); ?> TechniServe Portal. All rights reserved.</div>
                    <div class="d-flex gap-3 mt-2 mt-md-0">
                        <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                        <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                        <a href="#" class="text-muted text-decoration-none">Support</a>
                    </div>
                </div>
            </div>
        </footer>

    </div><!-- /.ts-page -->
</div><!-- /.ts-wrapper -->

<!-- ── Global Feedback Modal ─────────────────────────────────────────── -->
<div id="tsModalOverlay" class="ts-modal-overlay">
    <div id="tsModal" class="ts-modal" role="dialog" aria-modal="true">
        <div id="tsModalHeader" class="ts-modal-header">
            <div id="tsModalIcon" class="ts-modal-icon"></div>
        </div>
        <div class="ts-modal-body">
            <h4 id="tsModalTitle" class="ts-modal-title"></h4>
            <p  id="tsModalMessage" class="ts-modal-message"></p>
        </div>
        <div id="tsModalFooter" class="ts-modal-footer"></div>
    </div>
</div>

<script src="../public/js/bootstrap.bundle.min.js"></script>
<script src="../public/js/main.js"></script>
</body>
</html>
