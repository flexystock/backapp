
ALTER TABLE `profiles` MODIFY `id` INT NOT NULL AUTO_INCREMENT;

CREATE TABLE `role_profile` (
  `role_id` INT UNSIGNED NOT NULL,
  `profile_id` INT NOT NULL,
  PRIMARY KEY (`role_id`, `profile_id`),
  CONSTRAINT `fk_role_profile_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_profile_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
