CREATE TABLE `user_client` (
  `uuid_user` CHAR(36) NOT NULL,
  `uuid_client` CHAR(36) NOT NULL,
  PRIMARY KEY (`uuid_user`, `uuid_client`),
  FOREIGN KEY (`uuid_user`) REFERENCES `users` (`uuid_user`) ON DELETE CASCADE,
  FOREIGN KEY (`uuid_client`) REFERENCES `client` (`uuid_client`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;