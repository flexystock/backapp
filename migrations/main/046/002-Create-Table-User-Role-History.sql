CREATE TABLE `user_role_history` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid_user` VARCHAR(36) COLLATE utf8_bin NOT NULL,
  `uuid_user_modification` VARCHAR(36) COLLATE utf8_bin NOT NULL,
  `data_roles_before_modification` TEXT COLLATE utf8_bin NOT NULL,
  `data_roles_after_modification` TEXT COLLATE utf8_bin NOT NULL,
  `date_modification` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
