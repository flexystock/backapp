CREATE TABLE `rented_scale` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `subscription_uuid` CHAR(36) NOT NULL,
  `scale_uuid` CHAR(36) NOT NULL,
  `rented_at` DATETIME NOT NULL,
  `returned_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_rented_scale_subscription` FOREIGN KEY (`subscription_uuid`) REFERENCES `subscription`(`uuid_subscription`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;