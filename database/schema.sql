-- Схема БД MyCMS (MySQL 5.7+ / MariaDB 10.3+)
-- Миграция безопасности: пароли хранятся только как password_hash (bcrypt/argon2),
-- колонки `salt` и `hash` (токен автологина из cookie) упразднены.

CREATE DATABASE IF NOT EXISTS `mycms` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `mycms`;

CREATE TABLE IF NOT EXISTS `user` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `email`          VARCHAR(255) NOT NULL,
    `password_hash`  VARCHAR(255) NOT NULL,
    `role`           ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `page` (
    `id`       INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`    VARCHAR(255) NOT NULL,
    `slug`     VARCHAR(100) NOT NULL DEFAULT '',
    `content`  MEDIUMTEXT NULL,
    `date`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_page_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Создание администратора (пароль задаётся отдельно через скрипт admin:create-user,
-- НЕ вставлять bcrypt-хеш прямо в SQL-дамп):
-- INSERT INTO `user` (email, password_hash, role) VALUES ('admin@example.com', '<hash>', 'admin');

-- Откат старой схемы (выполнить один раз после миграции данных):
-- ALTER TABLE `user` DROP COLUMN `password`, DROP COLUMN `salt`, DROP COLUMN `hash`;
