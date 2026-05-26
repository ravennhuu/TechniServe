-- ================================================================
-- UPDATED SEED DATA (3NF-compliant, no client_id in users INSERT)
-- ================================================================
/*
USE `techniServe`;

-- Users: 2 clients and 1 admin, Password = "password123"
-- Hash: $2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES
  ('System Admin',   'admin@techniServe.ph', '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'admin'),
  ('Juan dela Cruz', 'client@acmecorp.ph',   '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'client'),
  ('Maria Santos',   'client@bpioffice.ph',  '$2y$10$5BtGOAzw6Wbqw3W27wae9OD17IRUIM4wqd4IokMw8PLNM.qyklof.', 'client');

INSERT INTO `clients` (`user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`) VALUES
  (2, 'Acme Corporation',  'Makati City, Metro Manila', 'Juan dela Cruz', 'client@acmecorp.ph',  '09171234567'),
  (3, 'BPI Office Manila', 'BGC, Taguig City',          'Maria Santos',   'client@bpioffice.ph', '09281234567');

INSERT INTO `leads` (`company_name`, `contact_person`, `email`, `phone`, `preferred_plan`, `message`, `status`) VALUES
  ('Globe BPO Services', 'Pedro Reyes', 'it@globebpo.ph', '09391234567', 'Professional', 'Need managed IT for 3 floors.', 'pending'),
  ('SM Corporate Office','Anna Cruz',   'ict@smcorp.ph',  '09501234567', 'Enterprise',   'Looking for long-term IT partner.','pending');

*/