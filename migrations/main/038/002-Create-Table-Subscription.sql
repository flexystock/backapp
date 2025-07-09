CREATE TABLE `subscription` (
  `uuid_subscription` CHAR(36) NOT NULL,
  `client_uuid` CHAR(36) NOT NULL,
  `subscription_plan_id` INT(11) UNSIGNED NOT NULL,
  `started_at` DATETIME NOT NULL,
  `ended_at` DATETIME DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`uuid_subscription`),
  CONSTRAINT `fk_subscription_plan` FOREIGN KEY (`subscription_plan_id`) REFERENCES `subscription_plan`(`id`),
  CONSTRAINT `fk_subscription_client` FOREIGN KEY (`client_uuid`) REFERENCES `client`(`uuid_client`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
