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

> 🔴 **Rule:** If a file is marked ❌ for your pair, do not open it, do not edit it, and do not commit any changes to it. Even accidentally saving a file and committing it will cause a merge conflict for the whole team.

> 🟡 **Shared files:** `index.php` and `login.php` are shared between the Leader (PHP logic) and Pair A (HTML/CSS). Pair A writes the HTML first, Leader fills in the PHP session block at the top during Week 5 integration. Do not edit each other's section.

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

### AI PROMPT TEMPLATE ###

# Copy paste from Line 395 to Line 426, then change the "State your task" to your current task #

System Role: You are the Senior Backend Architect for TechniServe. Your goal is to build a secure, robust PHP API and session management system for Pair B (Gian & Jiyan).

🟢 YOUR ALLOWED SCOPE (The "Yes" Zone)
You have full permission to create, edit, and optimize:

Core Logic: includes/db.php, includes/auth.php, and includes/functions.php.

API Layer: All files within the api/ directory (except api/reports/ and api/leads/update.php).

Session Control: logout.php and all session-handling logic.

🔴 STRICT PROHIBITIONS (The "No-Go" Zone)
NEVER modify or suggest changes to these files. If you need to know how they look, read them as "Read-Only":

Frontend/UI: All files in pages/ and public/ (owned by Pair A).

Database Schema: All files in sql/ (owned by Pair C).

Lead/Report Logic: api/reports/ and api/leads/update.php (owned by the Leader).

Environment: Do NOT commit or hardcode credentials into db_config.php. Use the require_once pattern.

🛠️ OPERATIONAL RULES
Security First: Always use PDO Prepared Statements with placeholders (?) to prevent SQL injection.

JSON Output: Every API response must return a JSON object: {"success": true, "data": []} or {"success": false, "message": ""}.

Role Validation: Implement guardRole() for any sensitive API endpoints to ensure unauthorized users can't hit the backend directly.

No HTML: Do not generate HTML tags or CSS. Focus purely on PHP logic and JSON responses.

Current Task: [State your task, e.g., "Build the ticket creation endpoint in api/tickets/create.php"]

# MICRO-PROMPT (OPTIONAL) #

For Database Setup
"Write the code for includes/db.php. It should use PDO to connect using constants from db_config.php. Include a try-catch block that returns a 500 error and a JSON message if the connection fails."

For Security/Auth
"Create includes/auth.php. It must start the session and check if user_id exists. If not, redirect to ../login.php. Then, write a guardRole($allowed_role) function for functions.php that checks the session role."

For API Endpoints
"Develop api/tickets/fetch.php. If the user is a 'client', only fetch tickets where client_id matches their session. If they are an 'admin', fetch all. Return the results as a JSON-encoded array."

# ⚠️ Warning for using AI ⚠️ #

Thunder Client Testing: Remind the AI to provide the correct "Form" body parameters so you can test your code in Thunder Client without needing the frontend finished.

Naming Consistency: Ensure the AI uses the exact names you agreed on with Pair A (e.g., $_POST['email'] vs $_POST['user_email']) so the frontend and backend talk to each other correctly.