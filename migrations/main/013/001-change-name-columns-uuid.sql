ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
ALTER TABLE `login` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
ALTER TABLE `login` DROP FOREIGN KEY `fk_login_users`;
ALTER TABLE `client`
    CHANGE COLUMN `uuid` `uuid_client` CHAR(36) NOT NULL COMMENT 'UUID del cliente';
ALTER TABLE `users`
    CHANGE COLUMN `uuid` `uuid_user` VARCHAR(36) NOT NULL COMMENT 'UUID del usuario';
ALTER TABLE `login`
    ADD CONSTRAINT `fk_login_users` FOREIGN KEY (`uuidUser`) REFERENCES `users`(`uuid_user`);

