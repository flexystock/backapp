ALTER TABLE `pool_ttn_device`
ADD COLUMN `end_device_name` VARCHAR(36) DEFAULT NULL COMMENT 'Nombre del dispositivo en TTN que es del uuidClient' AFTER `end_device_id`;
