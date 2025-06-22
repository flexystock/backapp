CREATE TABLE `pool_scales` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `available` TINYINT(1) NOT NULL DEFAULT '1' COMMENT 'Indica si la balanza est\u00e1 disponible o no',
  `end_device_id` VARCHAR(100) NOT NULL COMMENT 'Id del dispositivo en TTN',
  `end_device_name` VARCHAR(36) DEFAULT NULL COMMENT 'Uuid del cliente asociado',
  `appEUI` VARCHAR(100) NOT NULL COMMENT 'appEUI del dispositivo en TTN',
  `devEUI` VARCHAR(100) NOT NULL COMMENT 'devEUI del dispositivo en TTN',
  `appKey` VARCHAR(100) NOT NULL COMMENT 'appKey del dispositivo en TTN',
  `uuid_user_creation` VARCHAR(36) NOT NULL COMMENT 'UUID del usuario que crea este registro',
  `datehour_creation` DATETIME NOT NULL COMMENT 'Fecha/hora de creaci\u00f3n del registro',
  `uuid_user_modification` VARCHAR(36) DEFAULT NULL COMMENT 'UUID del usuario que modifica este registro',
  `datehour_modification` DATETIME DEFAULT NULL COMMENT 'Fecha/hora de la \u00faltima modificaci\u00f3n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
