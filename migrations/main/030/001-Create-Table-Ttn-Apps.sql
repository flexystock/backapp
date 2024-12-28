CREATE TABLE `ttn_apps` (
   `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
   `uuid_client` VARCHAR(36) NOT NULL COMMENT 'Id del cliente',
   `ttn_application_id` VARCHAR(100) NOT NULL COMMENT 'Id de la aplicacion del cliente en TTN',
   `ttn_application_name` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre de la aplicacion del cliente en TTN',
   `ttn_application_description` VARCHAR(255) DEFAULT NULL COMMENT 'Descripción adicional de la aplicacion del cliente en TTN',
   `network_server_address` VARCHAR(100) DEFAULT 'eu1.cloud.thethings.network' COMMENT 'Dirección del Network Server',
   `application_server_address` VARCHAR(100) DEFAULT 'eu1.cloud.thethings.network' COMMENT 'Dirección del Application Server',
   `join_server_address` VARCHAR(100) DEFAULT 'eu1.cloud.thethings.network' COMMENT 'Dirección del Join Server',
   `ttn_application_key_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID del Token para poder crear DEVICES via API',
   `ttn_application_key` VARCHAR(100) DEFAULT NULL COMMENT 'Token para poder crear DEVICES via API',
   `ttn_application_key_name` VARCHAR(100) DEFAULT NULL COMMENT 'Nombre del Token para poder crear DEVICES via API',
   `expires_ttn_application_key` VARCHAR(100) DEFAULT NULL COMMENT 'Fecha de expiracino del token',
   `uuid_user_creation` VARCHAR(36) NOT NULL COMMENT 'UUID del usuario que crea este registro',
   `datehour_creation` DATETIME NOT NULL COMMENT 'Fecha/hora de creación del registro',
   `uuid_user_modification` VARCHAR(36) DEFAULT NULL COMMENT 'UUID del usuario que modifica este registro',
   `datehour_modification` DATETIME DEFAULT NULL COMMENT 'Fecha/hora de la última modificación',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;