CREATE TABLE IF NOT EXISTS `log_holidays` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid_client` CHAR(36) NOT NULL,
    `uuid_user_modification` CHAR(36) NOT NULL,
    `data_before_modification` TEXT NOT NULL,
    `data_after_modification` TEXT NOT NULL,
    `date_modification` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
