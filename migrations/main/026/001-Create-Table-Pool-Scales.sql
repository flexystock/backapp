CREATE TABLE `pool_scales` (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `uuid_scale` VARCHAR(36) NOT NULL COMMENT 'UUID interno generado por la aplicación para identificar la balanza',
   `available` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '1 = disponible, 0 = no disponible',
   `name` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre amigable de la balanza',
   `description` VARCHAR(255) DEFAULT NULL COMMENT 'Descripción adicional de la balanza',

   `device_id` VARCHAR(100) NOT NULL COMMENT 'device_id asignado en TTN',
   `dev_eui` VARCHAR(16) NOT NULL COMMENT 'DevEUI del dispositivo (ej: 70B3D57E...)',
   `join_eui` VARCHAR(16) NOT NULL COMMENT 'JoinEUI del dispositivo',
   `app_key` VARCHAR(32) NOT NULL COMMENT 'APP KEY del dispositivo',
   `application_id` VARCHAR(100) NOT NULL COMMENT 'application_id usada en TTN (ej: aplicacionpruebas1)',

   `lorawan_version` VARCHAR(50) DEFAULT NULL COMMENT 'Versión de LoRaWAN (ej: 1.0.2)',
   `lorawan_phy_version` VARCHAR(50) DEFAULT NULL COMMENT 'Versión física de LoRaWAN (ej: 1.0.2-b)',
   `frequency_plan_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID del plan de frecuencias (ej: EU_863_870_TTN)',
   `supports_join` TINYINT(1) DEFAULT NULL COMMENT 'Indica si soporta join (OTAA)',

   `network_server_address` VARCHAR(100) DEFAULT 'eu1.cloud.thethings.network' COMMENT 'Dirección del Network Server',
   `application_server_address` VARCHAR(100) DEFAULT 'eu1.cloud.thethings.network' COMMENT 'Dirección del Application Server',
   `join_server_address` VARCHAR(100) DEFAULT 'eu1.cloud.thethings.network' COMMENT 'Dirección del Join Server',

   `uuid_user_creation` VARCHAR(36) NOT NULL COMMENT 'UUID del usuario que crea este registro',
   `datehour_creation` DATETIME NOT NULL COMMENT 'Fecha/hora de creación del registro',

   `uuid_user_modification` VARCHAR(36) DEFAULT NULL COMMENT 'UUID del usuario que modifica este registro',
   `datehour_modification` DATETIME DEFAULT NULL COMMENT 'Fecha/hora de la última modificación',

  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_scale_UNIQUE` (`uuid_scale`),
  UNIQUE KEY `device_id_UNIQUE` (`device_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pool de balanzas disponibles registradas en TTN';