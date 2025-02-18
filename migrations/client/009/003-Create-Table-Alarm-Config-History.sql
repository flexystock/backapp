CREATE TABLE `alarm_config_history` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `alarm_config_id` INT(11) UNSIGNED NOT NULL,
    `uuid_user_modification` VARCHAR(36) COLLATE utf8_bin NOT NULL,
    `data_alarm_before_modification` TEXT COLLATE utf8_bin NOT NULL,
    `data_alarm_after_modification` TEXT COLLATE utf8_bin NOT NULL,
    `datehour_modification` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`alarm_config_id`) REFERENCES `alarm_config`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;