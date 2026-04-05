# TechniServe: IT Managed Services & SLA Portal

> A B2B platform for IT firms to deliver managed maintenance services to corporate client offices under a formal Service Level Agreement (SLA).

---

## Table of Contents

- [About the Project](#about-the-project)
- [Project Objectives](#project-objectives)
- [Authentication & Onboarding Model](#authentication--onboarding-model)
- [Tech Stack](#tech-stack)
- [File Structure](#file-structure)
- [Getting Started](#getting-started)
- [Database Setup](#database-setup)
- [Deployment](#deployment)
- [Team & Branch Guide](#team--branch-guide)
- [Guide for Each Pair](#guide-for-each-pair)
- [Git Workflow](#git-workflow)

---

## About the Project

TechniServe is a full B2B web portal with two distinct layers:

**Layer 1 — Public Landing Page (`index.php`):**
A professionally designed marketing page targeting corporate decision-makers. Its sole purpose is to convert business visitors into leads or paying clients by showcasing the IT managed services offering, SLA plan tiers, and a lead capture form. There is **no public sign-up page** — visitors submit a "Request Access" inquiry that the Admin reviews and manually onboards.

**Layer 2 — Authenticated Portal (`/pages/`):**
The secure operational system used daily by IT Admins, Technicians, and Clients. It covers SLA contract management, support ticket routing, automated maintenance logging, and monthly service report generation.

---

## Project Objectives

1. **Secure Multi-Role Authentication** — Portals for IT Service Providers (Admin) and Corporate Offices (Client)
2. **SLA Contract Management (CRUD)** — Admin manages active SLA contracts, tracking inclusions e.g. monthly support hours, free site visits
3. **Support Ticket Routing Workflow** — Clients submit IT issues categorized by priority: Low, High, Critical
4. **Automated Maintenance Logging** — System auto-deducts utilized support hours from the client's monthly SLA pool
5. **Monthly Service Report Generation** — Auto-generate formal summaries of resolved tickets and uptime per client per month
6. **PC-Optimized Power User Interface** — Dense multi-column dashboards for technicians to track open tickets simultaneously
7. **Administrative Data Visualization** — Charts for average ticket resolution times and peak support request days

---

## Authentication & Onboarding Model

This project uses a **controlled access model** — no public registration of any kind.

### Client Onboarding (3-step workflow)

```
Step 1 — Lead Generation
  Visitor fills "Request Access" form on index.php landing page
  → Data saved to leads table in MySQL
  → Admin receives the inquiry in pages/leads.php

Step 2 — Admin Vetting
  Admin reviews the lead inside the portal
  → Admin decides to approve or reject
  → If approved: Admin manually creates the client account

Step 3 — Manual Onboarding
  Admin uses pages/user_form.php to create the client's login account
  Admin uses pages/sla_contract_form.php to assign their SLA pool
  (e.g. 20 hours/month, 5 free site visits)
  → Client receives their login credentials directly from Admin
```

**Why no self-signup for clients?** In professional B2B IT services, a client must have a signed SLA contract before submitting tickets. Allowing open registration would let anyone file "Critical" tickets without a contract.

### Admin & Technician Account Creation

- **No public link or form** exists to create Admin or Technician accounts
- Only an existing Admin uses `pages/user_form.php` inside the portal to create staff accounts
- There is no `/register.php` or `/signup.php` anywhere in this project

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, Bootstrap 5 (local — no CDN), Vanilla JavaScript |
| Backend | PHP 7.x |
| Database | MySQL via AwardSpace phpMyAdmin |
| Hosting | AwardSpace Free Tier |
| Version Control | Git & GitHub |
| Design | Figma (Wireframes, Prototype, Component Library) |

> ⚠️ Bootstrap is loaded **locally** — not from a CDN. AwardSpace free tier does not guarantee CDN availability.
> ⚠️ No cron jobs on AwardSpace free tier. Monthly reports are generated on-demand by the Admin.

---

## File Structure

```
TechniServe/
│
│   ══ ROOT ═══════════════════════════════════════════════════════════
│
├── 🌐 index.php                      # B2B LANDING PAGE — hero, features,
│                                     # SLA plans, lead capture form, footer
│                                     # NOT a redirect — this IS the full page
│
├── 🐘 login.php                      # Login page — HTML form only (Pair A)
│                                     # Submits to api/auth/login.php
│
├── 🐘 logout.php                     # Destroys session → redirects to index.php
│
├── ⚙️  .htaccess                      # Blocks direct access to includes/ sql/ api/
│
├── ⚙️  .gitignore                     # Excludes db_config.php from all commits
│
├── 🐘 db_config.php                  # DB credentials — GITIGNORED — never commit
│
└── 📄 README.md
│
│   ══ PAGES (authenticated portal) ═══════════════════════════════════
│
├── 📁 pages/
│   ├── 🐘 dashboard.php              # Overview — multi-column power user layout
│   ├── 🐘 tickets.php                # All tickets — filterable by priority & status
│   ├── 🐘 ticket_view.php            # Single ticket detail + activity trail
│   ├── 🐘 ticket_create.php          # Create ticket form
│   ├── 🐘 maintenance.php            # Maintenance log history per client
│   ├── 🐘 maintenance_create.php     # Log new maintenance activity
│   ├── 🐘 sla_contracts.php          # SLA contract list — Admin CRUD
│   ├── 🐘 sla_contract_form.php      # Create / edit SLA contract form
│   ├── 🐘 reports.php                # Monthly service report + Chart.js charts
│   ├── 🐘 leads.php                  # Admin reviews Request Access submissions
│   ├── 🐘 clients.php                # Client company list — Admin only
│   ├── 🐘 client_form.php            # Create / edit client company
│   ├── 🐘 users.php                  # User account list — Admin only
│   ├── 🐘 user_form.php              # Create / edit user account — Admin only
│   └── 🐘 profile.php                # Logged-in user's own profile page
│
│   ══ API (backend endpoints) ═════════════════════════════════════════
│
├── 📁 api/
│   ├── 📁 auth/
│   │   ├── 🐘 login.php              # POST: verify login → create session → redirect
│   │   └── 🐘 logout.php            # POST: destroy session → redirect index.php
│   │
│   ├── 📁 tickets/
│   │   ├── 🐘 create.php            # POST: open new support ticket
│   │   ├── 🐘 fetch.php             # GET: list tickets with role-based filtering
│   │   ├── 🐘 update.php            # POST: update status / priority / assigned tech
│   │   └── 🐘 delete.php            # POST: soft-delete ticket (admin only)
│   │
│   ├── 📁 maintenance/
│   │   ├── 🐘 create.php            # POST: log activity + deductSLAHours()
│   │   ├── 🐘 fetch.php             # GET: retrieve maintenance logs
│   │   └── 🐘 update.php            # POST: edit maintenance entry
│   │
│   ├── 📁 sla/
│   │   ├── 🐘 create.php            # POST: create SLA contract (admin only)
│   │   ├── 🐘 fetch.php             # GET: retrieve SLA contracts
│   │   ├── 🐘 update.php            # POST: edit SLA terms
│   │   └── 🐘 deactivate.php        # POST: deactivate contract
│   │
│   ├── 📁 clients/
│   │   ├── 🐘 create.php            # POST: create client company (admin only)
│   │   ├── 🐘 fetch.php             # GET: list clients
│   │   └── 🐘 update.php            # POST: update client details
│   │
│   ├── 📁 users/
│   │   ├── 🐘 create.php            # POST: admin creates new user account only
│   │   ├── 🐘 fetch.php             # GET: list users (admin only)
│   │   ├── 🐘 update.php            # POST: edit user / reset password
│   │   └── 🐘 deactivate.php        # POST: deactivate user (admin only)
│   │
│   ├── 📁 leads/
│   │   ├── 🐘 submit.php            # POST: landing page form → insert to leads
│   │   │                            # Public — no auth required
│   │   ├── 🐘 fetch.php             # GET: list leads (admin only)
│   │   └── 🐘 update.php            # POST: admin marks approved or rejected
│   │
│   └── 📁 reports/
│       ├── 🐘 generate.php          # POST: run SLA compliance queries → save report
│       └── 🐘 fetch.php             # GET: retrieve saved reports
│
│   ══ INCLUDES (shared PHP) ════════════════════════════════════════════
│
├── 📁 includes/
│   ├── 🐘 db.php                    # PDO database connection
│   ├── 🐘 auth.php                  # Session guard — redirects if not logged in
│   ├── 🐘 functions.php             # deductSLAHours(), calcCompliance(),
│   │                                # formatDate(), hashPassword()
│   ├── 🐘 header.php                # Portal sidebar + top navbar
│   └── 🐘 footer.php                # Closing tags + JS scripts
│
│   ══ PUBLIC (static assets) ══════════════════════════════════════════
│
├── 📁 public/
│   ├── 📁 css/
│   │   ├── 🎨 bootstrap.min.css     # Bootstrap 5 — local copy
│   │   ├── 🎨 style.css             # Portal styles
│   │   └── 🎨 landing.css           # Landing page styles only
│   ├── 📁 js/
│   │   ├── ⚡ bootstrap.bundle.min.js
│   │   ├── ⚡ main.js                # Portal global scripts
│   │   ├── ⚡ tickets.js             # Ticket AJAX + badge rendering
│   │   ├── ⚡ maintenance.js         # Maintenance AJAX + SLA live update
│   │   ├── ⚡ reports.js             # Report generation + display
│   │   ├── ⚡ charts.js              # Chart.js — resolution time + peak days
│   │   └── ⚡ landing.js             # Landing page AJAX form + animations
│   └── 📁 assets/
│       ├── 🖼️  logo.png
│       ├── 🖼️  logo-white.png
│       ├── 🖼️  favicon.ico
│       └── 📁 images/
│           ├── 🖼️  hero-bg.jpg
│           └── 🖼️  feature-*.png
│
│   ══ DESIGN (Figma — not deployed) ═══════════════════════════════════
│
├── 📁 design/
│   ├── 📁 figma/
│   │   ├── 🎨 wireframes.fig
│   │   ├── 🎨 prototype.fig
│   │   └── 🎨 components.fig
│   └── 📁 exports/
│       ├── 📁 screens/
│       └── 📁 icons/
│
├── 📄 design-system.md               # Colors, fonts, spacing — shared with all pairs
│
│   ══ SQL ════════════════════════════════════════════════════════════════
│
├── 📁 sql/
│   ├── 🗄️  schema.sql                # All CREATE TABLE statements
│   ├── 🗄️  seed.sql                  # Sample data for testing
│   └── 📁 migrations/
│       └── 🗄️  001_add_sla_fields.sql
│
│   ══ DOCS ═══════════════════════════════════════════════════════════════
│
└── 📁 docs/
    ├── 🖼️  ERD.png
    ├── 📄 SRS.md
    ├── 📄 deployment.md
    └── 📄 team-tasks.md
```

---

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/)
- PHP 7.x · MySQL · Git
- Postman or Thunder Client (VS Code extension) — for Pair B API testing

### Local Setup

1. Clone the repository
```bash
git clone https://github.com/ravennhuu/TechniServe.git
cd TechniServe
```

2. Start Apache and MySQL in XAMPP/WAMP

3. Create your local `db_config.php` at the project root — **never commit this file**
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'techniServe');
define('DB_USER', 'root');
define('DB_PASS', '');
```

4. Set up the database via phpMyAdmin
   - Create database `techniServe`
   - SQL tab → paste and run `sql/schema.sql`
   - SQL tab → paste and run `sql/seed.sql`

5. Open the project
```
http://localhost/TechniServe/            ← Landing page
http://localhost/TechniServe/login.php   ← Portal login
```

---

## Database Setup

| Table | Purpose |
|---|---|
| `users` | All accounts — created by Admin only, no public registration |
| `clients` | Client company profiles linked to a user account |
| `sla_contracts` | SLA terms: monthly hours pool, site visits, response limits |
| `tickets` | Support tickets with priority Low/High/Critical and status |
| `ticket_activities` | Every status change on a ticket — full audit trail |
| `maintenance_logs` | Activities performed — auto-deducts from SLA hours pool |
| `reports` | Generated monthly service report records |
| `leads` | Landing page "Request Access" form submissions |

> ⚠️ `db_config.php` is in `.gitignore`. Each member creates their own local copy. Never commit it.

---

## Deployment

1. Log in to AwardSpace control panel
2. Create a MySQL database — save host, name, user, password
3. AwardSpace phpMyAdmin → SQL tab → run `schema.sql` then `seed.sql`
4. Upload all files **except** `db_config.php` and `design/`
5. Create `db_config.php` manually on the server with live AwardSpace credentials
6. Visit your domain — `index.php` displays the landing page

---

## Team & Branch Guide

| Role | Members | Branch |
|---|---|---|
| Project Manager / Lead Analyst | Alamo, Avryl Raven A. | `feature/reports-log` |
| UI/UX & Frontend Developer | San Diego, Francoise Maris A. · Estilo, Irish Mae C. | `feature/ui-design` |
| Backend & API Developer | Pitogo, Gian Paulo C. · Panganiban, Jiyan Braian R. | `feature/auth-backend` |
| Database Administrator (DBA) | Pontañeles, Tito III P. · Sapida, Jake Andrei A. | `feature/ticketing-system` |

---

### File Scope Per Pair

This table defines exactly which files each pair is allowed to create, edit, and commit. **Never modify a file that belongs to another pair** — doing so will cause merge conflicts when branches are merged into `develop`.

---

#### Leader — Raven · `feature/reports-log`

| File | Action |
|---|---|
| `api/reports/generate.php` | ✅ Create & edit |
| `api/reports/fetch.php` | ✅ Create & edit |
| `api/leads/update.php` | ✅ Create & edit |
| `index.php` | ✅ PHP backend logic only (session redirect if logged in) |
| `.htaccess` | ✅ Create & edit |
| `.gitignore` | ✅ Create & edit |
| `README.md` | ✅ Create & edit |
| `design-system.md` | ✅ Create & edit |
| `docs/SRS.md` | ✅ Create & edit |
| `docs/ERD.png` | ✅ Create & edit |
| `docs/deployment.md` | ✅ Create & edit |
| `docs/team-tasks.md` | ✅ Create & edit |
| `pages/` — all files | ❌ Do not touch — Pair A owns these |
| `includes/db.php` | ❌ Do not touch — Pair B owns this |
| `includes/auth.php` | ❌ Do not touch — Pair B owns this |
| `sql/` — all files | ❌ Do not touch — Pair C owns these |
| `public/` — all files | ❌ Do not touch — Pair A owns these |

---

#### Pair A — Francoise & Irish · `feature/ui-design`

| File | Action |
|---|---|
| `design/figma/wireframes.fig` | ✅ Create & edit |
| `design/figma/prototype.fig` | ✅ Create & edit |
| `design/figma/components.fig` | ✅ Create & edit |
| `design/exports/screens/` | ✅ Create & edit |
| `design/exports/icons/` | ✅ Create & edit |
| `index.php` | ✅ HTML & CSS structure only — no PHP session logic |
| `login.php` | ✅ HTML form only — no PHP session logic |
| `pages/dashboard.php` | ✅ Create & edit |
| `pages/tickets.php` | ✅ Create & edit |
| `pages/ticket_view.php` | ✅ Create & edit |
| `pages/ticket_create.php` | ✅ Create & edit |
| `pages/maintenance.php` | ✅ Create & edit |
| `pages/maintenance_create.php` | ✅ Create & edit |
| `pages/sla_contracts.php` | ✅ Create & edit |
| `pages/sla_contract_form.php` | ✅ Create & edit |
| `pages/reports.php` | ✅ Create & edit |
| `pages/leads.php` | ✅ Create & edit |
| `pages/clients.php` | ✅ Create & edit |
| `pages/client_form.php` | ✅ Create & edit |
| `pages/users.php` | ✅ Create & edit |
| `pages/user_form.php` | ✅ Create & edit |
| `pages/profile.php` | ✅ Create & edit |
| `includes/header.php` | ✅ Create & edit |
| `includes/footer.php` | ✅ Create & edit |
| `public/css/bootstrap.min.css` | ✅ Add (download from Bootstrap website) |
| `public/css/style.css` | ✅ Create & edit |
| `public/css/landing.css` | ✅ Create & edit |
| `public/js/bootstrap.bundle.min.js` | ✅ Add (download from Bootstrap website) |
| `public/js/main.js` | ✅ Create & edit |
| `public/js/tickets.js` | ✅ Create & edit |
| `public/js/maintenance.js` | ✅ Create & edit |
| `public/js/reports.js` | ✅ Create & edit |
| `public/js/charts.js` | ✅ Create & edit |
| `public/js/landing.js` | ✅ Create & edit |
| `public/assets/logo.png` | ✅ Add image asset |
| `public/assets/logo-white.png` | ✅ Add image asset |
| `public/assets/favicon.ico` | ✅ Add image asset |
| `public/assets/images/hero-bg.jpg` | ✅ Add image asset |
| `public/assets/images/feature-*.png` | ✅ Add image assets |
| `includes/db.php` | ❌ Do not touch — Pair B owns this |
| `includes/auth.php` | ❌ Do not touch — Pair B owns this |
| `includes/functions.php` | ❌ Do not touch — Pair B owns this |
| `api/` — all files | ❌ Do not touch — Pair B owns these |
| `sql/` — all files | ❌ Do not touch — Pair C owns these |
| `db_config.php` | ❌ Do not commit — create your own local copy only |

---

#### Pair B — Gian & Jiyan · `feature/auth-backend`

| File | Action |
|---|---|
| `includes/db.php` | ✅ Create & edit |
| `includes/auth.php` | ✅ Create & edit |
| `includes/functions.php` | ✅ Create & edit |
| `logout.php` | ✅ Create & edit |
| `api/auth/login.php` | ✅ Create & edit |
| `api/auth/logout.php` | ✅ Create & edit |
| `api/tickets/create.php` | ✅ Create & edit |
| `api/tickets/fetch.php` | ✅ Create & edit |
| `api/tickets/update.php` | ✅ Create & edit |
| `api/tickets/delete.php` | ✅ Create & edit |
| `api/maintenance/create.php` | ✅ Create & edit |
| `api/maintenance/fetch.php` | ✅ Create & edit |
| `api/maintenance/update.php` | ✅ Create & edit |
| `api/sla/create.php` | ✅ Create & edit |
| `api/sla/fetch.php` | ✅ Create & edit |
| `api/sla/update.php` | ✅ Create & edit |
| `api/sla/deactivate.php` | ✅ Create & edit |
| `api/clients/create.php` | ✅ Create & edit |
| `api/clients/fetch.php` | ✅ Create & edit |
| `api/clients/update.php` | ✅ Create & edit |
| `api/users/create.php` | ✅ Create & edit |
| `api/users/fetch.php` | ✅ Create & edit |
| `api/users/update.php` | ✅ Create & edit |
| `api/users/deactivate.php` | ✅ Create & edit |
| `api/leads/submit.php` | ✅ Create & edit |
| `api/leads/fetch.php` | ✅ Create & edit |
| `pages/` — all files | ❌ Do not touch — Pair A owns these |
| `public/` — all files | ❌ Do not touch — Pair A owns these |
| `sql/` — all files | ❌ Do not touch — Pair C owns these |
| `api/reports/` — all files | ❌ Do not touch — Leader owns these |
| `api/leads/update.php` | ❌ Do not touch — Leader owns this |
| `db_config.php` | ❌ Do not commit — create your own local copy only |

---

#### Pair C / DBA — Tito & Jake · `feature/ticketing-system`

| File | Action |
|---|---|
| `sql/schema.sql` | ✅ Create & edit — push on Day 1 of Week 1 |
| `sql/seed.sql` | ✅ Create & edit |
| `sql/migrations/001_add_sla_fields.sql` | ✅ Create & edit |
| `docs/ERD.png` | ✅ Export and add after ERD is finalized |
| `pages/` — all files | ❌ Do not touch — Pair A owns these |
| `public/` — all files | ❌ Do not touch — Pair A owns these |
| `api/` — all files | ❌ Do not touch — Pair B and Leader own these |
| `includes/` — all files | ❌ Do not touch — Pair B owns these |
| `db_config.php` | ❌ Do not commit — create your own local copy only |

---

> 🔴 **Rule:** If a file is marked ❌ for your pair, do not open it, do not edit it, and do not commit any changes to it. Even accidentally saving a file and committing it will cause a merge conflict for the whole team.

> 🟡 **Shared files:** `index.php` and `login.php` are shared between the Leader (PHP logic) and Pair A (HTML/CSS). Pair A writes the HTML first, Leader fills in the PHP session block at the top during Week 5 integration. Do not edit each other's section.

---

## Guide for Each Pair

---

### Pair A — UI/UX & Frontend Developer
**Francoise & Irish**

Your job is everything the user sees and interacts with. You own the design in Figma, all the HTML pages, all the CSS styling, and all the JavaScript interactions. You do not write any database queries or PHP backend logic — that is Pair B's job.

---

#### Week 1: Make Figma Wireframes for Every Screen

Before writing a single line of code, you must design every screen in Figma first. A wireframe is a simple black-and-white sketch showing where elements go on the page — no colors, no real images, just boxes and labels.

**Screens you must wireframe:**
1. `index.php` — the landing page (see sections below)
2. `login.php` — the login form
3. `pages/dashboard.php` — three versions: Admin view, Technician view, Client view
4. `pages/tickets.php` — ticket list table with filter dropdowns
5. `pages/ticket_view.php` — single ticket with activity trail below
6. `pages/ticket_create.php` — form with priority dropdown
7. `pages/maintenance.php` — maintenance log table
8. `pages/maintenance_create.php` — maintenance entry form
9. `pages/sla_contracts.php` — SLA contract table
10. `pages/sla_contract_form.php` — SLA contract create/edit form
11. `pages/reports.php` — report viewer with two chart areas
12. `pages/leads.php` — leads table with Approve/Reject buttons
13. `pages/users.php` — user list table
14. `pages/user_form.php` — create/edit user form

When wireframes are approved by Raven at end of Week 1, proceed to hi-fi mockups in Week 2.

---

#### Week 2: Make Hi-Fi Mockups and Write design-system.md

Hi-fi mockups are full-color, pixel-accurate designs of every screen in Figma. Apply your brand colors, fonts, spacing, and real content. Then write `design-system.md` — a document that tells the whole team:
- What primary color you used (e.g. `#4F46E5`)
- What font family (e.g. `Inter`, `Poppins`)
- What the base font size is
- What spacing units you use (e.g. 8px grid)
- What Bootstrap component classes you used for buttons, cards, tables

This file must be pushed to the branch before Week 3 so Pair B and Pair C know the design rules.

---

#### Week 3–4: Build All HTML Pages Using Bootstrap 5

Now you translate your Figma designs into actual code. You work in `feature/ui-design` branch.

**How Bootstrap 5 works:** Bootstrap is a CSS library that gives you ready-made components. You write HTML and add Bootstrap class names to style things. Bootstrap is loaded from `public/css/bootstrap.min.css` — do NOT use a CDN link. Example:

```html
<!-- A Bootstrap button -->
<button class="btn btn-primary">Submit</button>

<!-- A Bootstrap table -->
<table class="table table-hover table-bordered">
  <thead>...</thead>
  <tbody>...</tbody>
</table>
```

**Every portal page (`pages/*.php`) must start with:**
```php
<?php
require '../includes/auth.php';   // Pair B writes this — it checks if user is logged in
require '../includes/header.php'; // This is your sidebar and top navbar
?>

<!-- your page HTML here -->

<?php require '../includes/footer.php'; ?>
```

**During Weeks 3–4 you do not have real data yet.** Use fake hardcoded data at the top of each PHP file to make the page look realistic. This is called "dummy data":

```php
<?php
// Fake data — replace with real API calls later
$tickets = [
    ['id' => 1001, 'subject' => 'Network switch failure', 'priority' => 'critical', 'status' => 'open',     'client' => 'Acme Corp'],
    ['id' => 1002, 'subject' => 'Printer offline',        'priority' => 'low',      'status' => 'resolved', 'client' => 'BPI Office'],
    ['id' => 1003, 'subject' => 'Email server slow',      'priority' => 'high',     'status' => 'open',     'client' => 'Globe BPO'],
];
?>
```

Then loop through it in HTML:
```html
<tbody>
  <?php foreach ($tickets as $ticket): ?>
    <tr>
      <td>#<?= $ticket['id'] ?></td>
      <td><?= $ticket['subject'] ?></td>
      <td>
        <?php if ($ticket['priority'] === 'critical'): ?>
          <span class="badge bg-danger">Critical</span>
        <?php elseif ($ticket['priority'] === 'high'): ?>
          <span class="badge bg-warning text-dark">High</span>
        <?php else: ?>
          <span class="badge bg-success">Low</span>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
```

---

#### The Landing Page `index.php` in Detail

`index.php` is not just a simple page — it is a full B2B marketing website. It uses `public/css/landing.css` and `public/js/landing.js` only. Do NOT include `style.css` or `main.js` on this page.

The page must have these 7 sections from top to bottom:

**Section 1 — Top Navigation Bar**
- Left: TechniServe logo (use `public/assets/logo-white.png`)
- Center: links to Features, SLA Plans, Why Us, Contact
- Right: a "Client Portal Login" button linking to `login.php`
- This navbar should stick to the top when you scroll (use Bootstrap `navbar-sticky-top`)
- It should be transparent over the hero and turn solid after scrolling (use JavaScript in `landing.js`)

**Section 2 — Hero Section**
- Takes up the full height of the screen (use CSS `min-height: 100vh`)
- Background: a dark image `public/assets/images/hero-bg.jpg` with a dark overlay
- Large bold headline in white text: e.g. *"Enterprise IT Support. Backed by a Service Level Agreement."*
- One-line subtext below: e.g. *"Reduce downtime. Track every ticket. Know your SLA is being met."*
- Two buttons side by side:
  - Primary button: "Request Access" — when clicked, smoothly scrolls down to the form section
  - Secondary button (outlined): "See How It Works" — smoothly scrolls to the features section

**Section 3 — Features Section**
- Section heading: "What TechniServe Delivers"
- A 3-column grid of 7 cards. Each card has: an icon, a short title, a 1–2 sentence description
- Map each card to one of the 7 project objectives

**Section 4 — SLA Plans Section**
- 3 pricing tier cards side by side: Basic, Professional, Enterprise
- Each card shows: monthly support hours included, number of free site visits, response time SLA guarantee, price or "Contact for pricing"
- The middle card (Professional) should look highlighted — use a colored border and a "Most Popular" badge
- A line below the cards: "All plans include a formal SLA contract. Talk to our team." with a button

**Section 5 — Why Choose Us**
- 4 stat boxes: e.g. "99.5% SLA Compliance", "50+ Corporate Clients", "4-Hour Response Guarantee", "Certified IT Technicians"
- Large number + label + short description per box

**Section 6 — Request Access Form (Lead Capture)**
- Section heading: "Ready to get started? Request access."
- Subheading: "No open sign-ups. All client accounts are reviewed and manually onboarded."
- Form fields:
  - Company Name (required, text input)
  - Contact Person Full Name (required, text input)
  - Email Address (required, email input)
  - Phone Number (text input)
  - Preferred SLA Plan (dropdown: Basic / Professional / Enterprise)
  - Message (textarea)
  - Submit button: "Send Request"
- When the form is submitted, `landing.js` sends it via AJAX (`fetch()`) to `api/leads/submit.php`
- Do NOT reload the page on submit
- On success: hide the form and show a success message: *"Thank you! We'll review your request and contact you within 24 hours."*
- On error: show a red error message below the button

**Section 7 — Footer**
- Logo, tagline, quick links (Features, Plans, Contact)
- Email: support@techniServe.ph
- Copyright: © 2026 TechniServe. All rights reserved.

**There must be NO sign-up link anywhere on this page.** The Request Access form is the only way for new clients to reach TechniServe.

---

#### Charts on `reports.php` — Objective 7

You must implement two charts using Chart.js on the reports page. Chart.js is loaded via `public/js/charts.js`. Here is the starting code to put in `charts.js`:

```javascript
// Chart 1 — Average Ticket Resolution Time (bar chart)
const ctx1 = document.getElementById('resolutionChart').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
            label: 'Avg Resolution (hrs)',
            data: [5.2, 3.8, 6.1, 4.5, 3.2],  // replaced by real data later
            backgroundColor: '#4F46E5'
        }]
    }
});

// Chart 2 — Peak Support Request Days (bar chart)
const ctx2 = document.getElementById('peakDaysChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        datasets: [{
            label: 'Tickets Filed',
            data: [18, 22, 15, 20, 25, 8, 3],  // replaced by real data later
            backgroundColor: '#059669'
        }]
    }
});
```

Add two `<canvas>` elements to `reports.php` where the charts should appear:
```html
<canvas id="resolutionChart" width="400" height="200"></canvas>
<canvas id="peakDaysChart"   width="400" height="200"></canvas>
```

---

#### What You Must NOT Touch

- `includes/db.php` — Pair B's database connection file
- `includes/auth.php` — Pair B's session guard file
- Anything inside `api/` — all backend PHP logic
- Anything inside `sql/` — Pair C's database files

---

### Pair B — Backend & API Developer
**Gian & Jiyan**

Your job is all PHP logic that happens behind the scenes — the database connection, login system, sessions, and every API endpoint. Think of yourself as the engine that powers the car (Pair A's design is the car body).

**What is a PHP API endpoint?** It is a PHP file that receives data (like a form submission), does something with it (like save to the database), and returns a response (usually in JSON format). Pair A's JavaScript calls these files using `fetch()`.

---

#### Week 1: Set Up the Database Connection

Your first task is `includes/db.php`. This file creates a connection to MySQL. Every other API file will include this at the top.

```php
<?php
// includes/db.php

require_once __DIR__ . '/../db_config.php';  // loads DB_HOST, DB_NAME, DB_USER, DB_PASS

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}
```

**What is PDO?** PDO (PHP Data Objects) is the safe way to talk to a MySQL database from PHP. It prevents SQL injection attacks.

Wait for Pair C to push `sql/schema.sql` on Day 1. Once you run that in your local phpMyAdmin (inside XAMPP), you can test that `db.php` connects successfully.

**To test the connection**, create a temporary file `test_db.php` in the root:
```php
<?php
require 'includes/db.php';
echo $pdo ? "Connected successfully!" : "Failed.";
```
Open `http://localhost/TechniServe/test_db.php` in your browser. Delete this file after testing — do not commit it.

---

#### Week 2: Build the Login System

This is the most important part of the whole project. If login does not work, no one can access the portal.

**Step 1 — Build `includes/auth.php`**

This file is included at the top of every portal page. It checks if the user is logged in. If not, it kicks them to the login page.

```php
<?php
// includes/auth.php

session_start();

// If user is not logged in, send them to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
```

If you want to also check the role (e.g. admin-only pages), add this helper function to `includes/functions.php`:

```php
function guardRole($allowed_role) {
    if ($_SESSION['role'] !== $allowed_role) {
        header('Location: ../pages/dashboard.php');
        exit();
    }
}
```

Then at the top of an admin-only page, Pair A writes:
```php
<?php
require '../includes/auth.php';
guardRole('admin');
?>
```

**Step 2 — Build `api/auth/login.php`**

This file receives the login form data, checks the username and password against the database, and creates a session.

```php
<?php
// api/auth/login.php

session_start();
require '../../includes/db.php';
header('Content-Type: application/json');

// Get form data
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? '');

// Basic validation
if (!$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

// Find user in the database by email and role
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ? AND is_active = 1");
$stmt->execute([$email, $role]);
$user = $stmt->fetch();

// Check if user exists AND password matches
if ($user && password_verify($password, $user['password_hash'])) {
    // Save user info in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['name']    = $user['name'];

    // Redirect based on role
    if ($user['role'] === 'admin') {
        header('Location: ../../pages/dashboard.php');
    } elseif ($user['role'] === 'technician') {
        header('Location: ../../pages/tickets.php');
    } else {
        header('Location: ../../pages/tickets.php');
    }
    exit();
} else {
    // Wrong credentials — go back to login with error
    header('Location: ../../login.php?error=1');
    exit();
}
```

**How to test this without Pair A's frontend?** Use a temporary HTML form:
```html
<form action="api/auth/login.php" method="POST">
  Email: <input type="email" name="email"><br>
  Password: <input type="password" name="password"><br>
  Role: <select name="role">
    <option value="admin">Admin</option>
    <option value="technician">Technician</option>
    <option value="client">Client</option>
  </select><br>
  <button type="submit">Login</button>
</form>
```

---

#### Weeks 3–4: Build All API Endpoints

Every API endpoint file follows the same pattern. Here is the complete template:

```php
<?php
// Example: api/tickets/fetch.php

session_start();
require '../../includes/auth.php'; // checks login — redirects if not logged in
require '../../includes/db.php';
header('Content-Type: application/json'); // tells the browser this is JSON

// Role check example — only admin and technician can fetch all tickets
// Clients can only see their own
if ($_SESSION['role'] === 'client') {
    $stmt = $pdo->prepare("SELECT * FROM tickets WHERE client_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['client_id']]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM tickets ORDER BY created_at DESC");
    $stmt->execute();
}

$tickets = $stmt->fetchAll();
echo json_encode(['success' => true, 'data' => $tickets]);
```

**What is a prepared statement?** Instead of writing:
```php
// WRONG — dangerous, allows SQL injection
$result = $pdo->query("SELECT * FROM tickets WHERE id = " . $_GET['id']);
```

You write:
```php
// CORRECT — safe, uses placeholders (?)
$stmt = $pdo->prepare("SELECT * FROM tickets WHERE id = ?");
$stmt->execute([$_GET['id']]);
$result = $stmt->fetch();
```

Always use prepared statements. Never put user input directly into a SQL string.

---

#### The `includes/functions.php` File

This file contains helper functions used in multiple places. You must write these four functions:

```php
<?php
// includes/functions.php

/**
 * Deducts hours from a client's SLA pool.
 * Called every time a maintenance log is created (Objective 4).
 */
function deductSLAHours($pdo, $client_id, $hours) {
    $stmt = $pdo->prepare(
        "UPDATE sla_contracts
         SET hours_used = hours_used + ?
         WHERE client_id = ? AND is_active = 1"
    );
    $stmt->execute([$hours, $client_id]);
}

/**
 * Calculates SLA compliance percentage.
 * Used in generate.php for monthly reports.
 */
function calcCompliance($resolved, $total) {
    if ($total == 0) return 0;
    return round(($resolved / $total) * 100, 2);
}

/**
 * Formats a database timestamp into a readable date.
 */
function formatDate($timestamp) {
    return date('M d, Y h:i A', strtotime($timestamp));
}

/**
 * Hashes a plain-text password.
 * Used when Admin creates a new user account.
 */
function hashPassword($plain) {
    return password_hash($plain, PASSWORD_BCRYPT);
}
```

---

#### The `api/leads/submit.php` — Special Case (No Login Required)

This endpoint is called by the landing page contact form. It does NOT check if someone is logged in because it is meant for visitors who don't have accounts yet.

```php
<?php
// api/leads/submit.php — public endpoint, no auth check

require '../../includes/db.php';
header('Content-Type: application/json');

$company  = trim($_POST['company_name']   ?? '');
$contact  = trim($_POST['contact_person'] ?? '');
$email    = trim($_POST['email']          ?? '');
$phone    = trim($_POST['phone']          ?? '');
$plan     = trim($_POST['preferred_plan'] ?? '');
$message  = trim($_POST['message']        ?? '');

if (!$company || !$contact || !$email) {
    echo json_encode(['success' => false, 'message' => 'Company name, contact person, and email are required.']);
    exit();
}

$stmt = $pdo->prepare(
    "INSERT INTO leads (company_name, contact_person, email, phone, preferred_plan, message)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->execute([$company, $contact, $email, $phone, $plan, $message]);

echo json_encode(['success' => true, 'message' => 'Your request has been received.']);
```

---

#### The `api/users/create.php` — Admin Creates All Accounts

There is no public signup. This is the ONLY way new accounts are created.

```php
<?php
// api/users/create.php

session_start();
require '../../includes/auth.php';
require '../../includes/db.php';
require '../../includes/functions.php';
header('Content-Type: application/json');

// Only admin can create accounts
if ($_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$name     = trim($_POST['name']     ?? '');
$email    = trim($_POST['email']    ?? '');
$password = trim($_POST['password'] ?? '');
$role     = trim($_POST['role']     ?? '');

if (!$name || !$email || !$password || !$role) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit();
}

$hashed = hashPassword($password);  // from functions.php

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)"
);
$stmt->execute([$name, $email, $hashed, $role]);

echo json_encode(['success' => true, 'message' => 'User account created successfully.']);
```

---

#### How to Test Your API Endpoints Without Pair A's Frontend

Install **Thunder Client** in VS Code (it is a free extension). After installing:
1. Click the Thunder Client icon in the VS Code sidebar
2. Click "New Request"
3. Set the method to POST
4. Enter the URL: `http://localhost/TechniServe/api/tickets/create.php`
5. Click "Body" tab → select "Form"
6. Add your form fields and values
7. Click "Send"
8. You should see a JSON response in the right panel

If you see `{"success":true,"data":[...]}` — it works. If you see an error, check your PHP code.

---

#### Key Rules

- Priority values in ALL ticket queries must be exactly: `'low'`, `'high'`, `'critical'` — matching the database ENUM
- Every API endpoint that modifies data must use `$_POST` — never `$_GET` for inserts or updates
- Always use prepared statements — never string-concatenate user input into SQL
- Tell Pair A what field names to use in their HTML forms so they match what your PHP reads. Agree on Day 1: `$_POST['email']`, `$_POST['password']`, `$_POST['role']`
- The JSON response format you agree with Pair A: `{ "success": true, "data": [...] }` for success, `{ "success": false, "message": "..." }` for errors
- Never commit `db_config.php` to GitHub

---

### Pair C — Database Administrator (DBA)
**Tito & Jake**

Your job is the database — designing it, creating all the tables, and making sure data stays consistent and correct. You work entirely in MySQL via phpMyAdmin (both on your local XAMPP and later on AwardSpace).

**Your most important deadline: push `sql/schema.sql` to GitHub on Day 1 of Week 1.** Without your tables, Pair B cannot write any PHP queries, and Pair A cannot show any real data. Everyone is blocked until you deliver this.

---

#### What Is a Database Schema?

A schema is the blueprint of your database. It tells MySQL what tables exist, what columns each table has, and what rules apply to the data (like "this column cannot be empty" or "this ID must match a record in another table").

You write the schema as SQL code in a file called `sql/schema.sql`. When someone runs this file in phpMyAdmin, it creates all the tables automatically.

---

#### Step 1: Open phpMyAdmin in XAMPP

1. Start XAMPP and turn on Apache and MySQL
2. Open your browser and go to `http://localhost/phpmyadmin`
3. Click "New" on the left sidebar
4. Create a database named `techniServe` (exact spelling, case-sensitive)
5. Click the SQL tab at the top
6. Paste the contents of `schema.sql` and click "Go"

All 8 tables will be created automatically.

---

#### Step 2: Understand the 8 Tables You Are Creating

Here is what each table stores and why it exists:

| Table | What It Stores |
|---|---|
| `users` | Every person who can log in — admins, technicians, and clients. Created by Admin only. |
| `clients` | Company profiles of client businesses. Linked to a user account. |
| `sla_contracts` | The SLA agreement terms for each client — how many support hours per month, how many site visits, response time limits. |
| `tickets` | Every support ticket submitted by a client. Has a priority (low/high/critical) and status. |
| `ticket_activities` | The history log of every change made to a ticket — who changed what and when. |
| `maintenance_logs` | Every maintenance activity performed. Stores how many hours were spent so SLA hours can be deducted. |
| `reports` | The generated monthly service report records. Stores the final calculated numbers. |
| `leads` | Form submissions from the public landing page. Admin reviews these to decide who gets onboarded. |

---

#### Step 3: The Complete `schema.sql` File

Here is the complete, ready-to-run SQL that creates all 8 tables. Copy this exactly into `sql/schema.sql`:

```sql
-- ================================================================
-- TechniServe: IT Managed Services & SLA Portal
-- schema.sql — run this FIRST, before seed.sql
-- DBA: Pontañeles, Tito III P. & Sapida, Jake Andrei A.
-- ================================================================

CREATE DATABASE IF NOT EXISTS `techniServe`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `techniServe`;

-- ----------------------------------------------------------------
-- TABLE 1: users
-- Stores every person who can log in to the system.
-- IMPORTANT: Created by Admin only. No public registration.
-- ----------------------------------------------------------------
CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `client_id`     INT          NULL DEFAULT NULL,
  `name`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role`          ENUM('admin','technician','client') NOT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE 2: clients
-- Stores the company profile of each client business.
-- Each client company is linked to one user account (the login).
-- ----------------------------------------------------------------
CREATE TABLE `clients` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `user_id`        INT          NOT NULL,
  `company_name`   VARCHAR(150) NOT NULL,
  `address`        TEXT         NULL,
  `contact_person` VARCHAR(100) NOT NULL,
  `contact_email`  VARCHAR(150) NOT NULL,
  `contact_phone`  VARCHAR(20)  NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_clients_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- Link users back to clients (a client user knows their company)
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_client`
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`)
    ON DELETE SET NULL;

-- ----------------------------------------------------------------
-- TABLE 3: sla_contracts
-- Stores the SLA agreement for each client.
-- monthly_hours_pool = how many support hours they paid for.
-- hours_used = auto-updated whenever maintenance is logged.
-- ----------------------------------------------------------------
CREATE TABLE `sla_contracts` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `client_id`            INT          NOT NULL,
  `monthly_hours_pool`   INT          NOT NULL DEFAULT 20,
  `hours_used`           INT          NOT NULL DEFAULT 0,
  `site_visits_included` INT          NOT NULL DEFAULT 5,
  `site_visits_used`     INT          NOT NULL DEFAULT 0,
  `response_time_hrs`    INT          NOT NULL DEFAULT 4,
  `resolution_time_hrs`  INT          NOT NULL DEFAULT 24,
  `uptime_target_pct`    DECIMAL(5,2) NOT NULL DEFAULT 99.50,
  `start_date`           DATE         NOT NULL,
  `end_date`             DATE         NOT NULL,
  `is_active`            TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_sla_client`
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE 4: tickets
-- Stores every support ticket submitted by a client.
-- priority MUST be: 'low', 'high', or 'critical' — not 'medium'
-- ----------------------------------------------------------------
CREATE TABLE `tickets` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `client_id`   INT          NOT NULL,
  `created_by`  INT          NOT NULL,
  `assigned_to` INT          NULL DEFAULT NULL,
  `subject`     VARCHAR(200) NOT NULL,
  `description` TEXT         NOT NULL,
  `priority`    ENUM('low','high','critical') NOT NULL DEFAULT 'low',
  `status`      ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `resolved_at` TIMESTAMP    NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_tickets_client`
    FOREIGN KEY (`client_id`)   REFERENCES `clients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tickets_created_by`
    FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_tickets_assigned_to`
    FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`)   ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE 5: ticket_activities
-- Every change made to a ticket is logged here.
-- This powers the "Activity Trail" shown on ticket_view.php.
-- ----------------------------------------------------------------
CREATE TABLE `ticket_activities` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `ticket_id`  INT          NOT NULL,
  `user_id`    INT          NOT NULL,
  `action`     VARCHAR(100) NOT NULL,
  `note`       TEXT         NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_activity_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_activity_user`
    FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE 6: maintenance_logs
-- Logs every maintenance activity performed by a technician.
-- hours_spent is used by deductSLAHours() to reduce the client's pool.
-- ----------------------------------------------------------------
CREATE TABLE `maintenance_logs` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `ticket_id`     INT          NOT NULL,
  `client_id`     INT          NOT NULL,
  `performed_by`  INT          NOT NULL,
  `title`         VARCHAR(150) NOT NULL,
  `description`   TEXT         NULL,
  `activity_type` ENUM(
    'patch','backup','network_audit',
    'hardware_repair','software_install',
    'site_visit','remote_support','other'
  ) NOT NULL DEFAULT 'other',
  `hours_spent`   DECIMAL(5,2) NOT NULL DEFAULT 1.00,
  `scheduled_at`  TIMESTAMP    NULL DEFAULT NULL,
  `completed_at`  TIMESTAMP    NULL DEFAULT NULL,
  `status`        ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_maint_ticket`
    FOREIGN KEY (`ticket_id`)    REFERENCES `tickets`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_maint_client`
    FOREIGN KEY (`client_id`)    REFERENCES `clients`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_maint_performed_by`
    FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE 7: reports
-- Stores generated monthly service reports.
-- All numbers are pre-calculated and saved here for fast retrieval.
-- ----------------------------------------------------------------
CREATE TABLE `reports` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `client_id`        INT          NOT NULL,
  `generated_by`     INT          NOT NULL,
  `month`            TINYINT      NOT NULL,
  `year`             SMALLINT     NOT NULL,
  `total_tickets`    INT          NOT NULL DEFAULT 0,
  `resolved_tickets` INT          NOT NULL DEFAULT 0,
  `critical_count`   INT          NOT NULL DEFAULT 0,
  `high_count`       INT          NOT NULL DEFAULT 0,
  `low_count`        INT          NOT NULL DEFAULT 0,
  `sla_breaches`     INT          NOT NULL DEFAULT 0,
  `compliance_pct`   DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `avg_response_hrs` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `hours_used`       INT          NOT NULL DEFAULT 0,
  `site_visits_used` INT          NOT NULL DEFAULT 0,
  `generated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_report_per_month` (`client_id`,`month`,`year`),
  CONSTRAINT `fk_reports_client`
    FOREIGN KEY (`client_id`)    REFERENCES `clients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reports_by`
    FOREIGN KEY (`generated_by`) REFERENCES `users`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------------
-- TABLE 8: leads
-- Stores form submissions from the public landing page.
-- Admin reviews these in pages/leads.php and decides who to onboard.
-- ----------------------------------------------------------------
CREATE TABLE `leads` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `company_name`   VARCHAR(150) NOT NULL,
  `contact_person` VARCHAR(100) NOT NULL,
  `email`          VARCHAR(150) NOT NULL,
  `phone`          VARCHAR(20)  NULL,
  `preferred_plan` VARCHAR(50)  NULL,
  `message`        TEXT         NULL,
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reviewed_by`    INT          NULL DEFAULT NULL,
  `reviewed_at`    TIMESTAMP    NULL DEFAULT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_leads_reviewed_by`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB;
```

---

#### Step 4: Write the `seed.sql` File

After creating the tables, you need to fill them with test data so the team can develop without waiting for real users. This is `sql/seed.sql`.

**Important:** All passwords in the database must be hashed (encrypted). The plain text password `password123` becomes a long scrambled string when hashed. Use this pre-generated hash for all seed data users:

Hash of `password123`:
```
$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
```

```sql
-- ================================================================
-- TechniServe — seed.sql
-- Run this AFTER schema.sql
-- ================================================================

USE `techniServe`;

-- ----------------------------------------------------------------
-- 1. Insert users (Admin, Technician, and a placeholder Client)
-- All passwords are "password123"
-- ----------------------------------------------------------------
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
  ('Raven Alamo',         'admin@techniServe.ph',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  ('Gian Pitogo',         'tech@techniServe.ph',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'technician'),
  ('Client User',         'client@acmecorp.ph',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
  ('Client User 2',       'client@bpioffice.ph',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

-- ----------------------------------------------------------------
-- 2. Insert client companies
-- ----------------------------------------------------------------
INSERT INTO `clients` (`user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`) VALUES
  (3, 'Acme Corporation',  'Makati City, Metro Manila',    'Juan dela Cruz',  'client@acmecorp.ph',   '09171234567'),
  (4, 'BPI Office Manila', 'BGC, Taguig City',             'Maria Santos',    'client@bpioffice.ph',  '09281234567');

-- ----------------------------------------------------------------
-- 3. Link users to their client company
-- ----------------------------------------------------------------
UPDATE `users` SET `client_id` = 1 WHERE `id` = 3;
UPDATE `users` SET `client_id` = 2 WHERE `id` = 4;

-- ----------------------------------------------------------------
-- 4. Insert SLA contracts
-- ----------------------------------------------------------------
INSERT INTO `sla_contracts` (`client_id`, `monthly_hours_pool`, `hours_used`, `site_visits_included`, `site_visits_used`, `response_time_hrs`, `resolution_time_hrs`, `start_date`, `end_date`) VALUES
  (1, 20, 6,  5, 1, 4,  24, '2026-01-01', '2026-12-31'),
  (2, 40, 12, 8, 2, 2,  12, '2026-01-01', '2026-12-31');

-- ----------------------------------------------------------------
-- 5. Insert sample tickets
-- ----------------------------------------------------------------
INSERT INTO `tickets` (`client_id`, `created_by`, `assigned_to`, `subject`, `description`, `priority`, `status`, `resolved_at`) VALUES
  (1, 3, 2, 'Network switch failure on Floor 3',    'The main network switch on Floor 3 is unresponsive.',                 'critical',  'resolved', NOW()),
  (1, 3, 2, 'Antivirus update failed on 5 PCs',     'Windows Defender failed to update on 5 workstations.',               'high',      'open',     NULL),
  (2, 4, 2, 'Printer offline in HR Department',     'HP LaserJet on Floor 2 shows offline status.',                       'low',       'resolved', NOW()),
  (2, 4, 2, 'Email server responding slowly',       'Outlook takes 2–3 minutes to send emails.',                          'high',      'in_progress', NULL),
  (1, 3, NULL, 'VPN connection drops intermittently','Remote staff report VPN disconnects every 30 minutes.',              'critical',  'open',     NULL);

-- ----------------------------------------------------------------
-- 6. Insert ticket activity logs
-- ----------------------------------------------------------------
INSERT INTO `ticket_activities` (`ticket_id`, `user_id`, `action`, `note`) VALUES
  (1, 2, 'Status changed to In Progress', 'Dispatched technician to site.'),
  (1, 2, 'Status changed to Resolved',    'Replaced faulty switch module. Network restored.'),
  (3, 2, 'Status changed to Resolved',    'Reinstalled printer driver. Printer back online.'),
  (4, 2, 'Status changed to In Progress', 'Investigating email server logs.');

-- ----------------------------------------------------------------
-- 7. Insert maintenance logs
-- ----------------------------------------------------------------
INSERT INTO `maintenance_logs` (`ticket_id`, `client_id`, `performed_by`, `title`, `description`, `activity_type`, `hours_spent`, `completed_at`, `status`) VALUES
  (1, 1, 2, 'Network switch replacement',     'Replaced faulty 24-port switch on Floor 3.',         'hardware_repair', 3.00, NOW(), 'completed'),
  (3, 2, 2, 'Printer driver reinstallation',  'Cleared print queue and reinstalled HP driver.',      'remote_support',  1.50, NOW(), 'completed'),
  (4, 2, 2, 'Email server performance audit', 'Reviewed SMTP logs. Identified memory leak in queue.','network_audit',   2.50, NULL,  'in_progress');

-- ----------------------------------------------------------------
-- 8. Insert sample leads
-- ----------------------------------------------------------------
INSERT INTO `leads` (`company_name`, `contact_person`, `email`, `phone`, `preferred_plan`, `message`, `status`) VALUES
  ('Globe BPO Services',    'Pedro Reyes',    'it@globebpo.ph',      '09391234567', 'Professional', 'We need managed IT support for 3 floors.',    'pending'),
  ('SM Corporate Office',   'Anna Cruz',      'ict@smcorp.ph',       '09501234567', 'Enterprise',   'Looking for a long-term IT managed services partner.', 'approved');
```

---

#### Step 5: Verify Everything in phpMyAdmin

After running `schema.sql` and `seed.sql`, open phpMyAdmin and check:

1. Click on the `techniServe` database in the left panel
2. You should see 8 tables listed: `users`, `clients`, `sla_contracts`, `tickets`, `ticket_activities`, `maintenance_logs`, `reports`, `leads`
3. Click on each table and click "Browse" to see if the seed data is there
4. Verify the `users` table has 4 rows and `tickets` has 5 rows
5. If anything is missing, check the SQL tab for error messages

---

#### Step 6: Write JOIN Queries for the Reports API (Week 4–5)

Raven needs these queries for `api/reports/generate.php`. Write them and document them in `sql/schema.sql` as comments, or create a new file `sql/queries.sql`.

**Query 1 — Monthly SLA Compliance per Client:**
```sql
-- Used in: api/reports/generate.php
-- Parameters: month number (1-12), year (e.g. 2026)
SELECT
  c.company_name,
  COUNT(t.id)                                                   AS total_tickets,
  SUM(CASE WHEN t.status = 'resolved'   THEN 1 ELSE 0 END)     AS resolved_tickets,
  SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END)     AS critical_count,
  SUM(CASE WHEN t.priority = 'high'     THEN 1 ELSE 0 END)     AS high_count,
  SUM(CASE WHEN t.priority = 'low'      THEN 1 ELSE 0 END)     AS low_count,
  sc.monthly_hours_pool,
  sc.hours_used,
  (sc.monthly_hours_pool - sc.hours_used)                       AS remaining_hours,
  sc.site_visits_included,
  sc.site_visits_used,
  ROUND(
    SUM(CASE WHEN t.status = 'resolved' THEN 1 ELSE 0 END)
    / NULLIF(COUNT(t.id), 0) * 100, 2
  )                                                             AS compliance_pct,
  ROUND(
    AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.resolved_at)), 2
  )                                                             AS avg_response_hrs
FROM tickets t
JOIN clients c         ON t.client_id  = c.id
JOIN sla_contracts sc  ON sc.client_id = c.id AND sc.is_active = 1
WHERE MONTH(t.created_at) = ? AND YEAR(t.created_at) = ?
GROUP BY c.id, c.company_name, sc.monthly_hours_pool,
         sc.hours_used, sc.site_visits_included, sc.site_visits_used;
```

**Query 2 — Peak Support Request Days (for Chart.js):**
```sql
-- Used in: public/js/charts.js via api/reports/fetch.php
SELECT
  DAYNAME(created_at) AS day_name,
  COUNT(*)            AS ticket_count
FROM tickets
GROUP BY DAYNAME(created_at)
ORDER BY FIELD(DAYNAME(created_at),
  'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
```

**Query 3 — Average Resolution Time per Month (for Chart.js):**
```sql
-- Used in: public/js/charts.js via api/reports/fetch.php
SELECT
  YEAR(created_at)  AS yr,
  MONTH(created_at) AS mo,
  ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)), 2) AS avg_hrs
FROM tickets
WHERE status = 'resolved' AND resolved_at IS NOT NULL
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY yr, mo;
```

---

#### What You Must NOT Touch

- `pages/` — all the PHP view files belong to Pair A
- `public/` — all CSS and JavaScript belong to Pair A
- `api/` — all the backend endpoint files belong to Pair B
- `includes/` — the PHP helper files belong to Pair B

---

### Leader — Project Manager / Lead Analyst
**Raven**

Your job combines coding (reports API), coordination (making sure all pairs stay on schedule), and DevOps (deploying to AwardSpace). You are the only person who merges code from feature branches into `develop`.

**Day 1, Week 1:** Check that Pair C pushed `schema.sql`. Check that Pair B can connect to the database. If either is blocked, solve it immediately — every day of delay cascades.

**Week 4–5:** Build `api/reports/generate.php` using Pair C's JOIN query. Build `api/leads/update.php` so Admin can approve or reject leads. Build `api/reports/fetch.php` so the reports page can retrieve saved reports.

**Week 5 — Merging (do one at a time, in this exact order):**
```bash
git checkout develop
git merge feature/auth-backend   # Pair B first — everyone needs db.php and auth.php
git push origin develop

git merge feature/ui-design      # Pair A second
git push origin develop

git merge feature/ticketing-system  # Pair C third
git push origin develop

git merge feature/reports-log    # Your own last
git push origin develop
```

If a conflict appears, do NOT skip it. Open the conflicting file in VS Code, resolve it, then `git add .` and `git commit` before continuing.

**Week 6:** Upload all files to AwardSpace except `db_config.php` and `design/`. Create `db_config.php` manually on the server with AwardSpace credentials. Run `schema.sql` and `seed.sql` in AwardSpace phpMyAdmin. Test the full live flow: landing page → fill form → check lead in portal → log in → submit ticket → log maintenance → generate report.

**PR rule:** Every PR description must include which features were tested and which roles were used to test them. Only you click "Merge."

---

## Git Workflow

```
main
 └── develop
      ├── feature/ui-design          → Pair A (Francoise & Irish)
      ├── feature/auth-backend       → Pair B (Gian & Jiyan)
      ├── feature/ticketing-system   → Pair C / DBA (Tito & Jake)
      └── feature/reports-log        → Leader (Raven)
```

**Rules:**
- Never push directly to `main` or `develop`
- Each pair only modifies their designated files
- Submit Pull Request to `develop` when feature is complete — include test note
- Only Raven merges PRs
- Merge order: `auth-backend` → `ui-design` → `ticketing-system` → `reports-log`
- `main` receives one final merge from `develop` at deployment

---

*TechniServe — IT Managed Services & SLA Portal · BSIT-MWA Academic Project · 2026*
*Team: Alamo · San Diego · Estilo · Pitogo · Panganiban · Pontañeles · Sapida*
