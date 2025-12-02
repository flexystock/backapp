CREATE TABLE IF NOT EXISTS `report` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(150) NOT NULL,
    `period` VARCHAR(50) NOT NULL,
    `send_time` TIME NOT NULL,
    `report_type` VARCHAR(50) NOT NULL,
    `product_filter` VARCHAR(255) DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `uuid_user_creation` CHAR(36) NOT NULL,
    `datehour_creation` DATETIME NOT NULL,
    `uuid_user_modification` CHAR(36) DEFAULT NULL,
    `datehour_modification` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
