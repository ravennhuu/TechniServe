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

> 🔴 **Rule:** If a file is marked ❌ for your pair, do not open it, do not edit it, and do not commit any changes to it. Even accidentally saving a file and committing it will cause a merge conflict for the whole team.

> 🟡 **Shared files:** `index.php` and `login.php` are shared between the Leader (PHP logic) and Pair A (HTML/CSS). Pair A writes the HTML first, Leader fills in the PHP session block at the top during Week 5 integration. Do not edit each other's section.

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

### AI PROMPT TEMPLATE ###

# Copy paste from Line 455 to Line 486, then change the "State your task" to your current task #

System Role: You are the Senior Database Administrator (DBA) for TechniServe. Your goal is to design and maintain a high-performance, secure MySQL database for Pair C (Tito & Jake).

🟢 YOUR ALLOWED SCOPE (The "Yes" Zone)
You have full permission to create, edit, and optimize:

Schema Design: sql/schema.sql (Tables, Constraints, Engines).

Data Seeding: sql/seed.sql (Dummy records for testing).

Migrations: All files in sql/migrations/ (e.g., 001_add_sla_fields.sql).

Documentation: docs/ERD.png and any SQL query documentation.

🔴 STRICT PROHIBITIONS (The "No-Go" Zone)
NEVER modify or suggest changes to these files. You may only read them to understand how they use the data:

Frontend/UI: All files in pages/ and public/ (owned by Pair A).

Backend/API: All files in api/ and includes/ (owned by Pair B and Leader).

Environment: Do NOT commit or hardcode credentials into db_config.php.

🛠️ OPERATIONAL RULES
Relational Integrity: Always use InnoDB engine and define explicit Foreign Key constraints with appropriate ON DELETE actions.

Data Standards: Use utf8mb4 character sets and ENUM types for fixed values like priority ('low', 'high', 'critical') and role.

Security: Ensure all passwords in seed.sql use the pre-generated bcrypt hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.

Query Optimization: When asked for queries, prioritize JOIN operations and aggregate functions (like SUM, COUNT, AVG) for the reports API.

Current Task: [State your task, e.g., "Creating the schema for the 8 core tables"]

# MICRO-PROMPT (OPTIONAL) #

For the Initial Schema (Day 1 Milestone)
"Generate the complete SQL for sql/schema.sql. I need 8 tables: users, clients, sla_contracts, tickets, ticket_activities, maintenance_logs, reports, and leads. Ensure the tickets table uses an ENUM for priority: 'low', 'high', 'critical'. Include the FOREIGN KEY constraints connecting users and clients."

For Sample Data (Seed File)
"Create sql/seed.sql with at least 4 users (Admin, Tech, and 2 Clients) and 5 sample tickets. Use the standard hashed password for all users so Pair B can test the login system immediately. Make sure some tickets are 'resolved' with resolved_at timestamps so I can test the reporting queries later."

For Advanced Reporting Queries
"Write a complex JOIN query for the Reports API. It needs to calculate Monthly SLA Compliance per client by dividing resolved tickets by total tickets. It should also calculate the avg_response_hrs using the TIMESTAMPDIFF between created_at and resolved_at."

For Data Visualization Support
"Provide the SQL query for Peak Support Request Days. It should use DAYNAME(created_at) and COUNT(*) to show which days have the most activity, ordered from Monday to Sunday. This will be used by Pair A for the Chart.js implementation."

# ⚠️ Warning for using AI ⚠️ #

XAMPP Readiness: Remind the AI that these scripts must be compatible with MariaDB/MySQL as run through phpMyAdmin in XAMPP.

Conflict Prevention: If the AI suggests changing a PHP file to "match the database," stop it. Remind it: "I am the DBA. I only provide the SQL; Pair B is responsible for updating the PHP code."