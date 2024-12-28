CREATE TABLE `pool_ttn_device` (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `available` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica si el dispositivo está disponible o no',
   `end_device_id` VARCHAR(100) NOT NULL COMMENT 'Id del dispositivo en TTN',
   `appEUI` VARCHAR(100) NOT NULL COMMENT 'appEUI del dispositivo en TTN',
   `devEUI` VARCHAR(100) NOT NULL COMMENT 'devEUI del dispositivo en TTN',
   `appKey` VARCHAR(100) NOT NULL COMMENT 'appKey del dispositivo en TTN',
   `uuid_user_creation` VARCHAR(36) NOT NULL COMMENT 'UUID del usuario que crea este registro',
   `datehour_creation` DATETIME NOT NULL COMMENT 'Fecha/hora de creación del registro',
   `uuid_user_modification` VARCHAR(36) DEFAULT NULL COMMENT 'UUID del usuario que modifica este registro',
   `datehour_modification` DATETIME DEFAULT NULL COMMENT 'Fecha/hora de la última modificación',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;