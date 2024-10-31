CREATE TABLE `user_business_group` (
  `uuid_user` CHAR(36) NOT NULL,
  `uuid_business_group` CHAR(36) NOT NULL,
  PRIMARY KEY (`uuid_user`, `uuid_business_group`),
  FOREIGN KEY (`uuid_user`) REFERENCES `users` (`uuid_user`) ON DELETE CASCADE,
  FOREIGN KEY (`uuid_business_group`) REFERENCES `business_group` (`uuid_business_group`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;