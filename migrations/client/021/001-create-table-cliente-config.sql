CREATE TABLE IF NOT EXISTS `cliente_config` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `check_out_of_hours` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `check_holidays` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `check_battery_shelve` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    `uuid_user_creation` CHAR(36) NOT NULL,
    `datehour_creation` DATETIME NOT NULL,
    `uuid_user_modification` CHAR(36) DEFAULT NULL,
    `datehour_modification` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
