-- ================================================================
-- TechniServe: IT Managed Services & SLA Portal
-- ================================================================
-- FILE:    schema_3nf.sql
-- PURPOSE: Full 3NF-compliant version of schema.sql.
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
-- 3NF CHANGES SUMMARY (4 fixes applied):
--
--   FIX 1 — users.client_id REMOVED
--     Was:  users had a client_id column pointing back to clients.
--     Why:  Redundant. The link already exists via clients.user_id.
--           Keeping both creates a circular bidirectional dependency.
--     Now:  Removed from users. Look up a user's company via:
--           SELECT * FROM clients WHERE user_id = <users.id>
--
--   FIX 2 — sla_contracts.hours_used / site_visits_used REMOVED
--     Was:  Two mutable aggregate columns updated on every maintenance log.
--     Why:  Derived data. Both are computable from maintenance_logs:
--             hours_used       = SUM(hours_spent) WHERE client_id = ?
--             site_visits_used = COUNT(*) WHERE activity_type = 'site_visit'
--           Storing them here creates a transitive dependency on
--           maintenance_logs data.
--     Now:  Removed. Use the VIEW v_sla_usage (provided below) or
--           run the aggregation query wherever the values are needed.
--
--   FIX 3 — maintenance_logs.client_id REMOVED
--     Was:  maintenance_logs stored client_id directly.
--     Why:  Redundant. client_id is already reachable via:
--           maintenance_logs.ticket_id → tickets.client_id
--           Storing it again is a transitive dependency.
--     Now:  Removed. Join to tickets when the client is needed:
--           JOIN tickets t ON t.id = maintenance_logs.ticket_id
--
--   FIX 4 — reports aggregate columns REMOVED; replaced with FK to source
--     Was:  reports stored pre-calculated totals (total_tickets,
--           resolved_tickets, compliance_pct, hours_used, etc.).
--     Why:  All values are derivable from tickets and maintenance_logs.
--           Storing them here creates transitive dependencies on live
--           ticket/maintenance data.
--     Now:  The reports table records *who* generated *which month's*
--           report and *when*. The actual figures are always computed
--           live via the v_monthly_report VIEW (provided below).
--
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
-- 3NF FIX 1: client_id column has been REMOVED.
--   The link between a user and a client company is stored once,
--   in clients.user_id. To find a user's company:
--     SELECT * FROM clients WHERE user_id = <users.id>
--
-- role = 'admin'  → The IT firm. Full access to everything.
-- role = 'client' → The corporate customer. Can only submit tickets,
--                   view their own data, and view their own reports.
--
-- IMPORTANT: Only TWO roles exist. There is no 'technician'.
--            Accounts are created by Admin only — no public signup.
-- ================================================================

CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  -- client_id REMOVED (3NF FIX 1): use clients.user_id to navigate instead.

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
--
-- 3NF NOTE: clients.user_id is the single source of truth for the
--   user ↔ company relationship. No back-reference in users needed.
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
  UNIQUE KEY `uq_client_user` (`user_id`),
                                -- Enforces one-to-one: one user per company

  CONSTRAINT `fk_clients_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE
    -- If the user account is deleted, the company profile is also deleted.

) ENGINE=InnoDB;

-- NOTE: The ALTER TABLE that added fk_users_client (users.client_id → clients.id)
-- has been removed as part of 3NF FIX 1. That back-reference no longer exists.


-- ================================================================
-- TABLE 3: sla_contracts
-- ================================================================
-- Stores the Service Level Agreement *terms* for each client.
-- Admin creates and manages these. One active contract per client.
--
-- 3NF FIX 2: hours_used and site_visits_used REMOVED.
--   These were running totals derived from maintenance_logs.
--   They are now computed on demand via the v_sla_usage VIEW below.
--
-- What remains here is purely definitional (the contract terms):
--   monthly_hours_pool   = total support hours the client gets per month
--   site_visits_included = free on-site visits per month
--   response_time_hrs    = how fast Admin must respond (SLA guarantee)
-- ================================================================

CREATE TABLE `sla_contracts` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `client_id`            INT          NOT NULL,

  `monthly_hours_pool`   INT          NOT NULL DEFAULT 20,
  -- hours_used REMOVED (3NF FIX 2): computed via v_sla_usage view.

  `site_visits_included` INT          NOT NULL DEFAULT 5,
  -- site_visits_used REMOVED (3NF FIX 2): computed via v_sla_usage view.

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
--
-- 3NF NOTE: No changes needed. All columns depend solely on tickets.id.
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
-- 3NF NOTE: No changes needed. All columns depend solely on id.
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
-- performed_by = always the Admin (only Admin can log maintenance)
--
-- 3NF FIX 3: client_id REMOVED.
--   Was redundant — client_id was already reachable via:
--     maintenance_logs.ticket_id → tickets.client_id
--   To get the client for a maintenance log:
--     JOIN tickets t ON t.id = maintenance_logs.ticket_id
--   Then use t.client_id.
-- ================================================================

CREATE TABLE `maintenance_logs` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `ticket_id`     INT          NOT NULL,
                                -- Which ticket this maintenance resolves/addresses.
                                -- The client is derived via tickets.client_id.
  -- client_id REMOVED (3NF FIX 3): reachable via ticket_id → tickets.client_id

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
                                -- Aggregated by v_sla_usage to compute hours_used per client.

  `scheduled_at`  TIMESTAMP    NULL DEFAULT NULL,
  `completed_at`  TIMESTAMP    NULL DEFAULT NULL,
  `status`        ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_maint_ticket`
    FOREIGN KEY (`ticket_id`)    REFERENCES `tickets`(`id`)  ON DELETE CASCADE,
  -- fk_maint_client REMOVED (3NF FIX 3)
  CONSTRAINT `fk_maint_performed_by`
    FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`)    ON DELETE CASCADE

) ENGINE=InnoDB;


-- ================================================================
-- TABLE 7: reports
-- ================================================================
-- Records *that* a monthly report was generated, by whom, and when.
--
-- 3NF FIX 4: All pre-calculated aggregate columns REMOVED:
--   total_tickets, resolved_tickets, critical_count, high_count,
--   low_count, sla_breaches, compliance_pct, avg_response_hrs,
--   hours_used, site_visits_used.
--
--   These were all derived from tickets and maintenance_logs, making
--   them transitive dependencies. They are now computed live via the
--   v_monthly_report VIEW (see below).
--
-- What remains: the metadata of the report generation event itself.
-- The UNIQUE KEY still enforces one report record per client per month.
-- ================================================================

CREATE TABLE `reports` (
  `id`           INT       NOT NULL AUTO_INCREMENT,
  `client_id`    INT       NOT NULL,
  `generated_by` INT       NOT NULL,
                            -- Which Admin generated this report

  `month`        TINYINT   NOT NULL,   -- 1 = January ... 12 = December
  `year`         SMALLINT  NOT NULL,   -- e.g. 2026

  -- Aggregate columns REMOVED (3NF FIX 4): use v_monthly_report VIEW instead.
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_report_per_month` (`client_id`, `month`, `year`),
                            -- One report record per client per month only

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
--
-- 3NF NOTE: No changes needed. All columns depend solely on leads.id.
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
-- VIEWS — Replaces the removed derived/aggregate columns
-- ================================================================


-- ----------------------------------------------------------------
-- VIEW: v_sla_usage
-- ----------------------------------------------------------------
-- Replaces sla_contracts.hours_used and site_visits_used (FIX 2),
-- and uses the normalized maintenance_logs (no client_id, FIX 3).
--
-- Returns per-client SLA consumption figures by joining
-- maintenance_logs → tickets to derive the client.
--
-- Usage:
--   SELECT * FROM v_sla_usage WHERE client_id = 1;
-- ----------------------------------------------------------------

CREATE OR REPLACE VIEW `v_sla_usage` AS
SELECT
  t.client_id,
  sc.id                                          AS contract_id,
  sc.monthly_hours_pool,
  COALESCE(SUM(ml.hours_spent), 0)               AS hours_used,
  sc.monthly_hours_pool
    - COALESCE(SUM(ml.hours_spent), 0)           AS hours_remaining,
  sc.site_visits_included,
  COUNT(CASE WHEN ml.activity_type = 'site_visit'
             AND ml.status = 'completed'
             THEN 1 END)                         AS site_visits_used
FROM `sla_contracts` sc
JOIN `clients`       c  ON c.id         = sc.client_id
JOIN `tickets`       t  ON t.client_id  = c.id
LEFT JOIN `maintenance_logs` ml ON ml.ticket_id = t.id
WHERE sc.is_active = 1
GROUP BY t.client_id, sc.id, sc.monthly_hours_pool, sc.site_visits_included;


-- ----------------------------------------------------------------
-- VIEW: v_monthly_report
-- ----------------------------------------------------------------
-- Replaces all aggregate columns removed from reports (FIX 4).
--
-- Computes the full monthly SLA report figures on demand,
-- joined to reports so the admin's generation record is preserved.
--
-- Usage (for a specific client + month):
--   SELECT * FROM v_monthly_report
--   WHERE client_id = 1 AND month = 4 AND year = 2026;
-- ----------------------------------------------------------------

CREATE OR REPLACE VIEW `v_monthly_report` AS
SELECT
  r.id                                                              AS report_id,
  r.client_id,
  c.company_name,
  r.month,
  r.year,
  r.generated_by,
  r.generated_at,

  COUNT(t.id)                                                       AS total_tickets,
  SUM(CASE WHEN t.status = 'resolved'   THEN 1 ELSE 0 END)         AS resolved_tickets,
  SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END)         AS critical_count,
  SUM(CASE WHEN t.priority = 'high'     THEN 1 ELSE 0 END)         AS high_count,
  SUM(CASE WHEN t.priority = 'low'      THEN 1 ELSE 0 END)         AS low_count,

  SUM(CASE
    WHEN t.status = 'resolved'
     AND TIMESTAMPDIFF(HOUR, t.created_at, t.resolved_at) > sc.response_time_hrs
    THEN 1 ELSE 0
  END)                                                              AS sla_breaches,

  ROUND(
    SUM(CASE WHEN t.status = 'resolved' THEN 1 ELSE 0 END)
    / NULLIF(COUNT(t.id), 0) * 100, 2
  )                                                                 AS compliance_pct,

  ROUND(
    AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.resolved_at)), 2
  )                                                                 AS avg_response_hrs,

  COALESCE(SUM(ml.hours_spent), 0)                                  AS hours_used,

  COUNT(CASE WHEN ml.activity_type = 'site_visit'
             AND ml.status = 'completed'
             THEN 1 END)                                            AS site_visits_used

FROM `reports` r
JOIN `clients`       c  ON c.id         = r.client_id
JOIN `tickets`       t  ON t.client_id  = c.id
                        AND MONTH(t.created_at) = r.month
                        AND YEAR(t.created_at)  = r.year
JOIN `sla_contracts` sc ON sc.client_id = c.id AND sc.is_active = 1
LEFT JOIN `maintenance_logs` ml ON ml.ticket_id = t.id

GROUP BY
  r.id, r.client_id, c.company_name, r.month, r.year,
  r.generated_by, r.generated_at, sc.response_time_hrs;


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
-- UPDATED SEED DATA (3NF-compliant, no client_id in users INSERT)
-- ================================================================
/*
USE `techniServe`;

-- Users: 1 admin, 2 clients. Password for all = "password123"
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
-- NOTE: No client_id column here — link is established via clients.user_id.

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
  ('Raven Alamo',   'admin@techniServe.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
  ('Juan dela Cruz','client@acmecorp.ph',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
  ('Maria Santos',  'client@bpioffice.ph',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client');

INSERT INTO `clients` (`user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`) VALUES
  (2, 'Acme Corporation',  'Makati City, Metro Manila', 'Juan dela Cruz', 'client@acmecorp.ph',  '09171234567'),
  (3, 'BPI Office Manila', 'BGC, Taguig City',          'Maria Santos',   'client@bpioffice.ph', '09281234567');

-- No UPDATE users SET client_id needed — that column no longer exists.

INSERT INTO `sla_contracts`
  (`client_id`,`monthly_hours_pool`,`site_visits_included`,`response_time_hrs`,`resolution_time_hrs`,`start_date`,`end_date`) VALUES
  (1, 20, 5, 4,  24, '2026-01-01', '2026-12-31'),
  (2, 40, 8, 2,  12, '2026-01-01', '2026-12-31');

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

-- NOTE: client_id removed from maintenance_logs INSERT — use ticket_id only.
INSERT INTO `maintenance_logs`
  (`ticket_id`,`performed_by`,`title`,`activity_type`,`hours_spent`,`completed_at`,`status`) VALUES
  (1, 1, 'Network switch replacement',    'hardware_repair', 3.00, NOW(), 'completed'),
  (3, 1, 'Printer driver reinstallation', 'remote_support',  1.50, NOW(), 'completed'),
  (4, 1, 'Email server performance audit','network_audit',   2.50, NULL,  'in_progress');

INSERT INTO `leads` (`company_name`,`contact_person`,`email`,`phone`,`preferred_plan`,`message`,`status`) VALUES
  ('Globe BPO Services', 'Pedro Reyes', 'it@globebpo.ph', '09391234567', 'Professional', 'Need managed IT for 3 floors.', 'pending'),
  ('SM Corporate Office','Anna Cruz',   'ict@smcorp.ph',  '09501234567', 'Enterprise',   'Looking for long-term IT partner.','approved');
*/