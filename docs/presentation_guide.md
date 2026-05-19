# TechniServe — Presentation Guide
### IT Managed Services & SLA Portal | B2B Web Application

> **Instructions:** This guide covers all 15 slides of your final presentation. Each section contains the exact talking points, key phrases, and technical details to highlight per slide. Do not change anything in the source code — this guide is for presentation use only.

---

## Slide 1 — Title Slide
**Heading:** TechniServe: IT Managed Services & SLA Portal

### What to Display
- **System Logo:** `public/assets/logo.png`
- **Tagline:** *"A B2B platform for IT firms to deliver managed maintenance services to corporate client offices under a formal Service Level Agreement."*
- **Group Number & Class Section:** *(fill in your section)*
- **Team Members & Roles:**

| Name | Role |
|---|---|
| Alamo, Avryl Raven A. | Project Manager / Lead Analyst |
| San Diego, Francoise Maris A. | UI/UX & Frontend Developer |
| Estilo, Irish Mae C. | UI/UX & Frontend Developer |
| Pitogo, Gian Paulo C. | Backend & API Developer |
| Panganiban, Jiyan Braian R. | Backend & API Developer |
| Pontañeles, Tito III P. | Database Administrator (DBA) |
| Sapida, Jake Andrei A. | Database Administrator (DBA) |

### Talking Points
- Briefly introduce the project name and what type of system it is.
- Mention that this is a **B2B (Business-to-Business)** web portal — meaning both users of the system are businesses, not individual consumers.
- State the 6-week development timeline and the tech stack: PHP, MySQL, Bootstrap 5, Chart.js.

---

## Slide 2 — The Business Problem
**Heading:** The Core B2B Challenge

### What to Display
- The traditional, manual pain point in IT managed services:
  > *"Corporate offices manually contact their IT provider via phone or email whenever a system goes down. There is no formal tracking, no priority system, no SLA visibility, and no paper trail."*
- **Consequences of the problem:**
  - 🔴 **Lost records** — no centralized log of what was fixed and when
  - 🔴 **No accountability** — clients don't know if their SLA hours are being used efficiently
  - 🔴 **Slow response** — no priority system means critical issues wait as long as minor ones
  - 🔴 **No compliance data** — neither party can prove whether the SLA was met or breached

### Talking Points
- The IT services industry traditionally runs on phone calls, text messages, and spreadsheets.
- When a client's network goes down, they text their IT vendor — and then wait, with no visibility on response time or resolution progress.
- SLA contracts exist on paper, but there is no digital system to enforce or track them in real time.
- This results in disputes, client dissatisfaction, and no hard data to prove service delivery.

---

## Slide 3 — The Digital Solution
**Heading:** System Value Proposition

### What to Display
- **System Name:** TechniServe
- **One-line pitch:** *"A full B2B web portal that digitalizes IT support delivery — from the initial client inquiry all the way to monthly SLA compliance reports."*
- **3 Immediate Benefits:**
  1. ✅ **Faster Processing** — Clients submit structured tickets instantly; Admin sees them in a live queue with priority badges
  2. ✅ **Accurate Records** — Every status change, maintenance log, and SLA usage figure is stored in MySQL with a full audit trail
  3. ✅ **Secure Access** — No public sign-up; controlled onboarding ensures only vetted, contracted clients access the portal

### Talking Points
- TechniServe replaces the phone/text/spreadsheet workflow with a structured, role-based web portal.
- The system has **two layers**: a public landing page (`index.php`) for lead capture, and a secure authenticated portal (`/pages/`) for daily operations.
- The portal automates the B2B transaction loop: from ticket submission → admin response → maintenance logging → SLA tracking → monthly report generation.

---

## Slide 4 — Target Users & Access Control
**Heading:** Role-Based Access Control (RBAC)

### What to Display

#### Provider / Admin User (the IT Firm — TechniServe)
- **Controls:** Full access to every module
- **Can do:** Onboard clients, manage SLA contracts, resolve tickets, log maintenance, generate reports, view analytics
- **Cannot do:** Submit tickets (that is the client's job)

#### Client User (the Corporate Office)
- **Controls:** Their own data only
- **Can do:** Submit support tickets (Low / High / Critical), view their own ticket history, view their SLA usage status, download their monthly reports
- **Cannot do:** See other clients' tickets, access admin analytics, manage users

### Talking Points
- There are **only two roles** in the system: `admin` and `client`. There is no technician role.
- **No public registration exists** — there is no `/register.php` or `/signup.php`. Client accounts are created manually by the Admin after a lead is reviewed and approved.
- This is a deliberate B2B design choice: a client must have a signed SLA contract before they can submit any tickets. Open registration would allow uncontracted companies to flood the system with requests.
- The 3-step onboarding flow: Lead submits inquiry → Admin approves → Admin creates login + assigns SLA contract → Client receives credentials.
- Access control is enforced server-side via `includes/auth.php` (session check) and `guardRole()` in `includes/functions.php`.

---

## Slide 5 — Database Architecture
**Heading:** Entity Relationship Diagram (ERD)

### What to Display
- **Embed:** `docs/ERD.png` (screenshot of your MySQL schema from phpMyAdmin)
- **8 Tables Summary:**

| Table | Purpose |
|---|---|
| `users` | All login accounts (admin & client). Created by Admin only. |
| `clients` | Corporate client company profiles. Linked 1-to-1 to a user. |
| `sla_contracts` | SLA terms per client: monthly hours pool, site visits, response time limits. |
| `tickets` | Support tickets with priority (low/high/critical) and status workflow. |
| `ticket_activities` | Full audit trail — every status change on every ticket. |
| `maintenance_logs` | Maintenance activities performed, linked to tickets. Hours auto-aggregated. |
| `reports` | Records of monthly report generation events per client. |
| `leads` | Public "Request Access" form submissions from the landing page. |

### Talking Points
- The schema is designed to **Third Normal Form (3NF)** — no redundant or derived data is stored.
- Key design decisions made during 3NF normalization:
  - `users.client_id` was removed — the relationship is stored once, in `clients.user_id`
  - `sla_contracts.hours_used` was removed — computed live via the `v_sla_usage` VIEW by aggregating `maintenance_logs`
  - `maintenance_logs.client_id` was removed — the client is derived through `maintenance_logs → tickets → clients`
  - `reports` aggregate columns were removed — live figures are computed via the `v_monthly_report` VIEW
- Two SQL VIEWS replace the removed columns: `v_sla_usage` and `v_monthly_report`.
- Primary relationships to highlight: **`clients` is the hub** — `sla_contracts`, `tickets`, and `reports` all foreign-key into `clients.id`.

---

## Slide 6 — System Workflow
**Heading:** The End-to-End B2B Transaction Loop

### What to Display
A step-by-step flow of the full transaction lifecycle:

```
[LANDING PAGE]
 1. Visitor fills "Request Access" form on index.php
    → api/leads/submit.php saves to leads table
    → Admin reviews in pages/leads.php

[ONBOARDING]
 2. Admin approves lead → creates user account (pages/user_form.php)
 3. Admin assigns SLA contract (pages/sla_contract_form.php)
    → Client receives credentials

[DAILY OPERATIONS]
 4. Client logs in → submits a support ticket (pages/ticket_create.php)
    → ticket saved with status = 'open', priority = low/high/critical
 5. Admin views pending queue (pages/tickets.php)
    → Admin updates ticket status: open → in_progress → resolved
    → Each change logged to ticket_activities (audit trail)

[MAINTENANCE & SLA]
 6. Admin logs maintenance activity (pages/maintenance_create.php)
    → hours_spent recorded in maintenance_logs
    → v_sla_usage VIEW auto-aggregates hours used vs. pool

[REPORTING]
 7. Admin generates Monthly Service Report (pages/reports.php)
    → v_monthly_report VIEW computes all figures live
    → Report record saved to reports table
    → Client can view/print their report
```

### Talking Points
- This loop replaces the entire phone/email/spreadsheet workflow with one integrated system.
- The flow is **fully traceable** — from the first inquiry on the landing page to the final monthly report, every action is stored.
- Highlight the separation of concerns: the client only acts in steps 4 and 7 (submit + view). Admin handles everything in between.

---

## Slide 7 — Desktop UI & UX Design Philosophy
**Heading:** PC-Optimized User Interface

### What to Display
- Screenshot of the Admin dashboard showing the multi-column layout
- Screenshot of the sidebar navigation in `includes/header.php`

### Talking Points
- **Why desktop-first?** TechniServe is a **power-user tool** for IT administrators and corporate office managers who work at desktop workstations — not on phones. A dense multi-column layout lets admins track multiple open tickets simultaneously without endless scrolling.
- **Bootstrap 5 layout choices:**
  - `container-fluid` is used throughout the portal for wide-screen utilization — no wasted whitespace on large monitors
  - A **fixed left sidebar** in `includes/header.php` provides persistent, one-click navigation to all modules (Tickets, Maintenance, Reports, etc.) without losing context
  - **Bootstrap grid** (`col-md-4`, `col-lg-3`) creates responsive multi-column dashboards that show KPI cards, ticket counts, and SLA status side by side
  - **Bootstrap badges** (`bg-danger`, `bg-warning`, `bg-success`) provide instant visual priority coding on ticket tables
- **DataTables.js** is integrated on list pages for client-side search, sort, and pagination — critical when an admin manages dozens of concurrent tickets.
- **Bootstrap Modals** are used for adding/editing records (e.g., SLA contracts, users) so the admin never loses their place in a long table.

---

## Slide 8 — Core Feature 1: Secure Authentication
**Heading:** Secure Multi-Role Login

### What to Display
- Screenshot of `login.php`
- Diagram or bullet list of the session-based routing logic

### Talking Points
- The login form (`login.php`) submits credentials via POST to `api/auth/login.php`.
- **Backend logic in `api/auth/login.php`:**
  1. Sanitizes the email and password inputs
  2. Queries the `users` table using a **PDO Prepared Statement** (protects against SQL injection)
  3. Verifies the submitted password against the stored `password_hash` using PHP's `password_verify()`
  4. On success: stores `$_SESSION['user_id']`, `$_SESSION['role']`, and `$_SESSION['name']`
  5. Routes the user: `role = 'admin'` → `pages/dashboard.php` (admin view); `role = 'client'` → `pages/dashboard.php` (client view, filtered data)
- **Every protected page** begins with `require '../includes/auth.php'` which calls `session_start()` and checks `$_SESSION['user_id']` — if not set, the user is immediately redirected back to `login.php`.
- Admin-only pages additionally call `guardRole('admin')` from `includes/functions.php`, which returns a 403 JSON error (for API endpoints) or redirects to the dashboard (for portal pages).
- **No public sign-up exists anywhere.** Attempting to access any `pages/*.php` URL directly while not logged in results in an immediate redirect to the login page.

---

## Slide 9 — Core Feature 2: Management Dashboard (Admin)
**Heading:** Supplier / Provider Catalog Management

### What to Display
- Screenshot of `pages/dashboard.php` (Admin view) showing KPI cards
- Screenshot of `pages/tickets.php` or `pages/users.php` showing the DataTables table
- Screenshot of a Bootstrap Modal for adding/editing a record

### Talking Points
- The Admin dashboard (`pages/dashboard.php`) provides a **multi-column KPI overview**: total open tickets, critical ticket count, clients with SLA contracts, and maintenance hours consumed this month — all computed live from the database via SQL queries.
- **`pages/tickets.php`** is the Admin's primary work queue — a filterable, sortable table of all tickets across all clients. Key features:
  - **DataTables.js integration:** Adds client-side search box, column sorting, and pagination to the Bootstrap table with zero additional code
  - **Priority badges:** Color-coded using Bootstrap badge classes — red for Critical, yellow for High, green for Low — for instant visual triage
  - **Status filter dropdown:** Admin can filter by `open`, `in_progress`, `resolved`, or `closed` without a page reload
- **Bootstrap Modals** are used throughout the admin panel for CRUD operations (e.g., creating users, editing SLA contracts) so the admin never navigates away from their current list view.
- **`pages/users.php`** and **`pages/clients.php`** are Admin-exclusive pages (`guardRole('admin')` is enforced at the top of each).

---

## Slide 10 — Core Feature 3: Ordering & Request Portal (Client)
**Heading:** Client Procurement Interface

### What to Display
- Screenshot of `pages/ticket_create.php` (the client's ticket submission form)
- Screenshot of `pages/tickets.php` filtered to the client's own tickets

### Talking Points
- Clients access a **filtered view** of the portal — they can only see their own tickets, their own SLA usage, and their own reports. The filtering is applied server-side based on `$_SESSION['user_id']`.
- **Ticket submission (`pages/ticket_create.php`):**
  - Client fills in: Subject, Description, and Priority (`low` / `high` / `critical`)
  - On submit, the form POSTs to `api/tickets/create.php`
  - The API validates inputs, then inserts a new row into `tickets` with `status = 'open'` and `client_id` derived from the session
  - A corresponding first entry is auto-inserted into `ticket_activities` to start the audit trail
- **Session-based data isolation:** Because `$_SESSION['user_id']` is verified server-side on every API call, a client cannot view or modify another client's tickets — even by manipulating URL parameters. The query always includes a `WHERE client_id = ?` clause tied to the session.
- The client's dashboard shows their current SLA usage (hours consumed vs. pool) computed live from `v_sla_usage`.

---

## Slide 11 — Core Feature 4: Backend Transaction Automation
**Heading:** Automated Status Synchronization

### What to Display
- Screenshot of `pages/ticket_view.php` showing the status update controls
- Screenshot of the Activity Trail section below a ticket

### Talking Points
- When the Admin updates a ticket's status, the backend in `api/tickets/update.php` performs **two atomic operations** in sequence:
  1. **UPDATE** the `tickets` table: sets the new `status` and, if status is `'resolved'`, sets `resolved_at = NOW()`
  2. **INSERT** a new row into `ticket_activities`: records who changed it, what the change was, and when — building the full audit trail
- **Why `resolved_at` matters:** This timestamp is used by the `v_monthly_report` VIEW to calculate `avg_response_hrs` (average resolution time) and detect `sla_breaches` (tickets resolved after the contract's `response_time_hrs` limit).
- **SLA hour deduction is fully automated** via the database VIEW approach (3NF design):
  - When Admin logs a maintenance entry in `api/maintenance/create.php`, `hours_spent` is recorded in `maintenance_logs`
  - The `v_sla_usage` VIEW then automatically aggregates all `hours_spent` values for that client, computing `hours_used` and `hours_remaining` live — no manual update query needed
- This eliminates the risk of desynchronized data: the SLA pool figures are always mathematically correct because they are computed, not stored.

---

## Slide 12 — Core Feature 5: Professional Document Generation
**Heading:** Dynamic Invoice / Manifest Generation

### What to Display
- Screenshot of `pages/reports.php` showing a generated monthly report
- Highlight the print-ready layout

### Talking Points
- **Monthly Service Report generation (`pages/reports.php`):**
  - Admin selects a client and a month/year, then clicks "Generate Report"
  - The form POSTs to `api/reports/generate.php`, which:
    1. Checks if a report record already exists for that client+month (`UNIQUE KEY uq_report_per_month`)
    2. If not, inserts a new record into `reports` (metadata only: who generated it, when)
    3. Returns the live-computed figures from `v_monthly_report` as JSON
  - The report page then dynamically renders the full report: total tickets, resolved count, SLA compliance %, average response time, hours used, site visits used
- **Dynamic data pull:** PHP uses the `report_id` (Transaction ID equivalent) to query `v_monthly_report WHERE report_id = ?`, ensuring each report always shows the correct month's figures for the correct client.
- **Desktop printing:** The report layout is designed for `window.print()` — a "Print Report" button triggers the browser's native print dialog, producing a clean, professional PDF-ready document ready for client delivery.

---

## Slide 13 — Core Feature 6: Administrative Analytics
**Heading:** Executive Data Visualization

### What to Display
- Screenshot of `pages/reports.php` showing the two Chart.js charts
- Point out `resolutionChart` and `peakDaysChart` canvas elements

### Talking Points
- The reports page includes **two Chart.js bar charts** implemented in `public/js/charts.js`:
  1. **Average Ticket Resolution Time** (`resolutionChart`) — a bar chart showing average hours to resolve tickets per month, helping the Admin track whether the team is improving or slowing down
  2. **Peak Support Request Days** (`peakDaysChart`) — a bar chart showing which days of the week receive the most ticket submissions, enabling the Admin to schedule technicians proactively
- **Data aggregation:** The chart data is pulled from the database via SQL aggregation queries:
  - Resolution time: `AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at))` grouped by month
  - Peak days: `COUNT(id)` grouped by `DAYOFWEEK(created_at)`
- **Chart.js integration:** `public/js/charts.js` initializes both charts using the `Chart` constructor on `<canvas>` elements. The data arrays passed to Chart.js are populated by PHP echoing the query results as JavaScript variables before the script runs.
- This gives the Admin **executive-level visibility** — they can present these charts directly to clients during service review meetings to demonstrate SLA compliance.

---

## Slide 14 — System Security & Data Integrity
**Heading:** Security Measures Implemented

### What to Display
- Code snippet of a PDO Prepared Statement (from `api/auth/login.php`)
- Code snippet of `includes/auth.php` session validation

### Talking Points
The system implements four layers of security:

#### 1. Prepared Statements — SQL Injection Prevention
All database queries use **PDO Prepared Statements**. User input is **never** concatenated directly into an SQL string. Example:
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
$stmt->execute([$email]);
```
This means even if a user types `' OR 1=1 --` into the email field, it is treated as literal text — not executable SQL.

#### 2. Session Validation — Unauthorized URL Access Prevention
Every portal page begins with `require '../includes/auth.php'`, which checks `$_SESSION['user_id']`. Typing a URL like `http://localhost/TechniServe/pages/users.php` directly while not logged in immediately redirects to `login.php`.

#### 3. Role-Based URL Guard — Privilege Escalation Prevention
Admin-only pages call `guardRole('admin')`. If a logged-in client attempts to access an admin URL directly, they are redirected to their own dashboard or receive a 403 Forbidden response on API endpoints.

#### 4. Password Hashing — Credential Security
Passwords are stored using PHP's `password_hash($plain, PASSWORD_BCRYPT)` and verified with `password_verify()`. Plain-text passwords are never stored or logged anywhere.

#### 5. .htaccess Protection
The `.htaccess` file blocks direct browser access to `includes/`, `sql/`, and `api/` directories, preventing raw PHP files and SQL schema files from being accessed via URL.

---

## Slide 15 — Conclusion & Future Enhancements
**Heading:** Project Summary & Next Steps

### What to Display
- Summary checklist of completed features
- Future roadmap bullets

### What Was Achieved (6-Week Timeline)

| ✅ Feature | Description |
|---|---|
| Multi-Role Authentication | Secure PHP session login with RBAC for Admin and Client |
| SLA Contract Management | Full CRUD for SLA terms with automatic usage tracking via SQL VIEWs |
| Support Ticket Routing | Priority-based (Low/High/Critical) ticket lifecycle with full activity audit trail |
| Automated Maintenance Logging | Maintenance activities auto-deduct from client SLA hours pool |
| Monthly Report Generation | Live-computed SLA compliance reports with print functionality |
| Chart.js Analytics | Resolution time trends and peak request day visualization |
| 3NF Database Design | 8-table normalized schema with 2 computed VIEWs replacing derived columns |
| Lead Capture System | Public landing page form feeding an admin-reviewed approval queue |

### Future Enhancements (If Scaled Up)

- 📱 **SMS/Email Notifications** — Automatically notify clients when their ticket status changes using Twilio or PHPMailer
- 💬 **Live In-App Chat** — Real-time client-to-admin messaging via WebSockets for faster issue triage
- 📡 **REST API Layer** — Expose endpoints for mobile app integration so technicians can update ticket statuses from the field
- 📊 **Advanced Analytics Dashboard** — Predictive SLA breach alerts, per-client uptime scoring, and exportable CSV reports
- 🔐 **Two-Factor Authentication (2FA)** — Add an OTP layer for admin logins to protect against credential theft
- ☁️ **Cloud Deployment Upgrade** — Migrate from AwardSpace free tier to AWS or DigitalOcean for guaranteed uptime and cron job support (enabling automated monthly report emails)

### Closing Statement
> *"TechniServe transforms a phone-and-spreadsheet IT support workflow into a structured, secure, and data-driven B2B platform — giving IT providers the tools to deliver accountable, SLA-compliant service at scale."*

---

*Document prepared by: Alamo, Avryl Raven A. — Project Manager / Lead Analyst*
*TechniServe | IT Managed Services & SLA Portal | 2026*
