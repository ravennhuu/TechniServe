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