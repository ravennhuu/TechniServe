-- ================================================================
-- TechniServe — seed.sql
-- Run this AFTER schema.sql
-- All passwords are "password123"
-- Hash: $2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.
-- ================================================================

USE `techniServe`;

-- Disable foreign key checks for safe truncation and auto-increment reset
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `ticket_activities`;
TRUNCATE TABLE `maintenance_logs`;
TRUNCATE TABLE `reports`;
TRUNCATE TABLE `tickets`;
TRUNCATE TABLE `sla_contracts`;
TRUNCATE TABLE `clients`;
TRUNCATE TABLE `users`;
TRUNCATE TABLE `leads`;

SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------
-- 1. INSERT USERS
-- Role Options: 'admin', 'client'
-- ----------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_active`) VALUES
(1, 'Raven Alamo', 'admin@techniServe.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'admin', 1),
(2, 'System Admin', 'admin2@techniServe.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'admin', 1),
(3, 'Juan dela Cruz', 'client@acmecorp.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'client', 1),
(4, 'Maria Santos', 'client@bpioffice.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'client', 1),
(5, 'Pedro Reyes', 'client@globebpo.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'client', 1),
(6, 'Anna Cruz', 'client@smcorp.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'client', 1);

-- ----------------------------------------------------------------
-- 2. INSERT CLIENTS
-- Link to users via user_id
-- ----------------------------------------------------------------
INSERT INTO `clients` (`id`, `user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`) VALUES
(1, 3, 'Acme Corporation', 'Makati City, Metro Manila', 'Juan dela Cruz', 'client@acmecorp.ph', '09171234567'),
(2, 4, 'BPI Office Manila', 'BGC, Taguig City', 'Maria Santos', 'client@bpioffice.ph', '09281234567'),
(3, 5, 'Globe BPO Services', 'Pioneer St, Mandaluyong City', 'Pedro Reyes', 'client@globebpo.ph', '09391234567'),
(4, 6, 'SM Corporate Office', 'Pasay City, Metro Manila', 'Anna Cruz', 'client@smcorp.ph', '09501234567');

-- ----------------------------------------------------------------
-- 3. INSERT SLA CONTRACTS
-- ----------------------------------------------------------------
INSERT INTO `sla_contracts` (`id`, `client_id`, `monthly_hours_pool`, `site_visits_included`, `response_time_hrs`, `resolution_time_hrs`, `uptime_target_pct`, `start_date`, `end_date`, `is_active`) VALUES
(1, 1, 20, 5, 4, 24, 99.50, '2026-01-01', '2026-12-31', 1),
(2, 2, 40, 8, 2, 12, 99.90, '2026-01-01', '2026-12-31', 1),
(3, 3, 30, 6, 4, 24, 99.50, '2026-02-01', '2026-12-31', 1),
(4, 4, 60, 12, 1, 6, 99.99, '2026-03-01', '2026-12-31', 1);

-- ----------------------------------------------------------------
-- 4. INSERT TICKETS
-- Cover different months (April, May, June 2026) for reporting
-- ----------------------------------------------------------------
INSERT INTO `tickets` (`id`, `client_id`, `created_by`, `subject`, `description`, `priority`, `status`, `created_at`, `resolved_at`) VALUES
-- April 2026 Tickets
(1, 1, 3, 'Network switch failure on Floor 3', 'The main switch on Floor 3 is completely unresponsive.', 'critical', 'resolved', '2026-04-10 09:00:00', '2026-04-10 11:30:00'),
(2, 1, 3, 'Antivirus update failed on 5 PCs', 'Workstations are showing outdated definition errors.', 'high', 'resolved', '2026-04-12 10:00:00', '2026-04-12 15:00:00'),
(3, 2, 4, 'Printer offline in HR Department', 'The office printer is refusing to connect to the network.', 'low', 'resolved', '2026-04-15 08:30:00', '2026-04-15 09:30:00'),
(4, 2, 4, 'Email server responding slowly', 'Users are reporting 2-3 minute delays when sending mails.', 'high', 'resolved', '2026-04-20 11:00:00', '2026-04-20 15:00:00'),
-- May 2026 Tickets
(5, 1, 3, 'VPN disconnects intermittently', 'Remote staff report losing VPN connectivity every 30 mins.', 'critical', 'resolved', '2026-05-02 09:00:00', '2026-05-02 10:30:00'),
(6, 1, 3, 'Weekly server backup check', 'Regular check of the backup restoration process.', 'low', 'resolved', '2026-05-05 13:00:00', '2026-05-05 15:00:00'),
(7, 3, 5, 'CRM deployment to new staff', 'Install CRM software on 3 new workstations in Sales.', 'low', 'resolved', '2026-05-10 10:00:00', '2026-05-10 11:30:00'),
(8, 3, 5, 'Slow network speed in Conference Room', 'Wi-Fi speeds are under 5Mbps in the main meeting room.', 'high', 'resolved', '2026-05-12 09:00:00', '2026-05-12 14:00:00'),
(9, 4, 6, 'Active Directory sync issue', 'Sync conflicts occurring with primary domain controller.', 'high', 'resolved', '2026-05-18 10:00:00', '2026-05-18 10:45:00'),
-- June 2026 Tickets (Current active month)
(10, 1, 3, 'Internet gateway failure', 'The secondary WAN link is down, causing slow failover.', 'critical', 'in_progress', '2026-06-01 09:00:00', NULL),
(11, 2, 4, 'VoIP phone line crackling', 'Finance desk phone has static noise on calls.', 'low', 'open', '2026-06-02 10:00:00', NULL),
(12, 3, 5, 'Fileserver access denied error', 'HR cannot access the archived documents folder.', 'high', 'resolved', '2026-06-03 14:00:00', '2026-06-03 15:30:00'),
(13, 4, 6, 'Patching system update', 'Applying OS security updates across server farm.', 'low', 'closed', '2026-06-04 11:00:00', '2026-06-04 11:30:00');

-- ----------------------------------------------------------------
-- 5. INSERT TICKET ACTIVITIES
-- ----------------------------------------------------------------
INSERT INTO `ticket_activities` (`id`, `ticket_id`, `user_id`, `action`, `note`, `created_at`) VALUES
(1, 1, 1, 'Status changed to In Progress', 'Admin dispatched to site.', '2026-04-10 09:15:00'),
(2, 1, 1, 'Status changed to Resolved', 'Replaced faulty switch module.', '2026-04-10 11:30:00'),
(3, 2, 2, 'Status changed to In Progress', 'Running remote script to force updates.', '2026-04-12 11:00:00'),
(4, 2, 2, 'Status changed to Resolved', 'All workstations updated successfully.', '2026-04-12 15:00:00'),
(5, 3, 1, 'Status changed to Resolved', 'Printer driver reinstalled and reconnected.', '2026-04-15 09:30:00'),
(6, 4, 1, 'Status changed to In Progress', 'Investigating Exchange server logs.', '2026-04-20 12:00:00'),
(7, 4, 1, 'Status changed to Resolved', 'Optimized database index on mail server.', '2026-04-20 15:00:00'),
(8, 5, 2, 'Status changed to Resolved', 'Reconfigured VPN timeout settings.', '2026-05-02 10:30:00'),
(9, 6, 1, 'Status changed to Resolved', 'Backup restore check completed without error.', '2026-05-05 15:00:00'),
(10, 7, 2, 'Status changed to Resolved', 'Completed installation and basic client training.', '2026-05-10 11:30:00'),
(11, 8, 1, 'Status changed to In Progress', 'Surveying wireless interference in boardroom.', '2026-05-12 10:00:00'),
(12, 8, 1, 'Status changed to Resolved', 'Relocated Access Point and changed to clear channel.', '2026-05-12 14:00:00'),
(13, 10, 1, 'Status changed to In Progress', 'In contact with ISP to resolve gateway ping drops.', '2026-06-01 10:00:00');

-- ----------------------------------------------------------------
-- 6. INSERT MAINTENANCE LOGS
-- ----------------------------------------------------------------
INSERT INTO `maintenance_logs` (`id`, `ticket_id`, `performed_by`, `title`, `description`, `activity_type`, `hours_spent`, `scheduled_at`, `completed_at`, `status`, `created_at`) VALUES
(1, 1, 1, 'Switch hardware replacement', 'Replaced faulty Cisco 24-port switch unit.', 'hardware_repair', 3.00, '2026-04-10 09:15:00', '2026-04-10 11:30:00', 'completed', '2026-04-10 09:15:00'),
(2, 2, 2, 'Antivirus remote deployment', 'Pushed windows defender update batch script.', 'remote_support', 1.50, '2026-04-12 11:00:00', '2026-04-12 15:00:00', 'completed', '2026-04-12 11:00:00'),
(3, 3, 1, 'Printer driver remediation', 'Reinstalled network print drivers on user PCs.', 'remote_support', 1.00, '2026-04-15 08:30:00', '2026-04-15 09:30:00', 'completed', '2026-04-15 08:30:00'),
(4, 4, 1, 'Exchange server performance audit', 'Emergency onsite maintenance for server PSU and diagnostics.', 'site_visit', 4.00, '2026-04-20 12:00:00', '2026-04-20 15:00:00', 'completed', '2026-04-20 12:00:00'),
(5, 5, 2, 'VPN config patch', 'Adjusted keepalive and MTU parameters.', 'patch', 1.50, '2026-05-02 09:00:00', '2026-05-02 10:30:00', 'completed', '2026-05-02 09:00:00'),
(6, 6, 1, 'Backup restore verification', 'Performed full restore drill to sandbox storage.', 'backup', 2.00, '2026-05-05 13:00:00', '2026-05-05 15:00:00', 'completed', '2026-05-05 13:00:00'),
(7, 7, 2, 'Sales CRM client install', 'Software installation and licensing on sales machines.', 'software_install', 3.00, '2026-05-10 10:00:00', '2026-05-10 11:30:00', 'completed', '2026-05-10 10:00:00'),
(8, 8, 1, 'Boardroom AP troubleshooting', 'Conducted Wi-Fi spectrum audit and relocated AP.', 'network_audit', 2.50, '2026-05-12 10:00:00', '2026-05-12 14:00:00', 'completed', '2026-05-12 10:00:00'),
(9, 9, 2, 'AD replication fix', 'Resolved sync tombstone conflicts.', 'other', 1.25, '2026-05-18 10:00:00', '2026-05-18 10:45:00', 'completed', '2026-05-18 10:00:00'),
(10, 10, 1, 'Gateway link debugging', 'Tracing routing tables and ISP connection drops.', 'network_audit', 2.00, '2026-06-01 10:00:00', NULL, 'in_progress', '2026-06-01 10:00:00');

-- ----------------------------------------------------------------
-- 7. INSERT REPORTS (Archived monthly metadata)
-- ----------------------------------------------------------------
INSERT INTO `reports` (`id`, `client_id`, `generated_by`, `month`, `year`, `generated_at`) VALUES
(1, 1, 1, 4, 2026, '2026-05-01 00:05:00'),
(2, 2, 1, 4, 2026, '2026-05-01 00:10:00'),
(3, 1, 1, 5, 2026, '2026-06-01 00:05:00'),
(4, 3, 1, 5, 2026, '2026-07-01 00:10:00'),
(5, 4, 1, 5, 2026, '2026-08-01 00:15:00');

-- ----------------------------------------------------------------
-- 8. INSERT LEADS
-- ----------------------------------------------------------------
INSERT INTO `leads` (`id`, `company_name`, `contact_person`, `email`, `phone`, `preferred_plan`, `message`, `status`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(1, 'Proweaver Inc', 'Jemuel Guerrero', 'hr@proweaver.com', '09189998888', 'Basic', 'Looking for routine backup services and workstation patch management.', 'pending', NULL, NULL, '2026-05-01 10:00:00'),
(2, 'Metro Gaisano', 'Evelyn Tan', 'ops@metrog.ph', '09176665555', 'Enterprise', 'We need 24/7 remote support monitoring for our POS terminals across 5 branches.', 'approved', 1, '2026-05-02 14:00:00', '2026-05-01 14:00:00'),
(3, 'Ayala Land', 'Carlos Zobel', 'ict@ayalaland.com.ph', '09192223333', 'Enterprise', 'Requesting custom SLA proposals for our head office infrastructure.', 'pending', NULL, NULL, '2026-05-15 11:00:00'),
(4, 'San Miguel Corp', 'Ramon Ang', 'admin@sanmiguel.com.ph', '09177777777', 'Professional', 'Interested in our network auditing capabilities.', 'rejected', 1, '2026-05-20 16:30:00', '2026-05-20 09:00:00');