CREATE TABLE `purchase_scales` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `uuid_purchase` CHAR(36) COLLATE utf8mb4_bin NOT NULL UNIQUE,
    `uuid_client` CHAR(36) COLLATE utf8mb4_bin NOT NULL,
    `client_name` VARCHAR(100) NOT NULL,
    `quantity` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `status` ENUM('pending', 'processing', 'completed', 'rejected') NOT NULL DEFAULT 'pending',
    `notes` TEXT NULL,
    `purchase_at` DATETIME NOT NULL,
    `processed_at` DATETIME NULL,
    `processed_by_uuid_user` CHAR(36) COLLATE utf8mb4_bin NULL,

    INDEX `idx_uuid_client` (`uuid_client`),
    INDEX `idx_status` (`status`),
    INDEX `idx_purchase_at` (`purchase_at`),

    CONSTRAINT `fk_scale_purchase_client`
        FOREIGN KEY (`uuid_client`)
        REFERENCES `client`(`uuid_client`)
        ON DELETE CASCADE,

    CONSTRAINT `fk_scale_purchase_processed_by`
        FOREIGN KEY (`processed_by_uuid_user`)
        REFERENCES `users`(`uuid_user`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;