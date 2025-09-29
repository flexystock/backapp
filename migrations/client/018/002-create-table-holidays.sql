CREATE TABLE IF NOT EXISTS `holidays` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `holiday_date` DATE NOT NULL,
    `name` VARCHAR(150) DEFAULT NULL,
    `uuid_user_creation` CHAR(36) NOT NULL,
    `datehour_creation` DATETIME NOT NULL,
    `uuid_user_modification` CHAR(36) DEFAULT NULL,
    `datehour_modification` DATETIME DEFAULT NULL,
    UNIQUE KEY `uniq_holidays_holiday_date` (`holiday_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
