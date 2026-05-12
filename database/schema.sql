-- Schema MySQL per dashboard insegnante/formatore
-- Charset consigliato: utf8mb4

SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `teacher_dashboard` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `teacher_dashboard`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `events`;
DROP TABLE IF EXISTS `lessons`;
DROP TABLE IF EXISTS `subjects`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(190) NOT NULL,
  `password_hash` VARCHAR(255) DEFAULT NULL COMMENT 'NULL per demo; in produzione obbligatorio',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `subjects` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `color` VARCHAR(7) NOT NULL DEFAULT '#0ea5e9',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_subjects_user` (`user_id`),
  CONSTRAINT `fk_subjects_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lessons` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `subject_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `lesson_date` DATE NOT NULL,
  `lesson_time` TIME DEFAULT NULL,
  `notes` TEXT,
  `status` ENUM('scheduled','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_lessons_user_date` (`user_id`, `lesson_date`),
  KEY `idx_lessons_subject` (`subject_id`),
  CONSTRAINT `fk_lessons_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lessons_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `events` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `start_datetime` DATETIME NOT NULL,
  `end_datetime` DATETIME DEFAULT NULL,
  `category` VARCHAR(50) NOT NULL DEFAULT 'Eventi',
  `priority` ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `reminder_datetime` DATETIME DEFAULT NULL,
  `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_events_user_start` (`user_id`, `start_datetime`),
  KEY `idx_events_category` (`category`),
  CONSTRAINT `fk_events_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT,
  `due_date` DATE DEFAULT NULL,
  `priority` ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `is_completed` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tasks_user_due` (`user_id`, `due_date`, `is_completed`),
  CONSTRAINT `fk_tasks_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Utente demo (password: demo123 — bcrypt)
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`) VALUES
(1, 'Maria Rossi', 'demo@eduflow.local', '$2y$12$6fFTmc2/OntJrvP21m03UOia4KEv7AaozMh21RZ8Qo/uyhiGgaFIa');

INSERT INTO `subjects` (`user_id`, `name`, `description`, `color`) VALUES
(1, 'Informatica', 'Moduli base e avanzati', '#0ea5e9'),
(1, 'Matematica', 'Analisi e geometria', '#8b5cf6');

INSERT INTO `lessons` (`user_id`, `subject_id`, `title`, `lesson_date`, `lesson_time`, `notes`, `status`) VALUES
(1, 1, 'Introduzione PHP', CURDATE() + INTERVAL 1 DAY, '09:00:00', 'Portare esercizi', 'scheduled'),
(1, 2, 'Ripasso derivate', CURDATE() + INTERVAL 3 DAY, '14:30:00', NULL, 'scheduled');

INSERT INTO `events` (`user_id`, `title`, `description`, `start_datetime`, `end_datetime`, `category`, `priority`, `reminder_datetime`, `is_completed`) VALUES
(1, 'Riunione dipartimento', 'Aula magna', CONCAT(CURDATE(), ' 10:00:00'), CONCAT(CURDATE(), ' 11:30:00'), 'Eventi', 'high', NULL, 0),
(1, 'Visita pediatra', 'Portare cartella', CONCAT(CURDATE() + INTERVAL 5 DAY, ' 16:00:00'), NULL, 'Visite mediche', 'medium', NULL, 0);

INSERT INTO `tasks` (`user_id`, `title`, `description`, `due_date`, `priority`, `is_completed`) VALUES
(1, 'Correggere compiti 3A', 'Consegna entro venerdì', CURDATE(), 'urgent', 0),
(1, 'Preparare slide modulo 4', NULL, CURDATE() + INTERVAL 2 DAY, 'medium', 0);
