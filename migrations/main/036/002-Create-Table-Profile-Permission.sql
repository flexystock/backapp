CREATE TABLE `profile_permission` (
  `profile_id` INT NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`profile_id`, `permission_id`),
  CONSTRAINT `fk_profile_permission_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_profile_permission_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
