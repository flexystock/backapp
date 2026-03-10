CREATE TABLE IF NOT EXISTS `product_service_hour` (
    `id`           INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `product_id`   INT UNSIGNED    NOT NULL,
    `day_of_week`  TINYINT         NOT NULL COMMENT '1=Lunes, 7=Domingo (ISO 8601)',
    `start_time_1` TIME            NOT NULL,
    `end_time_1`   TIME            NOT NULL,
    `start_time_2` TIME            NULL DEFAULT NULL,
    `end_time_2`   TIME            NULL DEFAULT NULL,
    `created_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_product_day` (`product_id`, `day_of_week`),
    CONSTRAINT `fk_psh_product` FOREIGN KEY (`product_id`) 
        REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
