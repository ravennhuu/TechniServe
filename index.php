<?php
// Pair A
// index.php — B2B landing page. HTML/CSS only. No PHP session logic.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="TechniServe — Enterprise IT Managed Services backed by a formal Service Level Agreement. Reduce downtime, track every ticket, and stay SLA-compliant.">
    <title>TechniServe — Enterprise IT Support &amp; SLA Portal</title>
    <link rel="icon" href="public/assets/images/TechniServeLogo2.png" type="image/png">
    <link rel="stylesheet" href="public/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/landing.css">
</head>
<body>

<!-- =====================================================
     SECTION 1 — STICKY NAVBAR
     ===================================================== -->
<style>
    .lp-navbar #navLogo { opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
    .lp-navbar.scrolled #navLogo { opacity: 1; visibility: visible; }
</style>
<nav class="lp-navbar" id="lpNavbar" role="navigation" aria-label="Main navigation">
    <a href="index.php" class="nav-brand" style="display:flex;align-items:center;text-decoration:none; overflow: hidden; height: 50px;">
        <img src="public/assets/images/TechniServeLogo1.png" alt="TechniServe" id="navLogo" style="width: 250px; height: auto; display:block; transform: scale(1.2);">
    </a>

    <ul class="lp-nav-links" id="navLinks">
        <li><a href="#features">Features</a></li>
        <li><a href="#sla-plans">SLA Plans</a></li>
        <li><a href="#why-us">Why Us</a></li>
        <li><a href="#request-access">Contact</a></li>
        <li><a href="login.php" class="btn-nav-login btn-nav-outline">&#9711;&nbsp; Client Portal Login</a></li>
    </ul>

    <button class="lp-hamburger" id="navHamburger" aria-label="Open navigation">
        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>
</nav>

<!-- =====================================================
     SECTION 2 — HERO (split layout)
     ===================================================== -->
<section class="lp-hero" id="hero" aria-label="Hero">
    <div class="hero-split">

        <!-- Left: text -->
        <div class="hero-left">
            <div style="margin-bottom: 1.5rem; margin-left: 50px; height: 200px; display: flex; align-items: center; justify-content: center; width: 500px; pointer-events: none;">
                <img src="public/assets/images/TechniServeLogo.png" alt="TechniServe" style="width: 100%; height: auto; display:block; transform: scale(2.0); transform-origin: center;">
            </div>

            <p class="hero-sub">
                Reduce downtime, track every ticket, and know your SLA compliance — in real time.
            </p>

            <div class="hero-actions">
                <a href="#request-access" class="btn-hero-primary" id="heroRequestBtn">
                    Request Access
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
                <a href="#features" class="btn-hero-outline" id="heroFeaturesBtn">
                    See How It Works
                </a>
            </div>
        </div>

        <!-- Right: dashboard preview card -->
        <div class="hero-right">
            <div class="dashboard-card">
                <div class="dc-header">
                    <span class="dc-title">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Dashboard
                    </span>
                    <span class="dc-live">&#9679; Live</span>
                </div>

                <div class="dc-stats">
                    <div class="dc-stat">
                        <div class="dc-stat-label">Open Tickets</div>
                        <div class="dc-stat-value">14</div>
                    </div>
                    <div class="dc-stat">
                        <div class="dc-stat-label">SLA Rate</div>
                        <div class="dc-stat-value green">97.4%</div>
                    </div>
                    <div class="dc-stat">
                        <div class="dc-stat-label">Avg Response</div>
                        <div class="dc-stat-value">2.3h</div>
                    </div>
                </div>

                <div class="dc-body">
                    <!-- Mini bar chart -->
                    <div class="dc-chart-area">
                        <div class="dc-chart-label">
                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"/></svg>
                            Monthly Tickets
                        </div>
                        <div class="dc-bars">
                            <div class="dc-bar" style="height:38%"></div>
                            <div class="dc-bar" style="height:55%"></div>
                            <div class="dc-bar" style="height:45%"></div>
                            <div class="dc-bar" style="height:70%"></div>
                            <div class="dc-bar" style="height:60%"></div>
                            <div class="dc-bar" style="height:80%"></div>
                            <div class="dc-bar" style="height:65%"></div>
                            <div class="dc-bar" style="height:90%"></div>
                        </div>
                    </div>

                    <!-- SLA Health -->
                    <div class="dc-health">
                        <div class="dc-health-label">
                            <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            SLA Health
                        </div>
                        <div class="dc-health-row">
                            <span class="dc-health-name">Response</span>
                            <div class="dc-progress"><div class="dc-progress-fill" style="width:98%"></div></div>
                            <span class="dc-health-pct">98%</span>
                        </div>
                        <div class="dc-health-row">
                            <span class="dc-health-name">Resolution</span>
                            <div class="dc-progress"><div class="dc-progress-fill" style="width:85%"></div></div>
                            <span class="dc-health-pct">85%</span>
                        </div>
                        <div class="dc-health-row">
                            <span class="dc-health-name">Uptime</span>
                            <div class="dc-progress"><div class="dc-progress-fill" style="width:99.9%"></div></div>
                            <span class="dc-health-pct">99.9%</span>
                        </div>
                    </div>
                </div>

                <div class="dc-footer">
                    <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Last updated: Just now
                </div>
            </div>
        </div>

    </div>
</section>

<!-- =====================================================
     SECTION 3 — FEATURES
     ===================================================== -->
<section class="lp-section" id="features" aria-label="Features">
    <div class="container-xl">
        <div class="text-center mb-2">
            <p class="section-label">Platform Capabilities</p>
            <h2 class="section-title">What TechniServe Delivers</h2>
            <p class="section-sub text-center">
                Seven core objectives built into one unified platform — from ticket creation to SLA reporting.
            </p>
        </div>

        <div class="features-grid">

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                </div>
                <h3 class="feature-title">Support Ticket Management</h3>
                <p class="feature-desc">Submit, track, and resolve IT support tickets with full priority-level classification — Critical, High, Medium, or Low.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
                <h3 class="feature-title">SLA Contract Management</h3>
                <p class="feature-desc">Define, store, and monitor formal SLA contracts per client — with built-in response time guarantees and compliance tracking.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/></svg>
                </div>
                <h3 class="feature-title">Preventive Maintenance Logs</h3>
                <p class="feature-desc">Schedule and record preventive maintenance visits, track equipment serviced, and ensure nothing falls through the cracks.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="feature-title">Role-Based Access Control</h3>
                <p class="feature-desc">Three distinct roles — Admin, Technician, and Client — each with scoped dashboards and permissions tailored to their needs.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <h3 class="feature-title">Client & Lead Management</h3>
                <p class="feature-desc">Manage corporate client profiles and review incoming access requests. Every new client is manually onboarded — no open sign-ups.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <h3 class="feature-title">Business Intelligence Reports</h3>
                <p class="feature-desc">View bar charts on average ticket resolution time and peak support days — giving management real data to optimize team deployment.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="feature-title">Lead Capture & Onboarding</h3>
                <p class="feature-desc">Prospective clients request access via a structured form. Admins review leads and approve or reject them before account creation.</p>
            </div>

        </div>
    </div>
</section>

<!-- =====================================================
     SECTION 4 — SLA PLANS
     ===================================================== -->
<section class="lp-section lp-section-alt" id="sla-plans" aria-label="SLA Plans">
    <div class="container-xl">
        <div class="text-center mb-2">
            <p class="section-label">Service Tiers</p>
            <h2 class="section-title">Choose Your SLA Plan</h2>
            <p class="section-sub">
                Every plan comes with a formal, signed SLA contract. Upgrade or adjust anytime with your account manager.
            </p>
        </div>

        <div class="plans-grid">

            <!-- Basic -->
            <div class="plan-card">
                <div class="plan-name">Basic</div>
                <div class="plan-price">₱8,500 <span>/ month</span></div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li>Up to 20 support hours / month</li>
                    <li>2 free on-site visits included</li>
                    <li>8-hour response time guarantee</li>
                    <li>Email &amp; portal ticket submission</li>
                    <li>Monthly SLA compliance report</li>
                </ul>
                <a href="#request-access" class="btn-plan btn-plan-outline">Get Started</a>
            </div>

            <!-- Professional (featured) -->
            <div class="plan-card featured">
                <div class="plan-badge">Most Popular</div>
                <div class="plan-name">Professional</div>
                <div class="plan-price">₱18,500 <span>/ month</span></div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li>Up to 60 support hours / month</li>
                    <li>6 free on-site visits included</li>
                    <li>4-hour response time guarantee</li>
                    <li>Priority ticket queue</li>
                    <li>Dedicated account technician</li>
                    <li>Quarterly SLA review meeting</li>
                </ul>
                <a href="#request-access" class="btn-plan btn-plan-solid">Get Started</a>
            </div>

            <!-- Enterprise -->
            <div class="plan-card">
                <div class="plan-name">Enterprise</div>
                <div class="plan-price">Contact <span>for pricing</span></div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li>Unlimited support hours</li>
                    <li>Unlimited on-site visits</li>
                    <li>1-hour critical response SLA</li>
                    <li>24/7 emergency hotline</li>
                    <li>Dedicated senior engineer</li>
                    <li>Custom SLA terms &amp; KPIs</li>
                    <li>Monthly executive reporting</li>
                </ul>
                <a href="#request-access" class="btn-plan btn-plan-outline">Talk to Our Team</a>
            </div>

        </div>

        <p class="plans-note">
            All plans include a formal SLA contract. Not sure which plan fits?
            <a href="#request-access">Talk to our team →</a>
        </p>
    </div>
</section>

<!-- =====================================================
     SECTION 5 — WHY CHOOSE US (Stats)
     ===================================================== -->
<section class="stats-section" id="why-us" aria-label="Why choose TechniServe">
    <div class="container-xl text-center" style="margin-bottom:2.5rem;">
        <p class="section-label" style="color:var(--steel-blue);">Why TechniServe</p>
        <h2 class="section-title" style="color:#fff;">Numbers That Speak for Themselves</h2>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-value">99.5%</div>
            <div class="stat-label">SLA Compliance</div>
            <div class="stat-desc">Consistently meeting response and resolution guarantees across all client contracts.</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">50+</div>
            <div class="stat-label">Corporate Clients</div>
            <div class="stat-desc">Serving mid-sized and enterprise companies across Metro Manila and key provinces.</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">4 hrs</div>
            <div class="stat-label">Response Guarantee</div>
            <div class="stat-desc">Professional plan clients receive a guaranteed 4-hour on-site or remote response.</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">100%</div>
            <div class="stat-label">Certified Technicians</div>
            <div class="stat-desc">Every engineer on our team holds industry certifications in networking, hardware, and security.</div>
        </div>
    </div>
</section>

<!-- =====================================================
     SECTION 6 — REQUEST ACCESS FORM
     ===================================================== -->
<section class="lp-section form-section" id="request-access" aria-label="Request Access">
    <div class="container-xl">
        <div class="text-center mb-4">
            <p class="section-label">Get Started</p>
            <h2 class="section-title">Ready to get started? Request access.</h2>
            <p class="section-sub">
                No open sign-ups. All client accounts are reviewed and manually onboarded by our team.
            </p>
        </div>

        <div class="form-card">
            <div id="formWrapper">
                <form id="requestAccessForm" novalidate>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="companyName">Company Name <span class="req">*</span></label>
                            <input type="text" id="companyName" name="company_name" class="form-control-lp" placeholder="e.g. Acme Corporation" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="contactName">Contact Person Full Name <span class="req">*</span></label>
                            <input type="text" id="contactName" name="contact_name" class="form-control-lp" placeholder="e.g. Juan dela Cruz" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="emailAddress">Email Address <span class="req">*</span></label>
                            <input type="email" id="emailAddress" name="email" class="form-control-lp" placeholder="you@company.com" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phoneNumber">Phone Number</label>
                            <input type="tel" id="phoneNumber" name="phone" class="form-control-lp" placeholder="+63 917 000 0000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="slaPlan">Preferred SLA Plan</label>
                        <select id="slaPlan" name="sla_plan" class="form-control-lp" style="appearance:none;background-image:url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2712%27 height=%2712%27 viewBox=%270 0 12 12%27%3E%3Cpath fill=%27%238DA9C4%27 d=%27M6 8L1 3h10z%27/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right .9rem center;padding-right:2.25rem;">
                            <option value="">— Select a plan —</option>
                            <option value="basic">Basic (₱8,500 / mo)</option>
                            <option value="professional">Professional (₱18,500 / mo)</option>
                            <option value="enterprise">Enterprise (Contact for pricing)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
                        <textarea id="message" name="message" class="form-control-lp" rows="4" placeholder="Tell us about your IT environment, team size, or any specific requirements…"></textarea>
                    </div>

                    <button type="submit" class="btn-submit-lp" id="submitBtn">Send Request</button>
                    <div id="form-error" role="alert"></div>

                </form>
            </div>

            <!-- Success message (hidden until form submitted) -->
            <div id="form-success" role="status">
                <img src="public/assets/images/check.png" width="100" height="100" alt="Request sent successfully" class="success-image">
                <h4>Request Sent!</h4>
                <p>Thank you! We'll review your request and contact you within 24 hours.</p>
            </div>
        </div>
    </div>
</section>

<!-- =====================================================
     SECTION 7 — FOOTER
     ===================================================== -->
<footer class="lp-footer" aria-label="Footer">
    <div class="lp-footer-grid">
        <div class="footer-brand">
            <img src="public/assets/logo-white.png" alt="TechniServe" onerror="this.style.display='none'">
            <div class="footer-brand-name">TechniServe</div>
            <p class="footer-tagline">
                Enterprise IT managed services backed by a formal Service Level Agreement.
                Reducing downtime for Philippine businesses since 2020.
            </p>
            <div class="footer-email">support@techniServe.ph</div>
        </div>

        <div>
            <div class="footer-col-title">Quick Links</div>
            <ul class="footer-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#sla-plans">SLA Plans</a></li>
                <li><a href="#why-us">Why Us</a></li>
                <li><a href="#request-access">Contact</a></li>
            </ul>
        </div>

        <div>
            <div class="footer-col-title">Portal</div>
            <ul class="footer-links">
                <li><a href="login.php">Client Login</a></li>
            </ul>
        </div>
    </div>

    <div class="lp-footer-bottom">
        &copy; 2026 TechniServe. All rights reserved.
    </div>
</footer>

<script src="public/js/bootstrap.bundle.min.js"></script>
<script src="public/js/landing.js"></script>
</body>
</html>