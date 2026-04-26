-- ================================================================
-- TechniServe: IT Managed Services & SLA Portal
-- ================================================================
-- FILE:    schema.sql
-- PURPOSE: Creates all 8 database tables.
--          Run this file FIRST in phpMyAdmin before seed.sql.
-- DBA:     Pontañeles, Tito III P. & Sapida, Jake Andrei A.
-- ================================================================
-- ROLE MODEL: Two roles only — admin and client.
--   admin  = the IT firm (service provider). Does everything:
--            manages contracts, resolves tickets, logs maintenance,
--            generates reports, onboards clients.
--   client = the corporate customer. Submits tickets, views their
--            own data and SLA status.
--   NOTE:  There is NO technician role in this system.
-- ================================================================
-- HOW TO RUN:
--   1. Open phpMyAdmin (http://localhost/phpmyadmin)
--   2. Click "New" on the left → name the database: techniServe
--   3. Click the SQL tab at the top
--   4. Copy and paste everything below → click "Go"
--   5. Then run seed.sql to insert test data
-- ================================================================

CREATE DATABASE IF NOT EXISTS `techniServe`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `techniServe`;


-- ================================================================
-- TABLE 1: users
-- ================================================================
-- Stores every person who can log in to the system.
--
-- role = 'admin'  → The IT firm. Full access to everything.
--                   Manages tickets, logs maintenance, generates
--                   reports, onboards clients, manages all accounts.
--
-- role = 'client' → The corporate customer. Can only submit tickets,
--                   view their own data, and view their own reports.
--
-- IMPORTANT: Only TWO roles exist. There is no 'technician'.
--            Accounts are created by Admin only — no public signup.
-- ================================================================

CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `client_id`     INT          NULL DEFAULT NULL,
                                -- Only filled for client users.
                                -- Points to clients.id for that user's company.
                                -- Always NULL for admin users.

  `name`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
                                -- Encrypted password via password_hash().
                                -- Never store plain text passwords.

  `role`          ENUM('admin','client') NOT NULL,
                                -- ONLY 'admin' or 'client'. No other values allowed.

  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
                                -- 1 = can log in, 0 = account deactivated

  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
                                -- No two users can share the same email address
) ENGINE=InnoDB;


-- ================================================================
-- TABLE 2: clients
-- ================================================================
-- Stores the company profile of each client business.
-- Separated from users because company info is not login info.
-- One client company = one user account (linked via user_id).
--
-- Created by Admin during manual onboarding after lead approval.
-- ================================================================

CREATE TABLE `clients` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `user_id`        INT          NOT NULL,
                                -- The login account that belongs to this company.
                                -- Must point to a user with role = 'client'.

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
    -- If the user account is deleted, the company profile is also deleted.

) ENGINE=InnoDB;

-- Links users back to their company.
-- users.client_id = 1 means this user belongs to clients.id = 1.
-- Done with ALTER TABLE because clients did not exist yet when users was created.
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_client`
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`)
    ON DELETE SET NULL;
    -- If the company is deleted, the user still exists but client_id becomes NULL.


-- ================================================================
-- TABLE 3: sla_contracts
-- ================================================================
-- Stores the Service Level Agreement terms for each client.
-- Admin creates and manages these. One active contract per client.
--
-- monthly_hours_pool   = total support hours the client gets per month
-- hours_used           = automatically updated when Admin logs maintenance
-- site_visits_included = free on-site visits per month
-- response_time_hrs    = how fast Admin must respond (SLA guarantee)
-- ================================================================

CREATE TABLE `sla_contracts` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `client_id`            INT          NOT NULL,

  `monthly_hours_pool`   INT          NOT NULL DEFAULT 20,
  `hours_used`           INT          NOT NULL DEFAULT 0,
  `site_visits_included` INT          NOT NULL DEFAULT 5,
  `site_visits_used`     INT          NOT NULL DEFAULT 0,
  `response_time_hrs`    INT          NOT NULL DEFAULT 4,
                                -- Admin must respond within this many hours (SLA limit)
  `resolution_time_hrs`  INT          NOT NULL DEFAULT 24,
  `uptime_target_pct`    DECIMAL(5,2) NOT NULL DEFAULT 99.50,
  `start_date`           DATE         NOT NULL,
  `end_date`             DATE         NOT NULL,
  `is_active`            TINYINT(1)   NOT NULL DEFAULT 1,
                                -- Only one active contract per client at a time
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_sla_client`
    FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB;


-- ================================================================
-- TABLE 4: tickets
-- ================================================================
-- Stores every support ticket in the system.
--
-- priority: MUST be exactly 'low', 'high', or 'critical'. No other values.
-- status flow: open → in_progress → resolved → closed
--
-- WHO DOES WHAT:
--   created_by = the client user who submitted the ticket
--                (Admin can also create on behalf of a client)
--   Admin directly picks up and resolves tickets.
--   There is NO assigned_to column — no technician exists to assign to.
-- ================================================================

CREATE TABLE `tickets` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `client_id`   INT          NOT NULL,
                              -- Which client company this ticket belongs to
  `created_by`  INT          NOT NULL,
                              -- Which user submitted this ticket (users.id)
                              -- Usually the client user, sometimes the admin

  `subject`     VARCHAR(200) NOT NULL,
  `description` TEXT         NOT NULL,

  `priority`    ENUM('low','high','critical') NOT NULL DEFAULT 'low',
                              -- MUST be exactly: 'low', 'high', or 'critical'
                              -- There is no 'medium' priority.

  `status`      ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
                              -- Admin updates this as they work on the ticket

  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                              -- MySQL automatically updates this on every row change
  `resolved_at` TIMESTAMP    NULL DEFAULT NULL,
                              -- Set by Admin when status is changed to 'resolved'

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_tickets_client`
    FOREIGN KEY (`client_id`)  REFERENCES `clients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tickets_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)   ON DELETE CASCADE

) ENGINE=InnoDB;


-- ================================================================
-- TABLE 5: ticket_activities
-- ================================================================
-- Logs every change made to a ticket — the full audit trail.
-- Powers the "Activity Trail" section on ticket_view.php.
--
-- Every time Admin changes a ticket status, the app inserts
-- a new row here automatically.
--
-- Example rows:
--   action = 'Status changed to In Progress'
--   action = 'Status changed to Resolved'
--   action = 'Priority updated to Critical'
-- ================================================================

CREATE TABLE `ticket_activities` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `ticket_id`  INT          NOT NULL,
  `user_id`    INT          NOT NULL,
                              -- Who made the change (Admin or Client)
  `action`     VARCHAR(100) NOT NULL,
  `note`       TEXT         NULL,
                              -- Optional additional note about the change
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_activity_ticket`
    FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_activity_user`
    FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`)   ON DELETE CASCADE

) ENGINE=InnoDB;


-- ================================================================
-- TABLE 6: maintenance_logs
-- ================================================================
-- Logs every maintenance activity performed by the Admin.
-- When Admin logs an activity, hours_spent is automatically
-- deducted from the client's SLA hours pool (Objective 4).
-- This is done by calling deductSLAHours() in functions.php.
--
-- performed_by = always the Admin (only Admin can log maintenance)
-- ================================================================

CREATE TABLE `maintenance_logs` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `ticket_id`     INT          NOT NULL,
                                -- Which ticket this maintenance resolves/addresses
  `client_id`     INT          NOT NULL,
                                -- Which client this work was done for
                                -- Needed directly for fast SLA hour deduction
  `performed_by`  INT          NOT NULL,
                                -- Which Admin performed the work (users.id, role='admin')

  `title`         VARCHAR(150) NOT NULL,
  `description`   TEXT         NULL,

  `activity_type` ENUM(
    'patch',
    'backup',
    'network_audit',
    'hardware_repair',
    'software_install',
    'site_visit',
    'remote_support',
    'other'
  ) NOT NULL DEFAULT 'other',

  `hours_spent`   DECIMAL(5,2) NOT NULL DEFAULT 1.00,
                                -- Hours used for this activity.
                                -- This number is deducted from sla_contracts.hours_used.

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


-- ================================================================
-- TABLE 7: reports
-- ================================================================
-- Stores generated monthly service reports.
-- Admin triggers report generation on-demand (no cron jobs on AwardSpace).
-- Numbers are pre-calculated and saved here for fast retrieval.
--
-- The UNIQUE KEY prevents generating a duplicate report for the
-- same client in the same month and year.
-- ================================================================

CREATE TABLE `reports` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `client_id`        INT          NOT NULL,
  `generated_by`     INT          NOT NULL,
                                   -- Which Admin generated this report

  `month`            TINYINT      NOT NULL,    -- 1 = January ... 12 = December
  `year`             SMALLINT     NOT NULL,    -- e.g. 2026

  `total_tickets`    INT          NOT NULL DEFAULT 0,
  `resolved_tickets` INT          NOT NULL DEFAULT 0,
  `critical_count`   INT          NOT NULL DEFAULT 0,
  `high_count`       INT          NOT NULL DEFAULT 0,
  `low_count`        INT          NOT NULL DEFAULT 0,
  `sla_breaches`     INT          NOT NULL DEFAULT 0,
                                   -- Tickets that exceeded response_time_hrs
  `compliance_pct`   DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                                   -- resolved_tickets / total_tickets * 100
  `avg_response_hrs` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `hours_used`       INT          NOT NULL DEFAULT 0,
  `site_visits_used` INT          NOT NULL DEFAULT 0,
  `generated_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_report_per_month` (`client_id`, `month`, `year`),
                                   -- One report per client per month only

  CONSTRAINT `fk_reports_client`
    FOREIGN KEY (`client_id`)    REFERENCES `clients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reports_by`
    FOREIGN KEY (`generated_by`) REFERENCES `users`(`id`)   ON DELETE CASCADE

) ENGINE=InnoDB;


-- ================================================================
-- TABLE 8: leads
-- ================================================================
-- Stores "Request Access" form submissions from index.php.
-- No login required to submit — this is a public endpoint.
-- Admin reviews leads in pages/leads.php and approves or rejects.
--
-- Approved leads → Admin manually creates a client account
-- using pages/user_form.php and assigns an SLA contract.
--
-- status flow: pending → approved OR rejected
-- ================================================================

CREATE TABLE `leads` (
  `id`             INT          NOT NULL AUTO_INCREMENT,
  `company_name`   VARCHAR(150) NOT NULL,
  `contact_person` VARCHAR(100) NOT NULL,
  `email`          VARCHAR(150) NOT NULL,
  `phone`          VARCHAR(20)  NULL,
  `preferred_plan` VARCHAR(50)  NULL,
                                -- Which SLA plan the visitor selected on the form
  `message`        TEXT         NULL,
  `status`         ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',

  `reviewed_by`    INT          NULL DEFAULT NULL,
                                -- Which Admin reviewed and actioned this lead
  `reviewed_at`    TIMESTAMP    NULL DEFAULT NULL,
  `created_at`     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_leads_reviewed_by`
    FOREIGN KEY (`reviewed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL

) ENGINE=InnoDB;


-- ================================================================
-- SEED CREDENTIALS (from seed.sql)
-- ================================================================
-- Role    | Email                    | Password
-- --------|--------------------------|------------
-- admin   | admin@techniServe.ph     | password123
-- client  | client@acmecorp.ph       | password123
-- client  | client@bpioffice.ph      | password123
-- ================================================================


-- ================================================================
-- REPORT QUERIES FOR api/reports/generate.php
-- Written by DBA (Tito & Jake) — used by Leader (Raven)
-- ================================================================

-- Query 1: Monthly SLA compliance report per client
-- Params: ? = month (1–12), ? = year (e.g. 2026)
/*
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
JOIN clients c        ON t.client_id  = c.id
JOIN sla_contracts sc ON sc.client_id = c.id AND sc.is_active = 1
WHERE MONTH(t.created_at) = ? AND YEAR(t.created_at) = ?
GROUP BY c.id, c.company_name, sc.monthly_hours_pool,
         sc.hours_used, sc.site_visits_included, sc.site_visits_used;
*/

-- Query 2: Peak ticket submission days — for charts.js (Objective 7)
/*
SELECT
  DAYNAME(created_at) AS day_name,
  COUNT(*)            AS ticket_count
FROM tickets
GROUP BY DAYNAME(created_at)
ORDER BY FIELD(DAYNAME(created_at),
  'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
*/

-- Query 3: Average resolution time per month — for charts.js (Objective 7)
/*
SELECT
  YEAR(created_at)  AS yr,
  MONTH(created_at) AS mo,
  ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)), 2) AS avg_hrs
FROM tickets
WHERE status = 'resolved' AND resolved_at IS NOT NULL
GROUP BY YEAR(created_at), MONTH(created_at)
ORDER BY yr, mo;
*/


-- ================================================================
-- seed.sql content (copy separately into phpMyAdmin after schema)
-- ================================================================
/*
USE `techniServe`;

-- Users: 1 admin, 2 clients. Password for all = "password123"
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
  ('Raven Alamo',   'admin@techniServe.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  ('Juan dela Cruz','client@acmecorp.ph',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
  ('Maria Santos',  'client@bpioffice.ph',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

INSERT INTO `clients` (`user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`) VALUES
  (2, 'Acme Corporation',  'Makati City, Metro Manila', 'Juan dela Cruz', 'client@acmecorp.ph',  '09171234567'),
  (3, 'BPI Office Manila', 'BGC, Taguig City',          'Maria Santos',   'client@bpioffice.ph', '09281234567');

UPDATE `users` SET `client_id` = 1 WHERE `id` = 2;
UPDATE `users` SET `client_id` = 2 WHERE `id` = 3;

INSERT INTO `sla_contracts`
  (`client_id`,`monthly_hours_pool`,`hours_used`,`site_visits_included`,`site_visits_used`,`response_time_hrs`,`resolution_time_hrs`,`start_date`,`end_date`) VALUES
  (1, 20,  6, 5, 1, 4,  24, '2026-01-01', '2026-12-31'),
  (2, 40, 12, 8, 2, 2,  12, '2026-01-01', '2026-12-31');

INSERT INTO `tickets` (`client_id`,`created_by`,`subject`,`description`,`priority`,`status`,`resolved_at`) VALUES
  (1, 2, 'Network switch failure on Floor 3',   'Main switch unresponsive.',          'critical', 'resolved',    NOW()),
  (1, 2, 'Antivirus update failed on 5 PCs',    'Defender failed to update.',         'high',     'open',        NULL),
  (2, 3, 'Printer offline in HR Department',    'HP LaserJet shows offline status.',  'low',      'resolved',    NOW()),
  (2, 3, 'Email server responding slowly',      'Outlook takes 3 mins to send.',      'high',     'in_progress', NULL),
  (1, 2, 'VPN drops intermittently',            'Remote staff disconnects every 30m.','critical', 'open',        NULL);

INSERT INTO `ticket_activities` (`ticket_id`,`user_id`,`action`,`note`) VALUES
  (1, 1, 'Status changed to In Progress', 'Admin dispatched to site.'),
  (1, 1, 'Status changed to Resolved',    'Replaced faulty switch module.'),
  (3, 1, 'Status changed to Resolved',    'Reinstalled printer driver.'),
  (4, 1, 'Status changed to In Progress', 'Investigating email server logs.');

INSERT INTO `maintenance_logs`
  (`ticket_id`,`client_id`,`performed_by`,`title`,`activity_type`,`hours_spent`,`completed_at`,`status`) VALUES
  (1, 1, 1, 'Network switch replacement',    'hardware_repair', 3.00, NOW(), 'completed'),
  (3, 2, 1, 'Printer driver reinstallation', 'remote_support',  1.50, NOW(), 'completed'),
  (4, 2, 1, 'Email server performance audit','network_audit',   2.50, NULL,  'in_progress');

INSERT INTO `leads` (`company_name`,`contact_person`,`email`,`phone`,`preferred_plan`,`message`,`status`) VALUES
  ('Globe BPO Services', 'Pedro Reyes', 'it@globebpo.ph', '09391234567', 'Professional', 'Need managed IT for 3 floors.', 'pending'),
  ('SM Corporate Office','Anna Cruz',   'ict@smcorp.ph',  '09501234567', 'Enterprise',   'Looking for long-term IT partner.','approved');
*/
