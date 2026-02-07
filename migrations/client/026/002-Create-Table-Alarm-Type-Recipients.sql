-- Create alarm_type_recipients table for managing multiple email recipients per alarm type
CREATE TABLE `alarm_type_recipients` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `uuid_client` VARCHAR(36) COLLATE utf8_bin NOT NULL,
    `alarm_type_id` INT(11) UNSIGNED NOT NULL,
    `email` VARCHAR(255) COLLATE utf8_bin NOT NULL,
    `uuid_user_creation` VARCHAR(36) COLLATE utf8_bin DEFAULT NULL,
    `datehour_creation` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `uuid_user_modification` VARCHAR(36) COLLATE utf8_bin DEFAULT NULL,
    `datehour_modification` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_client_alarm_email` (`uuid_client`, `alarm_type_id`, `email`),
    KEY `idx_uuid_client` (`uuid_client`),
    KEY `idx_alarm_type_id` (`alarm_type_id`),
    CONSTRAINT `fk_alarm_type_recipients_alarm_type` FOREIGN KEY (`alarm_type_id`) REFERENCES `alarm_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
