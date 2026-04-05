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

> 🔴 **Rule:** If a file is marked ❌ for your pair, do not open it, do not edit it, and do not commit any changes to it. Even accidentally saving a file and committing it will cause a merge conflict for the whole team.

> 🟡 **Shared files:** `index.php` and `login.php` are shared between the Leader (PHP logic) and Pair A (HTML/CSS). Pair A writes the HTML first, Leader fills in the PHP session block at the top during Week 5 integration. Do not edit each other's section.

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

### AI PROMPT TEMPLATE ###

# Copy paste from Line 279 to Line 310, then change the "State what you are working on" to your current task #

System Role: You are the Frontend Implementation Specialist for TechniServe. Your workspace is strictly limited to the UI/UX and Frontend layers of the application.

🟢 YOUR ALLOWED SCOPE (The "Yes" Zone)
You have full permission to create, edit, and suggest code for:

Design Assets: All files in design/ (Figma wireframes/exports).

Frontend Logic: All files in public/js/ (Vanilla JS, Chart.js, AJAX fetches).

Styling: All files in public/css/ (Bootstrap 5 and custom CSS).

User Interface: All files in pages/*.php and includes/header.php or footer.php.

Shared Structure: The HTML/CSS sections of index.php and login.php.

🔴 STRICT PROHIBITIONS (The "No-Go" Zone)
NEVER modify, open, or write code for the following. If a task requires data from these, use Dummy Data arrays instead:

Database & Config: includes/db.php, db_config.php, and the sql/ folder.

Authentication & Backend: includes/auth.php, includes/functions.php, and the api/ folder.

🛠️ OPERATIONAL RULES
No SQL: Do not write any SELECT, INSERT, or UPDATE queries.

No PDO/MySQLi: Do not use PHP database connection objects.

Bootstrap 5 Only: Use local Bootstrap classes (public/css/bootstrap.min.css)—no CDNs.

B2B Aesthetic: Maintain a minimalist, high-utility professional design for all portal pages.

Current Task: [State what you are working on, e.g., "Designing the Tickets table in pages/tickets.php"]

# MICRO-PROMPTS (OPTIONAL) #

🟢 Week 1: Wireframing & Structure
"I am in Week 1. Based on the PAIR-A-README.md, list the specific UI elements and layout requirements for the pages/dashboard.php wireframe. Provide separate layout suggestions for the Admin, Technician, and Client views as required by the project objectives."

🔵 Week 2: Design System & Hi-Fi
"Generate a design-system.md template for our project. Include sections for a primary brand color (Hex), a professional B2B font stack (e.g., Inter or Poppins), and a standardized 8px spacing grid. Also, list common Bootstrap 5 component classes we should use for buttons, cards, and tables to keep the UI consistent."

🟡 Weeks 3–4: HTML & Bootstrap Implementation
"I need to build the pages/tickets.php file. Please provide:

The PHP require statements for the header, footer, and auth check.

A PHP array of Dummy Data representing 5 tickets (include ID, subject, priority, and status).

A Bootstrap 5 responsive table that loops through this array and uses conditional logic to show different colored badges for 'Critical', 'High', and 'Low' priorities."

🟠 The Landing Page (index.php) Deep Dive
"Help me build Section 6 (Request Access Form) for the landing page. Create a Bootstrap 5 form with fields for Company Name, Contact Person, Email, Phone, and a 'Preferred SLA Plan' dropdown. Then, write the landing.js code to handle the form submission via fetch() to api/leads/submit.php without a page reload, including success/error message toggles."

🔴 Week 4: Analytics & Charts
"I am implementing Objective 7 on reports.php. Provide the HTML for two Bootstrap cards, each containing a <canvas> element for Chart.js. Then, provide the JavaScript for public/js/charts.js to initialize a bar chart for 'Average Ticket Resolution Time' using the color #4F46E5."

# ⚠️ Warning for using AI ⚠️ # 

# The Dummy Data Rule: If the AI tries to write a SELECT * FROM... statement, stop it immediately. Remind it: "I am Pair A. I do not have database access. Use a hardcoded PHP array for now."

# Asset Management: Since you are in charge of public/assets/, ask the AI for specific CSS to handle the hero-bg.jpg overlay and the transparent-to-solid navbar transition for the landing page 