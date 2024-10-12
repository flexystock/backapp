CREATE TABLE `login` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `uuidUser` CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'uuid del usuario',
    PRIMARY KEY (`id`),
    KEY `fk_login_users_idx` (`uuidUser`),
    CONSTRAINT `fk_login_users` FOREIGN KEY (`uuidUser`) REFERENCES `users` (`uuid_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin COMMENT='Tabla de control de logins';

