CREATE TABLE `user_role` (
  `uuid_user` varchar(36) NOT NULL,
  `role_id` int unsigned NOT NULL,
  PRIMARY KEY (`uuid_user`, `role_id`),
  CONSTRAINT `fk_user_role_user` FOREIGN KEY (`uuid_user`) REFERENCES `users` (`uuid_user`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_role_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
