-- ============================================================
-- damago_quiz – Vollständiges Aktualisierungsskript
-- ============================================================
-- Dieses Skript kann auf einer leeren ODER einer bereits
-- vorhandenen Datenbank ausgeführt werden.
--
-- Was es tut:
--   • Datenbank anlegen, falls sie noch nicht existiert
--   • Alle Tabellen anlegen, falls sie noch nicht existieren
--   • Fehlende Spalten ergänzen (ADD COLUMN IF NOT EXISTS)
--   • Veraltete Spaltennamen umbenennen (nur falls nötig)
--   • Fehlende Indizes anlegen (CREATE INDEX IF NOT EXISTS)
--   • Fehlende Fremdschlüssel setzen (geprüft via information_schema)
--   • Seed-Daten einfügen, falls noch nicht vorhanden (INSERT IGNORE)
--
-- Server: MariaDB 10.4+  |  Stand: 21.05.2026
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================
-- ABSCHNITT 0: DATENBANK
-- ============================================================

CREATE DATABASE IF NOT EXISTS `damago_quiz`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `damago_quiz`;

-- ============================================================
-- ABSCHNITT 1: TABELLEN ANLEGEN (falls noch nicht vorhanden)
-- Bereits vorhandene Tabellen werden nicht verändert.
-- ============================================================

-- Lookup-Tabelle: Rollen
CREATE TABLE IF NOT EXISTS `roles` (
  `id`           int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         varchar(30)      NOT NULL,
  `display_name` varchar(50)      NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lookup-Tabelle: Spielstatus
CREATE TABLE IF NOT EXISTS `game_statuses` (
  `id`           tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`         varchar(30)         NOT NULL,
  `display_name` varchar(50)         NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_game_statuses_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Lookup-Tabelle: Punktemodi
CREATE TABLE IF NOT EXISTS `score_modes` (
  `id`           tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`         varchar(30)         NOT NULL,
  `display_name` varchar(50)         NOT NULL,
  `description`  text                NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_score_modes_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fachbereiche (mit Selbstreferenz für Haupt-/Unterbereiche)
CREATE TABLE IF NOT EXISTS `departments` (
  `id`           int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id`    int(10) UNSIGNED DEFAULT NULL,
  `name`         varchar(80)      NOT NULL,
  `display_name` varchar(100)     NOT NULL,
  `description`  text             DEFAULT NULL,
  `is_active`    tinyint(1)       NOT NULL DEFAULT 1,
  `created_by`   int(10) UNSIGNED DEFAULT NULL,
  `created_at`   datetime         NOT NULL DEFAULT current_timestamp(),
  `updated_at`   datetime         DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by`   int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_departments_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mediendateien (zentrale Tabelle für Bilder/Uploads)
CREATE TABLE IF NOT EXISTS `media_files` (
  `id`            int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name`     varchar(255)     NOT NULL,
  `original_name` varchar(255)     NOT NULL,
  `mime_type`     varchar(100)     NOT NULL,
  `file_size`     int(10) UNSIGNED NOT NULL,
  `created_by`    int(10) UNSIGNED DEFAULT NULL,
  `created_at`    datetime         NOT NULL DEFAULT current_timestamp(),
  `updated_at`    datetime         DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by`    int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_media_files_file_name` (`file_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Benutzerkonten
CREATE TABLE IF NOT EXISTS `users` (
  `id`              int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`         int(10) UNSIGNED NOT NULL DEFAULT 1,
  `department_id`   int(10) UNSIGNED DEFAULT NULL,
  `avatar_image_id` int(10) UNSIGNED DEFAULT NULL,
  `username`        varchar(50)      NOT NULL,
  `email`           varchar(255)     NOT NULL,
  `password_hash`   varchar(255)     NOT NULL,
  `is_active`       tinyint(1)       NOT NULL DEFAULT 1,
  `created_at`      datetime         NOT NULL DEFAULT current_timestamp(),
  `updated_at`      datetime         DEFAULT NULL ON UPDATE current_timestamp(),
  `created_by`      int(10) UNSIGNED DEFAULT NULL,
  `updated_by`      int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_username` (`username`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fragenpools
CREATE TABLE IF NOT EXISTS `question_pools` (
  `id`          int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        varchar(100)     NOT NULL,
  `description` text             DEFAULT NULL,
  `created_by`  int(10) UNSIGNED NOT NULL,
  `is_active`   tinyint(1)       NOT NULL DEFAULT 1,
  `created_at`  datetime         NOT NULL DEFAULT current_timestamp(),
  `updated_at`  datetime         DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by`  int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Zuordnung Fragenpool ↔ Fachbereich (n:m)
CREATE TABLE IF NOT EXISTS `question_pool_departments` (
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `department_id`    int(10) UNSIGNED NOT NULL,
  `created_at`       datetime         NOT NULL DEFAULT current_timestamp(),
  `created_by`       int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`question_pool_id`, `department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fragen
CREATE TABLE IF NOT EXISTS `questions` (
  `id`               int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question_pool_id` int(10) UNSIGNED NOT NULL,
  `question_text`    text             NOT NULL,
  `image_id`         int(10) UNSIGNED DEFAULT NULL,
  `explanation`      text             DEFAULT NULL,
  `created_by`       int(10) UNSIGNED NOT NULL,
  `is_active`        tinyint(1)       NOT NULL DEFAULT 0,
  `created_at`       datetime         NOT NULL DEFAULT current_timestamp(),
  `updated_at`       datetime         DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by`       int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Antwortmöglichkeiten (normalisiert, je 4 pro Frage)
CREATE TABLE IF NOT EXISTS `answer_options` (
  `id`          int(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `question_id` int(10) UNSIGNED    NOT NULL,
  `sort_order`  tinyint(3) UNSIGNED NOT NULL,
  `answer_text` text                NOT NULL,
  `is_correct`  tinyint(1)          NOT NULL DEFAULT 0,
  `explanation` text                NOT NULL,
  `created_by`  int(10) UNSIGNED    DEFAULT NULL,
  `created_at`  datetime            NOT NULL DEFAULT current_timestamp(),
  `updated_at`  datetime            DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by`  int(10) UNSIGNED    DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_answer_options_question_sort_order` (`question_id`, `sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Spiele / Quizrunden
CREATE TABLE IF NOT EXISTS `games` (
  `id`                        int(10) UNSIGNED    NOT NULL AUTO_INCREMENT,
  `question_pool_id`          int(10) UNSIGNED    NOT NULL,
  `host_user_id`              int(10) UNSIGNED    DEFAULT NULL,
  `host_name`                 varchar(50)         NOT NULL,
  `join_code`                 varchar(6)          NOT NULL,
  `host_token_hash`           char(64)            NOT NULL,
  `question_count`            smallint(5) UNSIGNED NOT NULL,
  `time_limit_seconds`        smallint(5) UNSIGNED NOT NULL,
  `score_mode_id`             tinyint(3) UNSIGNED  NOT NULL,
  `status_id`                 tinyint(3) UNSIGNED  NOT NULL DEFAULT 1,
  `host_plays`                tinyint(1)           NOT NULL DEFAULT 0,
  `current_question_position` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `started_at`                datetime             DEFAULT NULL,
  `finished_at`               datetime             DEFAULT NULL,
  `created_at`                datetime             NOT NULL DEFAULT current_timestamp(),
  `created_by`                int(10) UNSIGNED     DEFAULT NULL,
  `updated_at`                datetime             DEFAULT NULL ON UPDATE current_timestamp(),
  `updated_by`                int(10) UNSIGNED     DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_games_join_code` (`join_code`),
  UNIQUE KEY `uq_games_host_token_hash` (`host_token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teilnehmer eines Spiels
CREATE TABLE IF NOT EXISTS `participants` (
  `id`                 int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_id`            int(10) UNSIGNED NOT NULL,
  `user_id`            int(10) UNSIGNED DEFAULT NULL,
  `display_name`       varchar(50)      NOT NULL,
  `avatar`             varchar(100)     DEFAULT NULL,
  `session_token_hash` char(64)         NOT NULL,
  `is_host_player`     tinyint(1)       NOT NULL DEFAULT 0,
  `is_removed`         tinyint(1)       NOT NULL DEFAULT 0,
  `joined_at`          datetime         NOT NULL DEFAULT current_timestamp(),
  `last_seen_at`       datetime         DEFAULT NULL,
  `removed_at`         datetime         DEFAULT NULL,
  `updated_at`         datetime         DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_participants_session_token_hash` (`session_token_hash`),
  UNIQUE KEY `uq_participants_game_display_name`  (`game_id`, `display_name`),
  UNIQUE KEY `uq_participants_game_user`          (`game_id`, `user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- ABSCHNITT 2: ENGINE KORRIGIEREN (nur falls nötig)
-- Betrifft: games + participants aus fehlerhafter Vorgängerversion
-- ============================================================

SET @eng = (SELECT ENGINE FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games');
SET @sql = IF(@eng IS NOT NULL AND @eng != 'InnoDB',
  'ALTER TABLE `games` ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @eng = (SELECT ENGINE FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'participants');
SET @sql = IF(@eng IS NOT NULL AND @eng != 'InnoDB',
  'ALTER TABLE `participants` ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- ============================================================
-- ABSCHNITT 3: SPALTEN UMBENENNEN (nur falls alte Namen noch vorhanden)
-- Betrifft: media_files aus fehlerhafter Vorgängerversion
-- ============================================================

-- media_files: file_type → mime_type
-- (CHANGE COLUMN behält Typ und Position bei, MariaDB 10.4 kompatibel)
SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'media_files'
              AND COLUMN_NAME  = 'file_type');
SET @sql = IF(@col > 0,
  'ALTER TABLE `media_files` CHANGE COLUMN `file_type` `mime_type` varchar(100) NOT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- media_files: uploaded_by → created_by
SET @col = (SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'media_files'
              AND COLUMN_NAME  = 'uploaded_by');
SET @sql = IF(@col > 0,
  'ALTER TABLE `media_files` CHANGE COLUMN `uploaded_by` `created_by` int(10) UNSIGNED DEFAULT NULL',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- ============================================================
-- ABSCHNITT 4: FEHLENDE SPALTEN ERGÄNZEN (ADD COLUMN IF NOT EXISTS)
-- Betrifft: media_files aus fehlerhafter Vorgängerversion
-- ============================================================

-- mime_type (falls weder file_type noch mime_type vorhanden war)
ALTER TABLE `media_files`
  ADD COLUMN IF NOT EXISTS `mime_type` varchar(100) NOT NULL DEFAULT '' AFTER `original_name`;

-- created_by (falls weder uploaded_by noch created_by vorhanden war)
ALTER TABLE `media_files`
  ADD COLUMN IF NOT EXISTS `created_by` int(10) UNSIGNED DEFAULT NULL AFTER `file_size`;

-- updated_at
ALTER TABLE `media_files`
  ADD COLUMN IF NOT EXISTS `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp() AFTER `created_at`;

-- updated_by
ALTER TABLE `media_files`
  ADD COLUMN IF NOT EXISTS `updated_by` int(10) UNSIGNED DEFAULT NULL AFTER `updated_at`;


-- ============================================================
-- ABSCHNITT 5: INDIZES ERGÄNZEN (CREATE INDEX IF NOT EXISTS)
-- Vorhandene Indizes werden übersprungen.
-- ============================================================

-- answer_options
CREATE INDEX IF NOT EXISTS `idx_answer_options_question_id`  ON `answer_options` (`question_id`);
CREATE INDEX IF NOT EXISTS `idx_answer_options_is_correct`   ON `answer_options` (`is_correct`);
CREATE INDEX IF NOT EXISTS `idx_answer_options_created_by`   ON `answer_options` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_answer_options_updated_by`   ON `answer_options` (`updated_by`);

-- departments
CREATE INDEX IF NOT EXISTS `idx_departments_parent_id`   ON `departments` (`parent_id`);
CREATE INDEX IF NOT EXISTS `idx_departments_is_active`   ON `departments` (`is_active`);
CREATE INDEX IF NOT EXISTS `idx_departments_created_by`  ON `departments` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_departments_updated_by`  ON `departments` (`updated_by`);

-- games
CREATE INDEX IF NOT EXISTS `idx_games_question_pool_id` ON `games` (`question_pool_id`);
CREATE INDEX IF NOT EXISTS `idx_games_host_user_id`     ON `games` (`host_user_id`);
CREATE INDEX IF NOT EXISTS `idx_games_score_mode_id`    ON `games` (`score_mode_id`);
CREATE INDEX IF NOT EXISTS `idx_games_status_id`        ON `games` (`status_id`);
CREATE INDEX IF NOT EXISTS `idx_games_created_by`       ON `games` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_games_updated_by`       ON `games` (`updated_by`);

-- media_files
CREATE INDEX IF NOT EXISTS `idx_media_files_created_by` ON `media_files` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_media_files_updated_by` ON `media_files` (`updated_by`);

-- participants
CREATE INDEX IF NOT EXISTS `idx_participants_game_id`    ON `participants` (`game_id`);
CREATE INDEX IF NOT EXISTS `idx_participants_user_id`    ON `participants` (`user_id`);
CREATE INDEX IF NOT EXISTS `idx_participants_is_removed` ON `participants` (`is_removed`);

-- question_pools
CREATE INDEX IF NOT EXISTS `idx_question_pools_created_by` ON `question_pools` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_question_pools_name`       ON `question_pools` (`name`);
CREATE INDEX IF NOT EXISTS `idx_question_pools_is_active`  ON `question_pools` (`is_active`);
CREATE INDEX IF NOT EXISTS `idx_question_pools_updated_by` ON `question_pools` (`updated_by`);

-- question_pool_departments
CREATE INDEX IF NOT EXISTS `idx_question_pool_departments_department_id` ON `question_pool_departments` (`department_id`);
CREATE INDEX IF NOT EXISTS `idx_qpd_created_by`                          ON `question_pool_departments` (`created_by`);

-- questions
CREATE INDEX IF NOT EXISTS `idx_questions_question_pool_id` ON `questions` (`question_pool_id`);
CREATE INDEX IF NOT EXISTS `idx_questions_created_by`       ON `questions` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_questions_is_active`        ON `questions` (`is_active`);
CREATE INDEX IF NOT EXISTS `idx_questions_image_id`         ON `questions` (`image_id`);
CREATE INDEX IF NOT EXISTS `idx_questions_updated_by`       ON `questions` (`updated_by`);

-- users
CREATE INDEX IF NOT EXISTS `idx_users_role_id`         ON `users` (`role_id`);
CREATE INDEX IF NOT EXISTS `idx_users_department_id`   ON `users` (`department_id`);
CREATE INDEX IF NOT EXISTS `idx_users_avatar_image_id` ON `users` (`avatar_image_id`);
CREATE INDEX IF NOT EXISTS `idx_users_created_by`      ON `users` (`created_by`);
CREATE INDEX IF NOT EXISTS `idx_users_updated_by`      ON `users` (`updated_by`);


-- ============================================================
-- ABSCHNITT 6: FREMDSCHLÜSSEL SETZEN (nur falls noch nicht vorhanden)
-- Jeder Constraint wird einzeln gegen information_schema geprüft.
-- ============================================================

-- --- answer_options ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'answer_options'
            AND CONSTRAINT_NAME = 'fk_answer_options_question');
SET @sql = IF(@c = 0,
  'ALTER TABLE `answer_options` ADD CONSTRAINT `fk_answer_options_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'answer_options'
            AND CONSTRAINT_NAME = 'fk_answer_options_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `answer_options` ADD CONSTRAINT `fk_answer_options_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'answer_options'
            AND CONSTRAINT_NAME = 'fk_answer_options_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `answer_options` ADD CONSTRAINT `fk_answer_options_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- departments ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments'
            AND CONSTRAINT_NAME = 'fk_departments_parent');
SET @sql = IF(@c = 0,
  'ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_parent` FOREIGN KEY (`parent_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments'
            AND CONSTRAINT_NAME = 'fk_departments_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'departments'
            AND CONSTRAINT_NAME = 'fk_departments_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `departments` ADD CONSTRAINT `fk_departments_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- games ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games'
            AND CONSTRAINT_NAME = 'fk_games_question_pool');
SET @sql = IF(@c = 0,
  'ALTER TABLE `games` ADD CONSTRAINT `fk_games_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games'
            AND CONSTRAINT_NAME = 'fk_games_host_user');
SET @sql = IF(@c = 0,
  'ALTER TABLE `games` ADD CONSTRAINT `fk_games_host_user` FOREIGN KEY (`host_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games'
            AND CONSTRAINT_NAME = 'fk_games_score_mode');
SET @sql = IF(@c = 0,
  'ALTER TABLE `games` ADD CONSTRAINT `fk_games_score_mode` FOREIGN KEY (`score_mode_id`) REFERENCES `score_modes` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games'
            AND CONSTRAINT_NAME = 'fk_games_status');
SET @sql = IF(@c = 0,
  'ALTER TABLE `games` ADD CONSTRAINT `fk_games_status` FOREIGN KEY (`status_id`) REFERENCES `game_statuses` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games'
            AND CONSTRAINT_NAME = 'fk_games_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `games` ADD CONSTRAINT `fk_games_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'games'
            AND CONSTRAINT_NAME = 'fk_games_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `games` ADD CONSTRAINT `fk_games_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- media_files ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_files'
            AND CONSTRAINT_NAME = 'fk_media_files_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `media_files` ADD CONSTRAINT `fk_media_files_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_files'
            AND CONSTRAINT_NAME = 'fk_media_files_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `media_files` ADD CONSTRAINT `fk_media_files_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Alten Constraint-Namen entfernen, falls noch vorhanden (Vorgängerversion)
SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'media_files'
            AND CONSTRAINT_NAME = 'fk_media_files_uploaded_by');
SET @sql = IF(@c > 0,
  'ALTER TABLE `media_files` DROP FOREIGN KEY `fk_media_files_uploaded_by`',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- participants ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'participants'
            AND CONSTRAINT_NAME = 'fk_participants_game');
SET @sql = IF(@c = 0,
  'ALTER TABLE `participants` ADD CONSTRAINT `fk_participants_game` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'participants'
            AND CONSTRAINT_NAME = 'fk_participants_user');
SET @sql = IF(@c = 0,
  'ALTER TABLE `participants` ADD CONSTRAINT `fk_participants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- question_pools ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'question_pools'
            AND CONSTRAINT_NAME = 'fk_question_pools_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `question_pools` ADD CONSTRAINT `fk_question_pools_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'question_pools'
            AND CONSTRAINT_NAME = 'fk_question_pools_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `question_pools` ADD CONSTRAINT `fk_question_pools_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- question_pool_departments ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'question_pool_departments'
            AND CONSTRAINT_NAME = 'fk_qpd_question_pool');
SET @sql = IF(@c = 0,
  'ALTER TABLE `question_pool_departments` ADD CONSTRAINT `fk_qpd_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON DELETE CASCADE ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'question_pool_departments'
            AND CONSTRAINT_NAME = 'fk_qpd_department');
SET @sql = IF(@c = 0,
  'ALTER TABLE `question_pool_departments` ADD CONSTRAINT `fk_qpd_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'question_pool_departments'
            AND CONSTRAINT_NAME = 'fk_qpd_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `question_pool_departments` ADD CONSTRAINT `fk_qpd_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- questions ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questions'
            AND CONSTRAINT_NAME = 'fk_questions_question_pool');
SET @sql = IF(@c = 0,
  'ALTER TABLE `questions` ADD CONSTRAINT `fk_questions_question_pool` FOREIGN KEY (`question_pool_id`) REFERENCES `question_pools` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questions'
            AND CONSTRAINT_NAME = 'fk_questions_image');
SET @sql = IF(@c = 0,
  'ALTER TABLE `questions` ADD CONSTRAINT `fk_questions_image` FOREIGN KEY (`image_id`) REFERENCES `media_files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questions'
            AND CONSTRAINT_NAME = 'fk_questions_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `questions` ADD CONSTRAINT `fk_questions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'questions'
            AND CONSTRAINT_NAME = 'fk_questions_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `questions` ADD CONSTRAINT `fk_questions_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- --- users ---

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'
            AND CONSTRAINT_NAME = 'fk_users_role');
SET @sql = IF(@c = 0,
  'ALTER TABLE `users` ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'
            AND CONSTRAINT_NAME = 'fk_users_department');
SET @sql = IF(@c = 0,
  'ALTER TABLE `users` ADD CONSTRAINT `fk_users_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'
            AND CONSTRAINT_NAME = 'fk_users_avatar_image');
SET @sql = IF(@c = 0,
  'ALTER TABLE `users` ADD CONSTRAINT `fk_users_avatar_image` FOREIGN KEY (`avatar_image_id`) REFERENCES `media_files` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'
            AND CONSTRAINT_NAME = 'fk_users_created_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `users` ADD CONSTRAINT `fk_users_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @c = (SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users'
            AND CONSTRAINT_NAME = 'fk_users_updated_by');
SET @sql = IF(@c = 0,
  'ALTER TABLE `users` ADD CONSTRAINT `fk_users_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- ============================================================
-- ABSCHNITT 7: SEED-DATEN (INSERT IGNORE – nur fehlende Zeilen)
-- Bereits vorhandene Datensätze (gleiche ID) werden übersprungen.
-- ============================================================

-- Rollen
INSERT IGNORE INTO `roles` (`id`, `name`, `display_name`) VALUES
(1, 'student',  'Schüler'),
(2, 'teacher',  'Dozent'),
(3, 'admin',    'Administrator');

-- Spielstatus
INSERT IGNORE INTO `game_statuses` (`id`, `code`, `display_name`) VALUES
(1, 'lobby',    'Lobby offen'),
(2, 'running',  'Spiel läuft'),
(3, 'reveal',   'Lösung wird angezeigt'),
(4, 'finished', 'Spiel beendet'),
(5, 'aborted',  'Spiel abgebrochen');

-- Punktemodi
INSERT IGNORE INTO `score_modes` (`id`, `code`, `display_name`, `description`) VALUES
(1, 'all_or_nothing', 'Ganz oder gar nicht',
   'Es gibt nur Punkte, wenn die Auswahl exakt richtig ist. Jede falsche oder unvollständige Auswahl ergibt 0 Punkte.'),
(2, 'partial_points',  'Teilpunkte',
   'Exakt richtige Auswahl ergibt volle Punkte. Eine teilweise richtige Auswahl ohne falsche Antwort ergibt Teilpunkte. Sobald eine falsche Antwort gewählt wurde, gibt es 0 Punkte.'),
(3, 'time_bonus',      'Zeitbonus',
   'Bei richtiger Auswahl gibt es 500 Grundpunkte plus Zeitbonus aus der verbleibenden Zeit. Falsche Antworten ergeben 0 Punkte.');

-- Fachbereiche
INSERT IGNORE INTO `departments`
  (`id`, `parent_id`, `name`, `display_name`, `description`, `is_active`, `created_by`, `created_at`) VALUES
(1, NULL, 'it',                      'IT',
   'Hauptbereich für IT-Umschulungen und IT-nahe Kurse.',               1, 1, '2026-05-19 00:00:00'),
(2, NULL, 'care',                    'Pflege',
   'Hauptbereich für Pflege-Umschulungen und pflegebezogene Kurse.',    1, 1, '2026-05-19 00:00:00'),
(3, 1,    'application_development', 'Anwendungsentwicklung',
   'Unterbereich der IT für Fachinformatiker(innen) in der Anwendungsentwicklung.', 1, 1, '2026-05-19 00:00:00'),
(4, 1,    'system_integration',      'Systemintegration',
   'Unterbereich der IT für Fachinformatiker(innen) in der Systemintegration.',     1, 1, '2026-05-19 00:00:00');

-- Benutzer (Admin-Account + Testnutzer)
INSERT IGNORE INTO `users`
  (`id`, `role_id`, `department_id`, `avatar_image_id`, `username`, `email`, `password_hash`, `is_active`, `created_at`) VALUES
(1, 3, NULL, NULL, 'admin',
   'admin@damago.learn',
   '$2y$12$zZ4IIz1ewDWWMfFi0DeQtekSd2.111XdcntHfbWGgWXAT4rEGWmre', 1, '2026-05-19 01:29:09'),
(2, 2, 3,    NULL, 'Kevin_hoeing',
   'Kevin_hoeing@damago.learn',
   '$2y$12$nRN1d0ZF9EhgWCBwBJduYeLNM8r4y69hQbhWEMDgponqT17LlF3Da', 1, '2026-05-19 01:29:09'),
(3, 1, 3,    NULL, 'Paul_Schulte',
   'Paul_Schulte@damago.learn',
   '$2y$12$.H39CvtqzhybIbH8suasXOwsqIYHQVK48aJi1vyVXtRzyUG.AQKoO', 1, '2026-05-19 01:29:09'),
(4, 1, 3,    NULL, 'Marcin_Banaszkiewicz',
   'Marcin_Banaszkiewicz@damago.learn',
   '$2y$12$sC82J9LRjI.ye9RV1yScAOShFWkRLCDfuKmmYgRdrhfTGU35lYPOq', 1, '2026-05-19 01:29:09'),
(5, 1, 3,    NULL, 'Pascal_Arndt',
   'Pascal_Arndt@damago.learn',
   '$2y$12$FH3SZiEtOqcXsb1bo6QqmeYIlhS.m0.vw449lXXRlx01k/nTaEthC', 1, '2026-05-19 01:29:09');

-- Fragenpool
INSERT IGNORE INTO `question_pools`
  (`id`, `name`, `description`, `created_by`, `is_active`, `created_at`) VALUES
(1, 'PCAP Python Grundlagen Testpool',
   'Test-Fragenpool mit zehn PCAP-Fragen aus dem Fragenkatalog. Enthält Multiple-Select-Fragen mit exakt vier Antwortmöglichkeiten und deutschen Erklärungen.',
   1, 1, '2026-05-18 23:16:07');

-- Fragenpool ↔ Fachbereich-Zuordnungen
INSERT IGNORE INTO `question_pool_departments`
  (`question_pool_id`, `department_id`, `created_at`, `created_by`) VALUES
(1, 3, '2026-05-19 00:00:00', 1),
(1, 4, '2026-05-19 00:00:00', 1);

-- Fragen (10 PCAP-Testfragen)
INSERT IGNORE INTO `questions`
  (`id`, `question_pool_id`, `question_text`, `image_id`, `explanation`, `created_by`, `is_active`, `created_at`) VALUES
(1,  1, 'Which import statement loads the entire math module so that all its functions must be accessed using the module name as a prefix?',
    NULL, 'Korrekt ist die Variante, bei der das komplette math-Modul unter dem Namen math eingebunden wird.', 1, 1, '2026-05-18 23:16:07'),
(2,  1, 'Consider the following code. Which statement about isinstance() and inheritance is incorrect?\r\n\r\nclass Animal:\r\n    pass\r\n\r\nclass Dog(Animal):\r\n    pass\r\n\r\nd = Dog()',
    NULL, 'Gesucht ist die fachlich falsche Aussage über isinstance() und Vererbung.', 1, 1, '2026-05-18 23:16:07'),
(3,  1, 'Which two statements about names starting with underscores in classes are correct?',
    NULL, 'Diese Frage prüft Python-Konventionen zu nicht öffentlichen Namen und Name Mangling.', 1, 1, '2026-05-18 23:16:07'),
(4,  1, 'Which of the following statements about combining map() and filter() are correct? (Choose three)',
    NULL, 'Diese Frage prüft die Kombination von map() und filter() sowie deren Rückgabewerte in Python 3.', 1, 1, '2026-05-18 23:16:07'),
(5,  1, 'Which two statements about instance attributes initialized in __init__ are correct?',
    NULL, 'Diese Frage prüft, wie Instanzattribute in Python über self erzeugt und gespeichert werden.', 1, 1, '2026-05-18 23:16:07'),
(6,  1, 'What value does the variable __name__ hold when a Python module is executed directly as the main program, for example python script.py?',
    NULL, 'Diese Frage prüft das Python-Standardmuster if __name__ == \"__main__\".', 1, 1, '2026-05-18 23:16:07'),
(7,  1, 'Consider the following code. Which of the following statements about this code are correct?\r\n\r\nclass Config:\r\n    debug = False\r\n\r\nclass AppConfig(Config):\r\n    debug = True\r\n\r\nac = AppConfig()\r\nprint(ac.debug)',
    NULL, 'Diese Frage prüft die Reihenfolge der Attributauflösung bei Instanzen, Klassen und Basisklassen.', 1, 1, '2026-05-18 23:16:07'),
(8,  1, 'Which two statements about instance methods are correct?',
    NULL, 'Diese Frage prüft den self-Parameter und den automatischen Methodenaufruf über eine Instanz.', 1, 1, '2026-05-18 23:16:08'),
(9,  1, 'What output does the following code produce?\r\n\r\nimport math\r\nprint(math.floor(-3.7))',
    NULL, 'Diese Frage prüft das Verhalten von math.floor() bei negativen Zahlen.', 1, 1, '2026-05-18 23:16:08'),
(10, 1, 'Consider the following code. What is printed?\r\n\r\ntry:\r\n    print(\"A\", end=\" \")\r\n    raise ValueError(\"bad\")\r\nexcept ValueError:\r\n    print(\"B\", end=\" \")\r\nfinally:\r\n    print(\"C\")',
    NULL, 'Diese Frage prüft die Ausführungsreihenfolge von try, except und finally.', 1, 1, '2026-05-18 23:16:08');

-- Antwortmöglichkeiten (40 Antworten, je 4 pro Frage)
INSERT IGNORE INTO `answer_options`
  (`id`, `question_id`, `sort_order`, `answer_text`, `is_correct`, `explanation`, `created_by`, `created_at`) VALUES
-- Frage 1
(1,  1, 1, 'import math', 1,
   'Richtig. import math bindet das Modulobjekt unter dem Namen math. Alle Zugriffe müssen qualifiziert erfolgen, zum Beispiel math.sqrt() oder math.ceil().',
   1, '2026-05-18 23:16:07'),
(2,  1, 2, 'from math import sqrt', 0,
   'Falsch. Diese Anweisung importiert nur den Namen sqrt direkt. Sie lädt nicht den gesamten Modul-Namespace und erfordert für sqrt keinen Zugriff über math.',
   1, '2026-05-18 23:16:07'),
(3,  1, 3, 'from math import *', 0,
   'Falsch. Diese Anweisung importiert alle öffentlichen Namen direkt in den aktuellen Namespace. Danach ist kein Modul-Präfix wie math erforderlich.',
   1, '2026-05-18 23:16:07'),
(4,  1, 4, 'import math as m', 0,
   'Falsch. Diese Anweisung importiert das ganze Modul, aber unter dem Alias m. Die Frage verlangt ausdrücklich den Zugriff über den Modulnamen math.',
   1, '2026-05-18 23:16:07'),
-- Frage 2
(5,  2, 1, 'isinstance(d, Dog) returns True.', 0,
   'Falsch als Auswahl. Diese Aussage ist korrekt, weil d direkt aus der Klasse Dog erzeugt wurde.',
   1, '2026-05-18 23:16:07'),
(6,  2, 2, 'isinstance(d, Animal) returns True.', 0,
   'Falsch als Auswahl. Diese Aussage ist korrekt, weil Dog von Animal erbt und d deshalb auch als Animal-Instanz gilt.',
   1, '2026-05-18 23:16:07'),
(7,  2, 3, 'isinstance(d, object) returns False.', 1,
   'Richtig als Auswahl. Diese Aussage ist fachlich falsch, weil normale Python-Objekte direkt oder indirekt Instanzen von object sind.',
   1, '2026-05-18 23:16:07'),
(8,  2, 4, 'isinstance(Animal(), Dog) returns False.', 0,
   'Falsch als Auswahl. Diese Aussage ist korrekt, weil ein allgemeines Animal-Objekt kein Dog-Objekt ist.',
   1, '2026-05-18 23:16:07'),
-- Frage 3
(9,  3, 1, 'A single leading underscore is a convention for internal or non-public members.', 1,
   'Richtig. Ein einzelner führender Unterstrich signalisiert vor allem eine Konvention für interne oder nicht öffentliche Verwendung.',
   1, '2026-05-18 23:16:07'),
(10, 3, 2, 'A double leading underscore can trigger name mangling inside a class.', 1,
   'Richtig. Doppelte führende Unterstriche können innerhalb einer Klasse Name Mangling auslösen.',
   1, '2026-05-18 23:16:07'),
(11, 3, 3, 'A single leading underscore prevents all access from outside the class.', 0,
   'Falsch. Ein einfacher führender Unterstrich verhindert den Zugriff nicht technisch. Er ist hauptsächlich eine Konvention.',
   1, '2026-05-18 23:16:07'),
(12, 3, 4, 'A double leading underscore makes an attribute impossible to access under any name.', 0,
   'Falsch. Auch ein durch Name Mangling veränderter Name kann über den gemangelten Namen weiterhin erreicht werden.',
   1, '2026-05-18 23:16:07'),
-- Frage 4
(13, 4, 1, 'map() after filter() transforms only the filtered elements.', 1,
   'Richtig. Wenn map() auf das Ergebnis von filter() angewendet wird, werden nur die von filter() durchgelassenen Elemente transformiert.',
   1, '2026-05-18 23:16:07'),
(14, 4, 2, 'map() and filter() always return lists in Python 3.', 0,
   'Falsch. In Python 3 geben map() und filter() Iterator-Objekte zurück. Für eine Liste ist eine explizite Umwandlung mit list() nötig.',
   1, '2026-05-18 23:16:07'),
(15, 4, 3, 'list(map(lambda x: x**2, filter(lambda x: x % 2 == 0, [1,2,3,4,5,6]))) returns [4, 16, 36].', 1,
   'Richtig. Zuerst filtert filter() die geraden Zahlen 2, 4 und 6. Danach quadriert map() diese Werte zu 4, 16 und 36.',
   1, '2026-05-18 23:16:07'),
(16, 4, 4, 'filter() after map() filters the transformed results.', 1,
   'Richtig. Wenn filter() nach map() verwendet wird, wird die Filterbedingung auf die bereits transformierten Werte angewendet.',
   1, '2026-05-18 23:16:07'),
-- Frage 5
(17, 5, 1, 'Assigning to self.name creates or updates an attribute on the current instance.', 1,
   'Richtig. Eine Zuweisung über self speichert oder aktualisiert ein Attribut auf der konkreten Instanz.',
   1, '2026-05-18 23:16:07'),
(18, 5, 2, 'Attributes assigned through self usually appear in the instance __dict__.', 1,
   'Richtig. Normale Instanzattribute werden üblicherweise im __dict__ der Instanz abgelegt.',
   1, '2026-05-18 23:16:07'),
(19, 5, 3, 'Instance attributes must be declared in the class body before __init__ can assign them.', 0,
   'Falsch. Python verlangt keine vorherige Deklaration von Instanzattributen im Klassenrumpf.',
   1, '2026-05-18 23:16:07'),
(20, 5, 4, 'The first parameter of __init__ is the class object itself.', 0,
   'Falsch. Bei einer Instanzmethode bezeichnet der erste Parameter die neue Instanz, üblicherweise self, nicht das Klassenobjekt.',
   1, '2026-05-18 23:16:07'),
-- Frage 6
(21, 6, 1, 'None', 0,
   'Falsch. __name__ ist nicht None, sondern enthält immer einen String.',
   1, '2026-05-18 23:16:07'),
(22, 6, 2, '"__main__"', 1,
   'Richtig. Wird ein Modul direkt ausgeführt, setzt Python __name__ auf den String "__main__".',
   1, '2026-05-18 23:16:07'),
(23, 6, 3, 'The absolute path of the file', 0,
   'Falsch. Der absolute Pfad ist über __file__ verfügbar, nicht über __name__.',
   1, '2026-05-18 23:16:07'),
(24, 6, 4, 'The filename without the .py extension', 0,
   'Falsch. Bei direkter Ausführung wird nicht der Dateiname ohne Erweiterung verwendet, sondern der spezielle String "__main__".',
   1, '2026-05-18 23:16:07'),
-- Frage 7
(25, 7, 1, 'The attribute lookup searches the instance first, then the class, then base classes.', 1,
   'Richtig. Python sucht Attribute zuerst in der Instanz, dann in der Klasse der Instanz und danach in den Basisklassen.',
   1, '2026-05-18 23:16:07'),
(26, 7, 2, 'AttributeError is raised because class variables cannot be overridden in subclasses.', 0,
   'Falsch. Klassenvariablen können in Subklassen überschrieben werden. Das ist ein normales Merkmal von Vererbung.',
   1, '2026-05-18 23:16:07'),
(27, 7, 3, 'The output is False because instances always read the base class variable.', 0,
   'Falsch. Der Lookup findet AppConfig.debug vor Config.debug, weil AppConfig die Klassenvariable überschreibt.',
   1, '2026-05-18 23:16:07'),
(28, 7, 4, 'The output is True because the class variable overridden in the subclass takes precedence.', 1,
   'Richtig. AppConfig.debug hat den Wert True und wird vor Config.debug gefunden.',
   1, '2026-05-18 23:16:07'),
-- Frage 8
(29, 8, 1, 'An instance method normally declares self as its first parameter.', 1,
   'Richtig. self verweist beim Methodenaufruf auf die aktuelle Instanz.',
   1, '2026-05-18 23:16:08'),
(30, 8, 2, 'The name self is a reserved keyword in Python.', 0,
   'Falsch. self ist eine starke Konvention, aber kein reserviertes Schlüsselwort in Python.',
   1, '2026-05-18 23:16:08'),
(31, 8, 3, 'Calling an instance method through an object supplies the object as the first argument.', 1,
   'Richtig. Beim Aufruf obj.method() wird die Instanz automatisch als erstes Argument an die Methode übergeben.',
   1, '2026-05-18 23:16:08'),
(32, 8, 4, 'Instance methods can be defined only outside a class body.', 0,
   'Falsch. Instanzmethoden werden normalerweise innerhalb der Klassendefinition definiert.',
   1, '2026-05-18 23:16:08'),
-- Frage 9
(33, 9, 1, '-3.0', 0,
   'Falsch. floor() gibt in Python 3 einen int zurück und rundet nicht zu -3.0.',
   1, '2026-05-18 23:16:08'),
(34, 9, 2, '3', 0,
   'Falsch. Diese Antwort ignoriert das Vorzeichen. floor() arbeitet auf der reellen Zahlengeraden.',
   1, '2026-05-18 23:16:08'),
(35, 9, 3, '-4', 1,
   'Richtig. math.floor(-3.7) liefert die größte ganze Zahl, die kleiner oder gleich -3.7 ist. Das ist -4.',
   1, '2026-05-18 23:16:08'),
(36, 9, 4, '-3', 0,
   'Falsch. -3 wäre das Ergebnis von math.ceil(-3.7), nicht von floor().',
   1, '2026-05-18 23:16:08'),
-- Frage 10
(37, 10, 1, 'A B C', 1,
   'Richtig. Der try-Block gibt zuerst A aus, die ValueError wird im except-Block behandelt und finally läuft immer am Ende.',
   1, '2026-05-18 23:16:08'),
(38, 10, 2, 'A C', 0,
   'Falsch. Der except-Block wird ausgeführt, daher fehlt B nicht in der Ausgabe.',
   1, '2026-05-18 23:16:08'),
(39, 10, 3, 'B C', 0,
   'Falsch. A wird bereits vor dem Auslösen der Exception ausgegeben.',
   1, '2026-05-18 23:16:08'),
(40, 10, 4, 'A ValueError C', 0,
   'Falsch. Die ValueError wird abgefangen und erscheint deshalb nicht als ungefangener Fehler in der Ausgabe.',
   1, '2026-05-18 23:16:08');


-- ============================================================
-- ABSCHNITT 8: ABSCHLUSS
-- ============================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

-- ============================================================
-- Ende des Aktualisierungsskripts
-- ============================================================
