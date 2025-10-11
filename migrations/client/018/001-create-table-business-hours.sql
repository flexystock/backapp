CREATE TABLE IF NOT EXISTS `business_hours` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `day_of_week` TINYINT UNSIGNED NOT NULL,
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `uuid_user_creation` CHAR(36) NOT NULL,
    `datehour_creation` DATETIME NOT NULL,
    `uuid_user_modification` CHAR(36) DEFAULT NULL,
    `datehour_modification` DATETIME DEFAULT NULL,
    INDEX `idx_business_hours_day_of_week` (`day_of_week`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
