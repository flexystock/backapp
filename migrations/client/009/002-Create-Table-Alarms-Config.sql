CREATE TABLE `alarm_config` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `alarm_name` VARCHAR(255) COLLATE utf8_bin NOT NULL,
    `product_id` INT(11) UNSIGNED NOT NULL,
    `alarm_type_id` INT(11) UNSIGNED NOT NULL, -- Clave foránea a alarm_types
    `percentage_threshold` DECIMAL(5,2) DEFAULT NULL,
    `uuid_user_creation` VARCHAR(36) COLLATE utf8_bin NOT NULL,
    `datehour_creation` DATETIME NOT NULL,
    `uuid_user_modification` VARCHAR(36) COLLATE utf8_bin DEFAULT NULL,
    `datehour_modification` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    FOREIGN KEY (`alarm_type_id`) REFERENCES `alarm_types`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;