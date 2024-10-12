CREATE TABLE `user_profile` (
  `uuid_user` VARCHAR(36) NOT NULL,
  `profile_id` INT NOT NULL,
  PRIMARY KEY (`uuid_user`, `profile_id`),
  CONSTRAINT `fk_user_profile_user` FOREIGN KEY (`uuid_user`) REFERENCES `users` (`uuid_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_profile_profile` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;