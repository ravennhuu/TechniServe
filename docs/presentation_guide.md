# TechniServe — Presentation Guide
### IT Managed Services & SLA Portal | B2B Web Application Presentation Guide

This guide details all 15 slides for the final presentation. Each section outlines the slide heading, visual elements to display, technical implementation highlights, and the exact talking points for the presenter.

---

## Slide 1: Title Slide
**Slide Heading:** TechniServe: IT Managed Services & SLA Portal

### What to Display
- **System Logo:** Located at `public/assets/logo.png`
- **Tagline:** *"Enterprise-grade IT managed maintenance and SLA portal for modern corporate offices."*
- **Group Details:** Section and Group Number *(e.g., Class Section / Group X)*
- **Team Members & Roles:**
  - **Avryl Raven A. Alamo** — Project Manager / Lead Analyst
  - **Francoise Maris A. San Diego** — UI/UX & Frontend Developer
  - **Irish Mae C. Estilo** — UI/UX & Frontend Developer
  - **Gian Paulo C. Pitogo** — Backend & API Developer
  - **Jiyan Braian R. Panganiban** — Backend & API Developer
  - **Tito III P. Pontañeles** — Database Administrator (DBA)
  - **Jake Andrei A. Sapida** — Database Administrator (DBA)

### Talking Points
* "Good day, everyone. Today, we are presenting **TechniServe**, a dedicated Business-to-Business (B2B) web portal designed for IT service providers to deliver and track managed maintenance services under formal Service Level Agreements (SLA)."
* "Our team built this system over a 6-week timeline, utilizing a secure, lightweight PHP backend, MySQL database, and local Bootstrap 5 styling to ensure maximum independence and performance."
* "Throughout this presentation, we will walk you through the business challenge, our architecture, role-based controls, and the core automated workflows that power the platform."

---

## Slide 2: The Business Problem
**Slide Heading:** The Core B2B Challenge

### What to Display
* **The Manual Bottleneck:** 
  > *"Corporate clients traditionally text or call IT technicians whenever an outage occurs, leaving no formal paper trail, zero SLA tracking, and no ticket prioritization."*
* **Consequences of the Problem:**
  * 🔴 **Operational Delays:** Critical network or server issues wait in the same queue as minor printer setup requests.
  * 🔴 **Untracked SLA Commitments:** IT firms cannot prove they met their guaranteed 4-hour response or 24-hour resolution times.
  * 🔴 **Billing & Hours Disputes:** Clients have no transparent way to verify how their pre-paid monthly SLA support hours are spent.
  * 🔴 **Data Silos & Loss:** Lack of a centralized audit trail results in lost logs, recurring issues being unresolved, and poor accountability.

### Talking Points
* "In the B2B IT service sector, the traditional way of handling support is highly fragmented. Clients report issues via personal messaging, phone calls, or emails."
* "This creates significant manual bottlenecks. Technicians get overwhelmed, critical downtime issues are delayed, and paper-based SLA compliance is impossible to verify."
* "Ultimately, this leads to disputes over contract hours, slow resolutions, and lost revenue for both the provider and the client due to extended IT downtime."

---

## Slide 3: The Digital Solution
**Slide Heading:** System Value Proposition

### What to Display
- **System Name:** TechniServe Portal
- **The B2B Automation Flow:**
  `Lead Capture (index.php) → Admin Vetting → SLA Account Creation → Support Ticket Life-Cycle → Automated Hours Deduction → Monthly Performance Manifests`
- **3 Immediate Business Benefits:**
  1. ✅ **Faster Ticket Processing:** Clients submit structured tickets with priority metadata; Admins view and update them in real time.
  2. ✅ **Accurate, Automated Records:** Centralized database computes SLA consumption live, removing manual bookkeeping errors.
  3. ✅ **Secure B2B Data Isolation:** Restricted multi-role routing ensures client companies can only see their own tickets and contracts.

### Talking Points
* "Our solution, TechniServe, digitalizes and automates this B2B relationship from start to finish."
* "The system operates on two layers: a public marketing page with a secure Lead Capture form, and an authenticated operations portal."
* "By automating the ticket-to-maintenance lifecycle, we guarantee three main benefits: significantly faster ticket processing, accurate hours-deduction tracking, and secure, isolated data access for corporate clients."

---

## Slide 4: Target Users & Access Control
**Slide Heading:** Role-Based Access Control (RBAC)

### What to Display
* **Admin Role (IT Service Provider):**
  * Controls all modules, client company configurations, user account creations, and SLA contracts (Full CRUD).
  * Directs ticket responses, logs maintenance hours, and generates monthly performance reports.
* **Client Role (Corporate Client Office):**
  * Submits support tickets specifying urgency (Low, High, Critical).
  * Monitors their ticket history, tracks live SLA hours usage, and downloads generated monthly reports.
* **The B2B Controlled-Entry Model:**
  * **No public sign-up/register form** is available. 
  * New clients must submit a "Request Access" inquiry from `index.php` which goes to `pages/leads.php`.
  * The Admin reviews the lead and manually creates the client login and SLA contracts.

### Talking Points
* "To protect the integrity of the platform, we implement strict Role-Based Access Control, or RBAC."
* "We intentionally support exactly two roles: the IT Provider Admin and the corporate Client. We do not have a public register link; allowing open registration would let non-contracted companies log critical tickets."
* "Admins manage the entire workspace, while clients can only interact with their own ticket forms, track their remaining SLA hours pool, and download monthly reports."

---

## Slide 5: Database Architecture
**Slide Heading:** Entity Relationship Diagram (ERD)

### What to Display
- **Schema Visual:** Embed clean view of `docs/ERD.png`
- **Third Normal Form (3NF) Normalization Highlights:**
  - **Removed Redundancy:** Removed `client_id` from `users` — link is managed via `clients.user_id` (1-to-1).
  - **Computed Fields Removed:** Removed running totals (`hours_used`, `site_visits_used`) from `sla_contracts` and aggregate metrics from `reports`.
  - **Computed VIEWS Created:** Implemented `v_sla_usage` and `v_monthly_report` to compute aggregates live.
- **Table Relationships:**
  - `clients` acts as the hub connecting `users`, `sla_contracts`, `tickets`, and `reports`.
  - `maintenance_logs` foreign-keys directly to `tickets` (deriving client ownership via the ticket structure).

### Talking Points
* "Our database schema is fully normalized to Third Normal Form, ensuring zero redundant data and absolute data integrity."
* "To achieve this, we removed derived columns like 'hours used' and 'total tickets' from our tables. Instead, we compute these aggregates dynamically using optimized MySQL views: `v_sla_usage` and `v_monthly_report`."
* "The `clients` table acts as our relational hub, binding login credentials, SLA policies, support tickets, and monthly manifests together."

---

## Slide 6: System Workflow
**Slide Heading:** The End-to-End B2B Transaction Loop

### What to Display
- **Step-by-Step Lifecycle Flowchart:**
  ```
  [1] Lead Inquiry Submitted (index.php Form)
             │
             ▼
  [2] Admin Reviews & Approves Lead (pages/leads.php)
             │
             ▼
  [3] Admin Creates Account & Assigns SLA Policy (pages/user_form.php)
             │
             ▼
  [4] Client Logs in & Submits Support Ticket (pages/ticket_create.php)
             │
             ▼
  [5] Admin Updates Ticket & Records Maintenance Work (api/maintenance/create.php)
             │
             ▼
  [6] System Deducts SLA Hours & Generates Compliance Manifest (pages/reports.php)
  ```

### Talking Points
* "Here we trace the complete end-to-end B2B transaction loop of TechniServe."
* "It starts when an interested business requests access. Once vetted and onboarded by an admin, the client can log in to submit tickets."
* "When an admin resolves the ticket and logs maintenance, the hours are immediately deducted from the client's SLA pool. At the end of the month, the admin generates a compliance report with a single click."

---

## Slide 7: Desktop UI & UX Design Philosophy
**Slide Heading:** PC-Optimized User Interface

### What to Display
- **Key UI Elements (Bootstrap 5):**
  - Use of `container-fluid` for wide, responsive desktop layouts.
  - A fixed left navigation sidebar (`includes/header.php`) for persistent, single-click navigation.
  - Interactive table filtering using vanilla JavaScript.
- **Power User Features:**
  - Fast Client-side searching, sorting, and pagination.
  - Dense grid views showing ticket statuses and priority badges (`bg-danger`, `bg-warning`, `bg-success`).
  - Bootstrap Modals for CRUD operations to keep admins in their work context.

### Talking Points
* "Unlike consumer apps that are mobile-first, TechniServe is designed as a desktop-first, power-user interface."
* "IT administrators and office managers operate on desktop workstations. They need dense, multi-column layouts to manage dozens of concurrent tasks without scrolling."
* "We utilized Bootstrap 5's grid system and local styling to design a widescreen portal with sticky navigation, colored status badges, and rapid filtering features."

---

## Slide 8: Core Feature 1 – Secure Authentication
**Slide Heading:** Secure Multi-Role Login

### What to Display
- **Visuals:** Screenshot of `login.php` showing the modern, split-pane layout.
- **Backend PHP Session Guard Details:**
  - Form POSTs to `api/auth/login.php`.
  - Credentials verified using `password_verify()` against Bcrypt hashes in the `users` table.
  - Secure parameters set: `$_SESSION['user_id']`, `$_SESSION['role']`, `$_SESSION['client_id']`.
  - Global guards: `includes/auth.php` redirects unauthenticated users; `guardRole('admin')` blocks client privilege escalation.

### Talking Points
* "Our first core feature is our secure login module, styled with a modern dual-pane layout."
* "Behind the scenes, we use PHP sessions to securely route users. When a user submits credentials, the backend queries the MySQL database using a PDO Prepared Statement to prevent SQL injection."
* "If verified, user roles and Client IDs are saved in session variables. Protected portal pages use our authentication guard to block unauthorized direct URL access."

---

## Slide 9: Core Feature 2 – Management Dashboard (Admin)
**Slide Heading:** Supplier / Provider Catalog Management

### What to Display
- **Visuals:** Screenshot of `pages/dashboard.php` (Admin View) with KPI counter cards, and `pages/sla_contracts.php`.
- **Key Modules Highlighted:**
  - **Live Counters:** Displays active tickets, critical alerts, and total onboarded clients.
  - **SLA Contract Management:** Displays active service tiers, monthly hours, and response guarantees.
  - **JavaScript Search & Filtering:** Showcases rapid live tables with text search and dropdown filters.
  - **Modals for Actions:** Shows Bootstrap modals for adding or editing items without page redirects.

### Talking Points
* "Slide 9 shows our Provider Management Dashboard, which functions as the service catalog and control panel."
* "From here, admins can track total open issues and manage active client SLAs. We integrated client-side search filters so admins can filter lists by keyword, role, or status instantly."
* "By utilizing contextual Bootstrap modals, admins can add new service contracts or update details without ever leaving the page they are working on."

---

## Slide 10: Core Feature 3 – Ordering & Request Portal (Client)
**Slide Heading:** Client Procurement Interface

### What to Display
- **Visuals:** Screenshot of `pages/ticket_create.php` and the client's dashboard overview.
- **Session-Based Isolation Logic:**
  - Form fields: Subject, Description, SLA Priority (Low, High, Critical).
  - Submits to `api/tickets/create.php`.
  - Client ID is resolved directly from `$_SESSION['client_id']`, rendering client-side spoofing impossible.
  - Live SLA widget displays hours remaining vs. monthly pool (pulled from `v_sla_usage`).

### Talking Points
* "The Client Portal serves as the client procurement interface. Here, client offices can request IT support services by filing tickets."
* "When submitting a ticket, the priority is defined based on business impact. The backend resolves the client's identity strictly from their active PHP session."
* "This prevents security vulnerabilities like ID tampering. Clients can also check their remaining monthly support allocation directly on their dashboard widget."

---

## Slide 11: Core Feature 4 – Backend Transaction Automation
**Slide Heading:** Automated Status Synchronization

### What to Display
- **Visuals:** Screenshot of `pages/ticket_view.php` showing the status change dropdown and the completed activity trail.
- **PHP/MySQL Automation Logic:**
  - **Status Updates:** Updating status to `resolved` runs `UPDATE tickets SET status = 'resolved', resolved_at = NOW() WHERE id = ?`.
  - **Audit Logs:** Simultaneously inserts a tracking record to the `ticket_activities` table.
  - **SLA Hours Calculation:** Logging maintenance in `api/maintenance/create.php` updates `maintenance_logs`.
  - Since `v_sla_usage` computes aggregates on-the-fly, the client's remaining hours pool updates automatically.

### Talking Points
* "Whenever a ticket's status is updated, our backend automates two distinct operations."
* "First, it updates the ticket status and sets a resolution timestamp. Second, it inserts an audit log into our ticket activities trail, recording who made the change, when, and any notes."
* "Furthermore, when an admin logs maintenance hours, our database view automatically updates the client's remaining contract pool. This removes the risk of data desynchronization."

---

## Slide 12: Core Feature 5 – Professional Document Generation
**Slide Heading:** Dynamic Invoice / Manifest Generation

### What to Display
- **Visuals:** Screenshot of the printable Monthly Performance Report overlay in `pages/reports.php`.
- **Dynamic Report Processing:**
  - Queries `v_monthly_report` using the selected client, month, and year.
  - Computes: Total Tickets, Resolution Rate, SLA breaches, and Compliance Percentage.
- **Print Optimization (`window.print()`):**
  - Styled with CSS `@media print` rules.
  - Hides sidebars, headers, and action buttons during printing to output a clean, paper-ready PDF manifest.

### Talking Points
* "Slide 12 showcases our document generation system. For IT firms, the monthly report serves as a service manifest."
* "The admin chooses the month, and PHP queries our `v_monthly_report` view to aggregate all ticket actions, response times, and compliance metrics into a single layout."
* "We implemented custom print CSS stylesheets. When the user clicks print, the browser hides all navigation menus and formats the document into a clean, professional PDF receipt ready for delivery."

---

## Slide 13: Core Feature 6 – Administrative Analytics
**Slide Heading:** Executive Data Visualization

### What to Display
- **Visuals:** Screenshot of the dual Chart.js visualizations on the Admin's report page.
- **Chart Implementations:**
  - `resolutionChart` (Bar Chart): Tracks average resolution time in hours over the months of the year.
  - `peakDaysChart` (Bar Chart): Aggregates ticket submissions by day of the week (Monday–Sunday) to track busy periods.
- **Database Aggregation:** Data is grouped using SQL `GROUP BY MONTH()` and `GROUP BY DAYOFWEEK()`, then passed directly into Chart.js configs.

### Talking Points
* "To give managers executive-level insight, we built interactive data visualizations using Chart.js."
* "We showcase two critical charts: one tracking monthly average resolution times, and another displaying support requests by weekday."
* "This is computed using SQL aggregation queries. Managers can see exactly when support requests peak, allowing them to schedule technician shifts proactively and reduce SLA response breaches."

---

## Slide 14: System Security & Data Integrity
**Slide Heading:** Security Measures Implemented

### What to Display
* **Our Four Security Pillars:**
  1. 🛡️ **PDO Prepared Statements:** Prevents SQL Injection by binding variables separately from the query structure.
  2. 🛡️ **PHP Session Validation:** Restricts directory traversal. Unauthenticated requests are rejected by `includes/auth.php`.
  3. 🛡️ **Role-Based Guards:** Prevents privilege escalation by checking `$_SESSION['role'] === 'admin'` before loading sensitive pages.
  4. 🛡️ **Bcrypt Password Hashing:** Secures credentials via PHP's `password_hash()` and `password_verify()`. No plain-text passwords exist.
  5. 🛡️ **Directory Protection (`.htaccess`):** Denies direct URL access to the `api/`, `includes/`, and `sql/` folders.

### Talking Points
* "Security is a core focus in enterprise software. We implemented four layers of protection to secure our data."
* "First, we prevent SQL injection by using PDO Prepared Statements. Second, session checks block unauthenticated URL access. Third, role-based guards stop clients from accessing admin pages."
* "Finally, passwords are encrypted using Bcrypt, and our `.htaccess` configuration blocks direct browser access to backend source code directories."

---

## Slide 15: Conclusion & Future Enhancements
**Slide Heading:** Project Summary & Next Steps

### What to Display
* **System Achievements (6-Week Timeline):**
  * Completed fully secure role-based authentication.
  * Designed a normalized 3NF database layout with computed views.
  * Deployed a responsive, PC-optimized dashboard with search, filter, and modal interfaces.
  * Integrated Chart.js analytics and print-ready report document generation.
* **Future Roadmap (Scale Up):**
  * 📱 **SMS/Email Notifications:** Auto-notify clients of ticket status changes (via Twilio or PHPMailer).
  * 💬 **Real-time Live Chat:** Real-time communications between client and admin via WebSockets.
  * 📡 **API Service Layer:** Expose RESTful endpoints for mobile technician applications.
  * ☁️ **Cloud Deployment:** Migration to cloud host with cron jobs for automated monthly reports.

### Talking Points
* "In conclusion, over our 6-week development cycle, our team delivered a fully operational B2B IT support portal."
* "We successfully built the normalized database, multi-role authentication, automated ticket flows, SLA monitoring, and data analytics."
* "Looking forward, our roadmap includes integrating live SMS alerts, building in-app chat via WebSockets, and exposing REST APIs to support mobile apps for field technicians."
* "Thank you, and we are now open to any questions you may have."
