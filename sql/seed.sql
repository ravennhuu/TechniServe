-- seed.sql — initial sample data for development
USE `techniServe`;

-- ================================================================
-- 1. ADMIN USER
-- ================================================================
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`)
VALUES ('System Administrator', 'admin@techniServe.ph', '$2y$10$eVtVXOLrThu5VSAo9oTS0ui6EjxfU9qswhaeqyOy8jCaYmDE4mMBW', 'admin');

-- ================================================================
-- 2. CLIENT: ACME CORP
-- ================================================================
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`)
VALUES ('Alice Acme', 'client@acmecorp.ph', '$2y$10$eVtVXOLrThu5VSAo9oTS0ui6EjxfU9qswhaeqyOy8jCaYmDE4mMBW', 'client');

SET @user_id_acme = LAST_INSERT_ID();

INSERT INTO `clients` (`user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`)
VALUES (@user_id_acme, 'Acme Corp', '123 Industrial Way, Manila', 'Alice Acme', 'client@acmecorp.ph', '0917-123-4567');

SET @client_id_acme = LAST_INSERT_ID();

UPDATE `users` SET `client_id` = @client_id_acme WHERE `id` = @user_id_acme;

-- ================================================================
-- 3. CLIENT: BPI OFFICE
-- ================================================================
INSERT INTO `users` (`name`, `email`, `password_hash`, `role`)
VALUES ('Bob BPI', 'client@bpioffice.ph', '$2y$10$eVtVXOLrThu5VSAo9oTS0ui6EjxfU9qswhaeqyOy8jCaYmDE4mMBW', 'client');

SET @user_id_bpi = LAST_INSERT_ID();

INSERT INTO `clients` (`user_id`, `company_name`, `address`, `contact_person`, `contact_email`, `contact_phone`)
VALUES (@user_id_bpi, 'BPI Office', '456 Financial Plaza, Makati', 'Bob BPI', 'client@bpioffice.ph', '0918-765-4321');

SET @client_id_bpi = LAST_INSERT_ID();

UPDATE `users` SET `client_id` = @client_id_bpi WHERE `id` = @user_id_bpi;