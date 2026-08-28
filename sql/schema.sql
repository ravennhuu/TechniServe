-- ================================================================
-- TechniServe: IT Managed Services & SLA Portal
-- ================================================================
-- DBA:     Pontañeles, Tito III P. & Sapida, Jake Andrei A.

CREATE DATABASE IF NOT EXISTS `techniServe`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `techniServe`;

CREATE TABLE `users` (
  `id`            INT          NOT NULL AUTO_INCREMENT,

  `name`          VARCHAR(100) NOT NULL,
  `email`         VARCHAR(150) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,

  `role`          ENUM('admin','client') NOT NULL,
  `is_active`     TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB;

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
  UNIQUE KEY `uq_client_user` (`user_id`),

  CONSTRAINT `fk_clients_user`
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
    ON DELETE CASCADE

) ENGINE=InnoDB;

CREATE TABLE `sla_contracts` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `client_id`            INT          NOT NULL,

  `monthly_hours_pool`   INT          NOT NULL DEFAULT 20,

  `site_visits_included` INT          NOT NULL DEFAULT 5,

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

CREATE TABLE `tickets` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `client_id`   INT          NOT NULL,

  `created_by`  INT          NOT NULL,

  `subject`     VARCHAR(200) NOT NULL,
  `description` TEXT         NOT NULL,

  `priority`    ENUM('low','high','critical') NOT NULL DEFAULT 'low',

  `status`      ENUM('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',

  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  `resolved_at` TIMESTAMP    NULL DEFAULT NULL,

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_tickets_client`
    FOREIGN KEY (`client_id`)  REFERENCES `clients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tickets_created_by`
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)   ON DELETE CASCADE

) ENGINE=InnoDB;

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

CREATE TABLE `maintenance_logs` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `ticket_id`     INT          NOT NULL,
  `performed_by`  INT          NOT NULL,
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

  `scheduled_at`  TIMESTAMP    NULL DEFAULT NULL,
  `completed_at`  TIMESTAMP    NULL DEFAULT NULL,
  `status`        ENUM('scheduled','in_progress','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  CONSTRAINT `fk_maint_ticket`
    FOREIGN KEY (`ticket_id`)    REFERENCES `tickets`(`id`)  ON DELETE CASCADE,
  CONSTRAINT `fk_maint_performed_by`
    FOREIGN KEY (`performed_by`) REFERENCES `users`(`id`)    ON DELETE CASCADE

) ENGINE=InnoDB;

CREATE TABLE `reports` (
  `id`           INT       NOT NULL AUTO_INCREMENT,
  `client_id`    INT       NOT NULL,
  `generated_by` INT       NOT NULL,
  `month`        TINYINT   NOT NULL,
  `year`         SMALLINT  NOT NULL,
  `generated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_report_per_month` (`client_id`, `month`, `year`),

  CONSTRAINT `fk_reports_client`
    FOREIGN KEY (`client_id`)    REFERENCES `clients`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reports_by`
    FOREIGN KEY (`generated_by`) REFERENCES `users`(`id`)   ON DELETE CASCADE

) ENGINE=InnoDB;

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

SELECT
  t.client_id,
  sc.id                                           AS contract_id,
  sc.monthly_hours_pool,
  COALESCE(SUM(ml.hours_spent), 0)               AS hours_used,
  sc.monthly_hours_pool
    - COALESCE(SUM(ml.hours_spent), 0)           AS hours_remaining,
  sc.site_visits_included,
  COUNT(CASE WHEN ml.activity_type = 'site_visit'
             AND ml.status = 'completed'
             THEN 1 END)                          AS site_visits_used
FROM `sla_contracts` sc
JOIN `clients`       c  ON c.id         = sc.client_id
JOIN `tickets`       t  ON t.client_id  = c.id
LEFT JOIN `maintenance_logs` ml ON ml.ticket_id = t.id
WHERE sc.is_active = 1
GROUP BY t.client_id, sc.id, sc.monthly_hours_pool, sc.site_visits_included;

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
  SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS resolved_tickets,
  SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS closed_tickets,
  SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END)         AS critical_count,
  SUM(CASE WHEN t.priority = 'high'     THEN 1 ELSE 0 END)         AS high_count,
  SUM(CASE WHEN t.priority = 'low'      THEN 1 ELSE 0 END)         AS low_count,

  SUM(CASE
    WHEN t.status IN ('resolved', 'closed')
     AND TIMESTAMPDIFF(SECOND, t.created_at, t.resolved_at) > sc.response_time_hrs * 3600
    THEN 1 ELSE 0
  END)                                                              AS sla_breaches,

  ROUND(
    SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END)
    / NULLIF(COUNT(t.id), 0) * 100, 2
  )                                                                 AS compliance_pct,

  ROUND(
    AVG(TIMESTAMPDIFF(SECOND, t.created_at, t.resolved_at)) / 3600, 2
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
  ('System Admin',   'admin@techniServe.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
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


-- ================================================================
-- OPTION A FIX: Run this in phpMyAdmin to fix the Reports Page
-- ================================================================
-- Step 1: Backfill resolved_at for tickets that were closed
--         without a recorded resolution timestamp.
-- Step 2: Recreate v_monthly_report to count both 'resolved'
--         AND 'closed' tickets properly.
-- ================================================================

USE `techniServe`;

-- STEP 1: Fix missing resolved_at for already-closed/resolved tickets
UPDATE `tickets`
SET    `resolved_at` = `updated_at`
WHERE  `status` IN ('resolved', 'closed')
  AND  `resolved_at` IS NULL;

-- STEP 2: Recreate the monthly report view
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
  SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS resolved_tickets,
  SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS closed_tickets,
  SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END)         AS critical_count,
  SUM(CASE WHEN t.priority = 'high'     THEN 1 ELSE 0 END)         AS high_count,
  SUM(CASE WHEN t.priority = 'low'      THEN 1 ELSE 0 END)         AS low_count,

  SUM(CASE
    WHEN t.status IN ('resolved', 'closed')
     AND TIMESTAMPDIFF(SECOND, t.created_at, t.resolved_at) > sc.response_time_hrs * 3600
    THEN 1 ELSE 0
  END)                                                              AS sla_breaches,

  ROUND(
    SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END)
    / NULLIF(COUNT(t.id), 0) * 100, 2
  )                                                                 AS compliance_pct,

  ROUND(
    AVG(TIMESTAMPDIFF(SECOND, t.created_at, t.resolved_at)) / 3600, 2
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