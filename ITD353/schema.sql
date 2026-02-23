-- ============================================================
-- Community Issue Reporter – Database Schema
-- MySQL 8.x  |  ENGINE=InnoDB, utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS `community_issues`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `community_issues`;

-- -------------------------------------------------------
-- 1. users
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(100)     NOT NULL,
  `email`        VARCHAR(255)     NOT NULL,
  `password`     VARCHAR(255)     NOT NULL,
  `phone`        VARCHAR(20)               DEFAULT NULL,
  `role`         ENUM('user','admin')      NOT NULL DEFAULT 'user',
  `is_banned`    TINYINT(1)                NOT NULL DEFAULT 0,
  `avatar`       VARCHAR(255)              DEFAULT NULL,
  `reset_token`  VARCHAR(64)               DEFAULT NULL,
  `reset_expires` DATETIME                 DEFAULT NULL,
  `created_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 2. categories
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(80)   NOT NULL,
  `slug`        VARCHAR(80)   NOT NULL,
  `icon`        VARCHAR(50)   DEFAULT '📌',
  `color`       VARCHAR(20)   DEFAULT '#3b82f6',
  `sort_order`  INT           NOT NULL DEFAULT 0,
  `is_active`   TINYINT(1)    NOT NULL DEFAULT 1,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 3. issues
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `issues` (
  `id`            INT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `ticket_id`     VARCHAR(12)    NOT NULL,          -- e.g. ISS-000042
  `user_id`       INT UNSIGNED   NOT NULL,
  `category_id`   INT UNSIGNED   NOT NULL,
  `title`         VARCHAR(200)   NOT NULL,
  `description`   TEXT           NOT NULL,
  `urgency`       ENUM('low','medium','high')   NOT NULL DEFAULT 'medium',
  `status`        ENUM('new','reviewing','in_progress','resolved','rejected')
                                 NOT NULL DEFAULT 'new',
  `location_text` VARCHAR(300)   DEFAULT NULL,
  `latitude`      DECIMAL(10,7)  DEFAULT NULL,
  `longitude`     DECIMAL(10,7)  DEFAULT NULL,
  `admin_note`    TEXT           DEFAULT NULL,
  `is_pinned`     TINYINT(1)     NOT NULL DEFAULT 0,
  `vote_count`    INT UNSIGNED   NOT NULL DEFAULT 0,  -- denormalised cache
  `created_at`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ticket` (`ticket_id`),
  KEY `idx_user`     (`user_id`),
  KEY `idx_category` (`category_id`),
  KEY `idx_status`   (`status`),
  KEY `idx_urgency`  (`urgency`),
  KEY `idx_pinned`   (`is_pinned`),
  KEY `idx_created`  (`created_at`),
  CONSTRAINT `fk_issue_user`     FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)      ON DELETE CASCADE,
  CONSTRAINT `fk_issue_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 4. issue_images  (1–3 รูปต่อ issue)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `issue_images` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `issue_id`   INT UNSIGNED  NOT NULL,
  `filename`   VARCHAR(200)  NOT NULL,
  `sort_order` TINYINT       NOT NULL DEFAULT 0,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_issue` (`issue_id`),
  CONSTRAINT `fk_img_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 5. issue_status_logs  (timeline)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `issue_status_logs` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `issue_id`   INT UNSIGNED  NOT NULL,
  `changed_by` INT UNSIGNED  DEFAULT NULL,          -- NULL = system
  `old_status` VARCHAR(20)   DEFAULT NULL,
  `new_status` VARCHAR(20)   NOT NULL,
  `note`       TEXT          DEFAULT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_log_issue` (`issue_id`),
  CONSTRAINT `fk_log_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 6. votes  (1 user / 1 issue)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `votes` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `issue_id`   INT UNSIGNED  NOT NULL,
  `user_id`    INT UNSIGNED  NOT NULL,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vote` (`issue_id`, `user_id`),
  KEY `idx_vote_user` (`user_id`),
  CONSTRAINT `fk_vote_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_vote_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 7. comments
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `comments` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `issue_id`   INT UNSIGNED  NOT NULL,
  `user_id`    INT UNSIGNED  NOT NULL,
  `body`       TEXT          NOT NULL,
  `is_pinned`  TINYINT(1)    NOT NULL DEFAULT 0,
  `created_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comment_issue` (`issue_id`),
  KEY `idx_comment_user`  (`user_id`),
  CONSTRAINT `fk_comment_issue` FOREIGN KEY (`issue_id`) REFERENCES `issues`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_comment_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------
-- 8. rate_limit  (simple IP-based spam guard)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rate_limit` (
  `id`         INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `ip`         VARCHAR(45)   NOT NULL,
  `action`     VARCHAR(40)   NOT NULL DEFAULT 'submit_issue',
  `hit_count`  INT           NOT NULL DEFAULT 1,
  `window_start` DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ip_action` (`ip`, `action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed data
-- ============================================================

-- สร้าง admin (password = Admin@1234)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
('ผู้ดูแลระบบ', 'admin@community.local',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrm3szjS', -- bcrypt of 'Admin@1234'
 'admin');

-- หมวดหมู่เริ่มต้น
INSERT INTO `categories` (`name`, `slug`, `icon`, `color`, `sort_order`) VALUES
('ถนน / ทางเท้า',     'road',        '🛣️',  '#f59e0b', 1),
('ไฟฟ้า / แสงสว่าง', 'electricity', '💡',  '#eab308', 2),
('น้ำประปา',          'water',       '🚰',  '#3b82f6', 3),
('ขยะ / สุขาภิบาล',  'waste',       '🗑️',  '#84cc16', 4),
('ความปลอดภัย',       'safety',      '🛡️',  '#ef4444', 5),
('เสียงรบกวน',        'noise',       '🔊',  '#a855f7', 6),
('น้ำท่วม / ระบายน้ำ','flood',       '🌊',  '#06b6d4', 7),
('อื่นๆ',             'other',       '📌',  '#6b7280', 8);

-- ตัวอย่างปัญหา (user_id=1=admin สำหรับ demo)
INSERT INTO `issues`
  (`ticket_id`,`user_id`,`category_id`,`title`,`description`,`urgency`,`status`,`location_text`,`vote_count`)
VALUES
('ISS-000001', 1, 1, 'ถนนซอย 5 มีหลุมขนาดใหญ่',
 'ถนนหน้าบ้านเลขที่ 42 มีหลุมลึกประมาณ 30 ซม. รถผ่านบ่อยอาจเกิดอุบัติเหตุ',
 'high', 'reviewing', 'ซอย 5 หมู่ 3 ตำบลตัวอย่าง', 12),
('ISS-000002', 1, 2, 'ไฟถนนดับตลอดแนว',
 'ไฟส่องสว่างตั้งแต่ปากทางเข้าหมู่บ้านถึงสวนสาธารณะดับทั้งหมด 8 ดวง',
 'medium', 'new', 'ถนนใหญ่หน้าหมู่บ้าน', 5),
('ISS-000003', 1, 4, 'ถุงขยะล้นทุกวันจันทร์-พุธ',
 'รถเก็บขยะไม่มารับตามกำหนด ส่งกลิ่นเหม็นและดึงดูดสัตว์',
 'medium', 'new', 'บริเวณถังขยะหน้าตลาด', 8);

-- กำหนด images ตัวอย่าง (ไม่มีไฟล์จริง – demo only)
INSERT INTO `issue_status_logs` (`issue_id`,`changed_by`,`old_status`,`new_status`,`note`) VALUES
(1, 1, 'new', 'reviewing', 'รับเรื่องแล้ว อยู่ระหว่างประสานเทศบาล');
