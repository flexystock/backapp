-- Asegurar que las claves foráneas se validen correctamente
SET FOREIGN_KEY_CHECKS=0;

-- 1. Crear la tabla warehouses (no depende de ninguna otra)
CREATE TABLE `warehouses` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
  `name` VARCHAR(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del almacén',
  `comment` VARCHAR(100) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Comentario adicional del usuario',
  `width` TINYINT(3) UNSIGNED NOT NULL COMMENT 'Ancho en celdas del almacén',
  `height` TINYINT(3) UNSIGNED DEFAULT NULL,
  `uuidUserCreation` VARCHAR(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuairo que la creo',
  `datehourCreation` DATETIME NOT NULL COMMENT 'Fecha y hora de creación',
  `uuidUserModification` VARCHAR(36) COLLATE utf8_bin DEFAULT NULL,
  `datehourModification` DATETIME DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `uuid_UNIQUE` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Almacenes';

-- 2. Crear la tabla products (no depende de warehouses directamente)
CREATE TABLE `products` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
  `name` VARCHAR(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del producto',
  `ean` VARCHAR(128) COLLATE utf8_bin DEFAULT NULL,
  `weight_range` DECIMAL(8,5) UNSIGNED DEFAULT NULL COMMENT 'Rango diferencia para considerar un peso nuevo',
  `name_unit1` VARCHAR(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Unidad adicional1',
  `weight_unit1` DECIMAL(9,5) UNSIGNED DEFAULT NULL COMMENT 'Kilos por unidad1',
  `name_unit2` VARCHAR(50) COLLATE utf8_bin DEFAULT NULL,
  `weight_unit2` DECIMAL(9,5) UNSIGNED DEFAULT NULL,
  `main_unit` ENUM('0','1','2') COLLATE utf8_bin NOT NULL DEFAULT '0',
  `tare` DECIMAL(9,5) UNSIGNED DEFAULT '0.00000',
  `sale_price` DECIMAL(6,2) UNSIGNED DEFAULT '0.00',
  `cost_price` DECIMAL(6,2) UNSIGNED DEFAULT '0.00',
  `out_system_stock` TINYINT(1) DEFAULT NULL,
  `days_average_consumption` INT(3) UNSIGNED DEFAULT '30',
  `days_serve_order` INT(3) UNSIGNED DEFAULT '0',
  `uuidUserCreation` VARCHAR(36) COLLATE utf8_bin NOT NULL,
  `datehourCreation` DATETIME NOT NULL,
  `uuidUserModification` VARCHAR(36) COLLATE utf8_bin DEFAULT NULL,
  `datehourModification` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Productos';

-- 3. Crear la tabla scales (depende de products)
CREATE TABLE `scales` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
  `serial_number` VARCHAR(50) COLLATE utf8_bin NOT NULL COMMENT 'Número de serie',
  `voltage_min` DECIMAL(5,3) UNSIGNED NOT NULL COMMENT 'Voltage mínimo',
  `last_send` DATETIME DEFAULT NULL COMMENT 'último envío de la báscula',
  `battery_die` DATETIME DEFAULT NULL COMMENT 'Fecha estimada fin batería',
  `product_id` INT(11) UNSIGNED DEFAULT NULL,
  `posX` TINYINT(2) UNSIGNED DEFAULT NULL,
  `width` TINYINT(2) UNSIGNED DEFAULT NULL,
  `uuidUserCreation` VARCHAR(36) COLLATE utf8_bin NOT NULL,
  `datehourCreation` DATETIME NOT NULL,
  `uuidUserModification` VARCHAR(36) COLLATE utf8_bin DEFAULT NULL,
  `datehourModification` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  KEY `fk_scales_products_idx` (`product_id`),
  CONSTRAINT `fk_scales_products` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Balanzas';

-- 4. Crear la tabla weights_log (depende de scales y products)
CREATE TABLE `weights_log` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `scale_id` INT(11) UNSIGNED NOT NULL,
  `product_id` INT(11) UNSIGNED DEFAULT NULL,
  `date` DATETIME NOT NULL,
  `real_weight` DECIMAL(8,5) NOT NULL,
  `adjust_weight` DECIMAL(8,5) NOT NULL,
  `charge_percentage` DECIMAL(5,2) NOT NULL,
  `voltage` DECIMAL(5,3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_weightslog_scales_idx` (`scale_id`),
  KEY `fk_weightslog_products_idx` (`product_id`),
  CONSTRAINT `fk_weightslog_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_weightslog_products` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Log de las pesadas';

SET FOREIGN_KEY_CHECKS=1;