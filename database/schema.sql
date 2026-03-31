-- ============================================================
--  StageConnect – Schéma base de données
--  SGBD : MySQL / MariaDB
-- ============================================================

SET SQL_MODE   = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone  = "+00:00";
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `stageconnect`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `stageconnect`;

-- ── ENTREPRISES ──────────────────────────────────────────────
CREATE TABLE `companies` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150)     NOT NULL,
  `description` TEXT,
  `email`       VARCHAR(150)     NOT NULL,
  `phone`       VARCHAR(20),
  `city`        VARCHAR(100),
  `sector`      VARCHAR(100),
  `logo`        VARCHAR(255),
  `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── COMPÉTENCES ───────────────────────────────────────────────
CREATE TABLE `skills` (
  `id`    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `label` VARCHAR(80)  NOT NULL UNIQUE,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── UTILISATEURS (table centrale) ────────────────────────────
-- role : 'admin' | 'pilot' | 'student'
CREATE TABLE `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `firstname`  VARCHAR(80)  NOT NULL,
  `lastname`   VARCHAR(80)  NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('admin','pilot','student') NOT NULL DEFAULT 'student',
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── PILOTES (extension de users pour rôle pilot) ─────────────
CREATE TABLE `pilots` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL UNIQUE,
  `promotion`  VARCHAR(100),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_pilots_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── ÉTUDIANTS (extension de users pour rôle student) ─────────
CREATE TABLE `students` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`      INT UNSIGNED NOT NULL UNIQUE,
  `pilot_id`     INT UNSIGNED,
  `stage_status` ENUM('searching','applied','found','none') NOT NULL DEFAULT 'searching',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_students_user`  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_students_pilot` FOREIGN KEY (`pilot_id`) REFERENCES `pilots`(`id`)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── OFFRES DE STAGE ───────────────────────────────────────────
CREATE TABLE `offers` (
  `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `company_id`   INT UNSIGNED    NOT NULL,
  `pilot_id`     INT UNSIGNED,
  `title`        VARCHAR(200)    NOT NULL,
  `description`  TEXT            NOT NULL,
  `salary`       DECIMAL(8,2),
  `duration`     INT UNSIGNED    COMMENT 'Durée en semaines',
  `location`     VARCHAR(150),
  `offer_date`   DATE,
  `is_active`    TINYINT(1)      NOT NULL DEFAULT 1,
  `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_offers_company` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_offers_pilot`   FOREIGN KEY (`pilot_id`)   REFERENCES `pilots`(`id`)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── COMPÉTENCES ↔ OFFRES (relation N:N) ──────────────────────
CREATE TABLE `offer_skills` (
  `offer_id` INT UNSIGNED NOT NULL,
  `skill_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`offer_id`, `skill_id`),
  CONSTRAINT `fk_ofskills_offer` FOREIGN KEY (`offer_id`) REFERENCES `offers`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ofskills_skill` FOREIGN KEY (`skill_id`) REFERENCES `skills`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── CANDIDATURES ─────────────────────────────────────────────
CREATE TABLE `applications` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_id` INT UNSIGNED NOT NULL,
  `offer_id`   INT UNSIGNED NOT NULL,
  `cover_letter` TEXT,
  `cv_path`    VARCHAR(255),
  `status`     ENUM('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  `applied_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_app` (`student_id`, `offer_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_app_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_offer`   FOREIGN KEY (`offer_id`)   REFERENCES `offers`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── WISH-LIST ─────────────────────────────────────────────────
CREATE TABLE `wishlists` (
  `student_id` INT UNSIGNED NOT NULL,
  `offer_id`   INT UNSIGNED NOT NULL,
  `added_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`student_id`, `offer_id`),
  CONSTRAINT `fk_wl_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wl_offer`   FOREIGN KEY (`offer_id`)   REFERENCES `offers`(`id`)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ── ÉVALUATIONS ENTREPRISES ───────────────────────────────────
CREATE TABLE `company_reviews` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_id` INT UNSIGNED NOT NULL,
  `student_id` INT UNSIGNED NOT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
  `comment`    TEXT,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uq_review` (`company_id`, `student_id`),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_rev_company` FOREIGN KEY (`company_id`) REFERENCES `companies`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rev_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)  ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  DONNÉES DE DÉMONSTRATION
-- ============================================================

-- Compétences
INSERT INTO `skills` (`label`) VALUES
  ('PHP'),('JavaScript'),('MySQL'),('Python'),('React'),
  ('Vue.js'),('Docker'),('Linux'),('UX/UI Design'),('Figma'),
  ('Marketing Digital'),('SEO'),('Data Analysis'),('Power BI'),
  ('Machine Learning'),('Java'),('C++'),('DevOps'),('Git'),('Cybersécurité');

-- Entreprises
INSERT INTO `companies` (`name`,`description`,`email`,`phone`,`city`,`sector`) VALUES
  ('TechTalent SAS','Agence spécialisée en développement web full-stack.','contact@techtalent.fr','0123456789','Paris','Développement web'),
  ('DataLab Consulting','Cabinet conseil en data analytics et business intelligence.','rh@datalab.fr','0234567890','Lyon','Data & Analytics'),
  ('CreativXp Studio','Studio de design UX/UI et expérience utilisateur.','jobs@creativxp.fr','0345678901','Bordeaux','Design'),
  ('MarketKing Agency','Agence marketing digital et croissance.','rh@marketking.fr','0456789012','Nantes','Marketing digital'),
  ('CyberSoft','Entreprise spécialisée en cybersécurité et audit SI.','stages@cybersoft.fr','0567890123','Toulouse','Cybersécurité'),
  ('AI-Nexus Labs','Laboratoire de R&D en intelligence artificielle.','careers@ainexus.fr','0678901234','Sophia-Antipolis','Intelligence artificielle');

-- Utilisateurs (mot de passe = "password" pour tous)
-- Hash généré via password_hash('password', PASSWORD_DEFAULT)
INSERT INTO `users` (`firstname`,`lastname`,`email`,`password`,`role`) VALUES
  ('Admin','Système','admin@cesi.fr', '$2y$10$.cd.FRrb4dzmP4yzEb5GzesvmSZGAPHAAbFuPA9l/n2x1YcV9Wpzy', 'admin'),
  ('Marie','Dupont','marie.dupont@cesi.fr','$2y$10$.cd.FRrb4dzmP4yzEb5GzesvmSZGAPHAAbFuPA9l/n2x1YcV9Wpzy','pilot'),
  ('Jean','Martin','jean.martin@cesi.fr','$2y$10$.cd.FRrb4dzmP4yzEb5GzesvmSZGAPHAAbFuPA9l/n2x1YcV9Wpzy','pilot'),
  ('Alice','Bernard','alice.bernard@student.cesi.fr','$2y$10$.cd.FRrb4dzmP4yzEb5GzesvmSZGAPHAAbFuPA9l/n2x1YcV9Wpzy','student'),
  ('Lucas','Petit','lucas.petit@student.cesi.fr','$2y$10$.cd.FRrb4dzmP4yzEb5GzesvmSZGAPHAAbFuPA9l/n2x1YcV9Wpzy','student'),
  ('Emma','Robert','emma.robert@student.cesi.fr','$2y$10$.cd.FRrb4dzmP4yzEb5GzesvmSZGAPHAAbFuPA9l/n2x1YcV9Wpzy','student');

-- Pilotes
INSERT INTO `pilots` (`user_id`,`promotion`) VALUES (2,'Promo 2025 - Informatique'),(3,'Promo 2025 - Systèmes');

-- Étudiants
INSERT INTO `students` (`user_id`,`pilot_id`,`stage_status`) VALUES (4,1,'searching'),(5,1,'applied'),(6,2,'searching');

-- Offres de stage
INSERT INTO `offers` (`company_id`,`pilot_id`,`title`,`description`,`salary`,`duration`,`location`,`offer_date`) VALUES
  (1,1,'Développeur Web Full Stack','Rejoignez notre équipe pour développer des applications web modernes en PHP/JS.',800,24,'Paris','2025-01-15'),
  (2,1,'Stagiaire Data Analyst','Analysez des jeux de données complexes avec Python et Power BI.',750,16,'Lyon','2025-01-20'),
  (3,2,'UX/UI Designer Junior','Participez à la conception d''interfaces utilisateur sur des projets clients.',700,20,'Bordeaux','2025-02-01'),
  (4,2,'Chargé Marketing Digital','Gérez les campagnes SEO/SEA et les réseaux sociaux de nos clients.',680,24,'Nantes','2025-02-05'),
  (5,1,'Développeur Cybersécurité','Audit de sécurité et développement d''outils de protection.',900,24,'Toulouse','2025-02-10'),
  (6,2,'Stage Machine Learning','Développez des modèles de ML pour des clients grands comptes.',1000,24,'Sophia-Antipolis','2025-02-15');

-- Compétences ↔ Offres
INSERT INTO `offer_skills` VALUES (1,1),(1,2),(1,3),(2,4),(2,14),(3,9),(3,10),(4,11),(4,12),(5,20),(5,8),(6,15),(6,4);

-- Wishlist exemple
INSERT INTO `wishlists` (`student_id`,`offer_id`) VALUES (1,1),(1,3),(2,2);

-- Candidatures exemple
INSERT INTO `applications` (`student_id`,`offer_id`,`cover_letter`,`status`) VALUES
  (1,1,'Je suis très motivé par ce poste de développeur full-stack...','pending'),
  (2,2,'Ma passion pour la data m''amène à vous proposer ma candidature...','reviewed');

-- Évaluation exemple
INSERT INTO `company_reviews` (`company_id`,`student_id`,`rating`,`comment`) VALUES
  (1,1,5,'Excellente entreprise, équipe très accueillante et projets variés.');
