/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- -----------------------------------------------------

-- Table structure for table `warehouses`
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                              `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del almacén',
                              `comment` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Comentario adicional del usuario',
                              `width` tinyint(3) unsigned NOT NULL COMMENT 'Ancho en celdas del almacén',
                              `height` tinyint(3) unsigned NOT NULL,
                              `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                              `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                              `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                              `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `name_UNIQUE` (`name`),
                              UNIQUE KEY `uuid_UNIQUE` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Almacenes';

-- Table structure for table `alarms_types`
DROP TABLE IF EXISTS `alarms_types`;
CREATE TABLE `alarms_types` (
                                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                `name` varchar(50) COLLATE utf8_bin NOT NULL,
                                `help` varchar(400) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Texto de ayuda',
                                `translation_name_id` int(11) unsigned DEFAULT NULL,
                                `translation_help_id` int(11) unsigned DEFAULT NULL,
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Table structure for table `alarms_recipients`
DROP TABLE IF EXISTS `alarms_recipients`;
CREATE TABLE `alarms_recipients` (
                                     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                     `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                     `type_send` enum('mail','sms') COLLATE utf8_bin NOT NULL COMMENT 'Tipo de envio de la notificacion',
                                     `recipient` varchar(300) COLLATE utf8_bin NOT NULL COMMENT 'Mail o número de móvil',
                                     `recipient_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del destinatario',
                                     `language_iso` varchar(3) COLLATE utf8_bin NOT NULL COMMENT 'Idioma en el que se lanza el aviso',
                                     `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                                     `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                     `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                     `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `type_send_recipient_UNIQUE` (`type_send`,`recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Destinatarios de las notificaciones de las alarmas';

-- Table structure for table `client_config`
DROP TABLE IF EXISTS `client_config`;
CREATE TABLE `client_config` (
                                 `key` varchar(20) COLLATE utf8_bin NOT NULL,
                                 `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del parámetro',
                                 `help` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Texto de ayuda',
                                 `readOnly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si se permite al usuario modificar el valor',
                                 `value` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT 'Valor de la configuración para el cliente',
                                 `dataType` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'string' COMMENT 'Tipo de dato segun nomenclatura PHP',
                                 PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Configuraciones del cliente';

-- Table structure for table `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
                            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                            `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                            `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre de la balda',
                            `ean` varchar(128) COLLATE utf8_bin DEFAULT NULL,
                            `weight_range` decimal(8,5) unsigned DEFAULT NULL COMMENT 'Si se establece el rango de diferencia en un peso, para considerar que es un peso nuevo. Si no esta establecido se usa el de la configuración del cliente',
                            `name_unit1` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Si se lleva el control de stock con una unidad adicional',
                            `weight_unit1` decimal(9,5) unsigned DEFAULT NULL COMMENT 'Cuantos kilos pesa una unidad. En el front se piden 3 chars y 3 decimals',
                            `name_unit2` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Si se lleva el control de stock con una unidad adicional',
                            `weight_unit2` decimal(9,5) unsigned DEFAULT NULL COMMENT 'Cuantos kilos pesa una unidad. En el front se piden 3 chars y 3 decimals',
                            `main_unit` enum('0','1','2') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT 'Si tiene más de una unidad, cual es la principal. 0 kilos, 1, 2',
                            `tare` decimal(9,5) unsigned DEFAULT '0.00000' COMMENT 'Si el producto tiene tara',
                            `sale_price` decimal(6,2) unsigned DEFAULT '0.00' COMMENT 'Precio de venta',
                            `cost_price` decimal(6,2) unsigned DEFAULT '0.00',
                            `out_system_stock` tinyint(1) DEFAULT NULL COMMENT 'Si tiene stock de este producto que no lo controlan las basculas ',
                            `days_average_consumption` int(3) unsigned DEFAULT '30' COMMENT 'Cuantos días atrás tiene en cuenta en el log de pesos para obtener el consumo medio',
                            `days_serve_order` int(3) unsigned DEFAULT '0' COMMENT 'Cuantos días tarda un pedido en llegar desde que se realiza. Este campo lo iremos actualizando de forma dinámica',
                            `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                            `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                            `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                            `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Productos';

-- Table structure for table `scales`
DROP TABLE IF EXISTS `scales`;
CREATE TABLE `scales` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                          `serial_number` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'El número de serie del chip',
                          `voltage_min` decimal(5,3) unsigned NOT NULL COMMENT 'Voltage mínimo que hará dispararse nuestra alarma interna',
                          `last_send` datetime DEFAULT NULL COMMENT 'Fecha y hora del último envío de la bascula que llegó al api',
                          `battery_die` datetime DEFAULT NULL COMMENT 'Fecha / hora estimada de cuando se va a agotar la batería',
                          `product_id` int(11) unsigned DEFAULT NULL,
                          `posX` tinyint(2) unsigned DEFAULT NULL COMMENT 'Posición x en la cuadricula del hueco',
                          `width` tinyint(2) unsigned DEFAULT NULL COMMENT 'Ancho en celdas en la cuadricula del hueco',
                          `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                          `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                          `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                          `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                          KEY `fk_scales_products_idx` (`product_id`),
                          CONSTRAINT `fk_scales_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Balanzas';

-- Table structure for table `storage_racks`
DROP TABLE IF EXISTS `storage_racks`;
CREATE TABLE `storage_racks` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                 `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                 `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre de la estantería',
                                 `warehouse_id` int(11) unsigned NOT NULL,
                                 `posX` tinyint(2) unsigned NOT NULL COMMENT 'Posición x en la cuadricula del almacén para la visualización del almacén',
                                 `posY` tinyint(2) unsigned NOT NULL COMMENT 'Posición y en la cuadricula del almacén para la visualización del almacén',
                                 `width` tinyint(2) unsigned NOT NULL COMMENT 'Ancho en celdas en la cuadricula del almacén para visualización almacén',
                                 `orientation` enum('horizontal','vertical') COLLATE utf8_bin NOT NULL COMMENT 'La orientación. Si es vertical, width indica el alto',
                                 `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                                 `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                 `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                 `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                                 UNIQUE KEY `name_UNIQUE` (`name`,`warehouse_id`),
                                 KEY `fk_storageracks_warehouses_idx` (`warehouse_id`),
                                 CONSTRAINT `fk_storageracks_warehouses` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Estanterias';

-- Table structure for table `alarms`
DROP TABLE IF EXISTS `alarms`;
CREATE TABLE `alarms` (
                          `type_alarm_id` int(11) unsigned NOT NULL,
                          `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                          `repetition` enum('immediate','daily','weekly','monthly') COLLATE utf8_bin NOT NULL,
                          `week_day` tinyint(1) unsigned DEFAULT NULL COMMENT 'Si la periodicidad es semanal, que dia de la semana se comprueba la alarma, 1-lunes 7-domingo',
                          `month_day` tinyint(2) unsigned DEFAULT NULL COMMENT 'Si la periodicidad es mensual, el día del mes que se comprueba la alarma',
                          `check_hour` varchar(5) COLLATE utf8_bin DEFAULT NULL COMMENT 'Hora para chequear la alarma. Inmediatly no la tiene en cuenta. 09:00',
                          `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                          `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                          `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                          `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                          PRIMARY KEY (`type_alarm_id`),
                          UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                          CONSTRAINT `fk_alarms_alarmstypes` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Alarmas';

-- Table structure for table `alarms_notifications`
DROP TABLE IF EXISTS `alarms_notifications`;
CREATE TABLE `alarms_notifications` (
                                        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                        `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                        `type_alarm_id` int(11) unsigned NOT NULL,
                                        `recipient_id` int(11) unsigned DEFAULT NULL,
                                        PRIMARY KEY (`id`),
                                        UNIQUE KEY `recipientid_typealarmid_UNIQUE` (`recipient_id`,`type_alarm_id`),
                                        KEY `fk_alarmsnotifications_alarms_idx` (`type_alarm_id`),
                                        KEY `fk_alarmsnotifications_alarmsrecipients_idx` (`recipient_id`),
                                        CONSTRAINT `fk_alarmsnotifications_alarms` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms` (`type_alarm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                        CONSTRAINT `fk_alarmsnotifications_alarmsrecipients` FOREIGN KEY (`recipient_id`) REFERENCES `alarms_recipients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Destinatarios de las notificaciones de las alarmas';

-- Table structure for table `products_warehouse`
DROP TABLE IF EXISTS `products_warehouse`;
CREATE TABLE `products_warehouse` (
                                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                      `product_id` int(11) unsigned NOT NULL,
                                      `warehouse_id` int(11) unsigned NOT NULL,
                                      `out_system_stock` tinyint(1) DEFAULT '0' COMMENT 'Si tiene stock de este producto que no lo controlan las basculas ',
                                      `days_average_consumption` int(3) unsigned DEFAULT '30' COMMENT 'Cuantos días atrás tiene en cuenta en el log de pesos para obtener el consumo medio',
                                      `days_serve_order` int(3) unsigned DEFAULT '0' COMMENT 'Cuantos días tarda un pedido en llegar desde que se realiza. Este campo lo iremos actualizando de forma dinámica',
                                      `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                                      `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                      `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                      `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `productid_warehouseid_UNIQUE` (`product_id`,`warehouse_id`),
                                      KEY `fk_productswarehouses_warehouses_idx` (`warehouse_id`),
                                      KEY `fk_productswarehouses_products_idx` (`product_id`),
                                      CONSTRAINT `fk_productswarehouses_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                      CONSTRAINT `fk_productswarehouses_warehouses` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Configuración de los productos por cada almacén';

-- Table structure for table `alarms_log`
DROP TABLE IF EXISTS `alarms_log`;
CREATE TABLE `alarms_log` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `type_alarm_id` int(11) unsigned NOT NULL,
                              `product_id` int(11) unsigned DEFAULT NULL COMMENT 'Id del producto',
                              `scale_id` int(11) unsigned DEFAULT NULL COMMENT 'id de la balanza (cuando la alarma es de voltage)',
                              `date` datetime NOT NULL COMMENT 'Fecha / hora de la comprobación',
                              `weight` decimal(5,2) DEFAULT NULL COMMENT 'Peso en kilos registrados. Es la suma de kilos de todas las balanzas del producto',
                              `charge_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Porcentaje de carga de la batería que dispara la alarma xxx.xx',
                              `voltage` decimal(5,2) DEFAULT NULL COMMENT 'Voltage de la batería que dispara la alarma xxx.xx',
                              `triggered` tinyint(1) NOT NULL COMMENT 'Si se disparó o no',
                              `notification_send_id` int(11) unsigned DEFAULT NULL COMMENT 'El id del envío de la notificación si se produjo',
                              PRIMARY KEY (`id`),
                              KEY `fk_alarmslog_alarms_idx` (`type_alarm_id`),
                              KEY `fk_alarmslog_alarmsnotificationssend_idx` (`notification_send_id`),
                              KEY `fk_alarmslog_products_idx` (`product_id`),
                              KEY `fk_alarmslog_scales_idx` (`scale_id`),
                              CONSTRAINT `fk_alarmslog_alarms` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms` (`type_alarm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                              CONSTRAINT `fk_alarmslog_alarmsnotificationssend` FOREIGN KEY (`notification_send_id`) REFERENCES `alarms_notifications_send` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                              CONSTRAINT `fk_alarmslog_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                              CONSTRAINT `fk_alarmslog_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Log de las comprobaciones realizadas';

-- Table structure for table `alarms_notifications_send`
DROP TABLE IF EXISTS `alarms_notifications_send`;
CREATE TABLE `alarms_notifications_send` (
                                             `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                             `type_alarm_id` int(11) unsigned DEFAULT NULL COMMENT 'id de la alarma (cuando la alarma es de usuario)',
                                             `product_id` int(11) unsigned DEFAULT NULL COMMENT 'Id del producto (cuando la alarma es de pesos)',
                                             `scale_id` int(11) unsigned DEFAULT NULL COMMENT 'id de la balanza (cuando la alarma es de voltage)',
                                             `key` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Si es de usuario la concatenación de ids (tipo de alarma, producto y balanza). Si es de sistema la concatenación de una clave interna nuestra',
                                             `date` datetime NOT NULL COMMENT 'Fecha / hora de la comprobación',
                                             `recipients` varchar(2000) COLLATE utf8_bin NOT NULL COMMENT 'Los destinatarios (teléfonos, mails) a los que se envió el aviso',
                                             PRIMARY KEY (`id`),
                                             KEY `fk_alarmsnotificationssend_alarms_idx` (`type_alarm_id`),
                                             KEY `fk_alarmsnotificationssend_products_idx` (`product_id`),
                                             KEY `fk_alarmsnotificationssend_scales_idx` (`scale_id`),
                                             CONSTRAINT `fk_alarmsnotificationssend_alarms` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms` (`type_alarm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                             CONSTRAINT `fk_alarmsnotificationssend_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                             CONSTRAINT `fk_alarmsnotificationssend_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Log de los envíos de notificaciones y a que destinatarios';

-- Table structure for table `weights_log`
DROP TABLE IF EXISTS `weights_log`;
CREATE TABLE `weights_log` (
                               `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id única para el registro',
                               `scale_id` int(11) unsigned NOT NULL,
                               `product_id` int(11) unsigned DEFAULT NULL,
                               `date` datetime NOT NULL COMMENT 'Fecha y hora de la pesada',
                               `real_weight` decimal(8,5) NOT NULL COMMENT 'Peso en kilos que devolvio la balda',
                               `adjust_weight` decimal(8,5) NOT NULL COMMENT 'Peso en kilos ajustado por el api',
                               `charge_percentage` decimal(5,2) NOT NULL COMMENT 'Porcentaje de carga de la batería xxx.xx',
                               `voltage` decimal(5,3) NOT NULL COMMENT 'Voltage de la batería xxx.xx',
                               PRIMARY KEY (`id`),
                               KEY `fk_weightslog_scales_idx` (`scale_id`),
                               KEY `fk_weightslog_products_idx` (`product_id`),
                               CONSTRAINT `fk_weightslog_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                               CONSTRAINT `fk_weightslog_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Log de las pesadas';

-- Restore previous settings
SET TIME_ZONE = IF(@OLD_TIME_ZONE IS NULL, @@session.time_zone, @OLD_TIME_ZONE);
SET SQL_MODE = @OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION;
SET SQL_NOTES = @OLD_SQL_NOTES;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- -----------------------------------------------------

-- Table structure for table `warehouses`
DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE `warehouses` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                              `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del almacén',
                              `comment` varchar(100) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Comentario adicional del usuario',
                              `width` tinyint(3) unsigned NOT NULL COMMENT 'Ancho en celdas del almacén',
                              `height` tinyint(3) unsigned NOT NULL,
                              `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                              `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                              `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                              `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                              PRIMARY KEY (`id`),
                              UNIQUE KEY `name_UNIQUE` (`name`),
                              UNIQUE KEY `uuid_UNIQUE` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Almacenes';

-- Table structure for table `alarms_types`
DROP TABLE IF EXISTS `alarms_types`;
CREATE TABLE `alarms_types` (
                                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                `name` varchar(50) COLLATE utf8_bin NOT NULL,
                                `help` varchar(400) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Texto de ayuda',
                                `translation_name_id` int(11) unsigned DEFAULT NULL,
                                `translation_help_id` int(11) unsigned DEFAULT NULL,
                                PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Table structure for table `alarms_recipients`
DROP TABLE IF EXISTS `alarms_recipients`;
CREATE TABLE `alarms_recipients` (
                                     `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                     `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                     `type_send` enum('mail','sms') COLLATE utf8_bin NOT NULL COMMENT 'Tipo de envio de la notificacion',
                                     `recipient` varchar(300) COLLATE utf8_bin NOT NULL COMMENT 'Mail o número de móvil',
                                     `recipient_name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del destinatario',
                                     `language_iso` varchar(3) COLLATE utf8_bin NOT NULL COMMENT 'Idioma en el que se lanza el aviso',
                                     `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                                     `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                     `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                     `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `type_send_recipient_UNIQUE` (`type_send`,`recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Destinatarios de las notificaciones de las alarmas';

-- Table structure for table `client_config`
DROP TABLE IF EXISTS `client_config`;
CREATE TABLE `client_config` (
                                 `key` varchar(20) COLLATE utf8_bin NOT NULL,
                                 `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre del parámetro',
                                 `help` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT 'Texto de ayuda',
                                 `readOnly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si se permite al usuario modificar el valor',
                                 `value` varchar(300) COLLATE utf8_bin DEFAULT NULL COMMENT 'Valor de la configuración para el cliente',
                                 `dataType` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT 'string' COMMENT 'Tipo de dato segun nomenclatura PHP',
                                 PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Configuraciones del cliente';

-- Table structure for table `products`
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
                            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                            `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                            `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre de la balda',
                            `ean` varchar(128) COLLATE utf8_bin DEFAULT NULL,
                            `weight_range` decimal(8,5) unsigned DEFAULT NULL COMMENT 'Si se establece el rango de diferencia en un peso, para considerar que es un peso nuevo. Si no esta establecido se usa el de la configuración del cliente',
                            `name_unit1` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Si se lleva el control de stock con una unidad adicional',
                            `weight_unit1` decimal(9,5) unsigned DEFAULT NULL COMMENT 'Cuantos kilos pesa una unidad. En el front se piden 3 chars y 3 decimals',
                            `name_unit2` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT 'Si se lleva el control de stock con una unidad adicional',
                            `weight_unit2` decimal(9,5) unsigned DEFAULT NULL COMMENT 'Cuantos kilos pesa una unidad. En el front se piden 3 chars y 3 decimals',
                            `main_unit` enum('0','1','2') COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT 'Si tiene más de una unidad, cual es la principal. 0 kilos, 1, 2',
                            `tare` decimal(9,5) unsigned DEFAULT '0.00000' COMMENT 'Si el producto tiene tara',
                            `sale_price` decimal(6,2) unsigned DEFAULT '0.00' COMMENT 'Precio de venta',
                            `cost_price` decimal(6,2) unsigned DEFAULT '0.00',
                            `out_system_stock` tinyint(1) DEFAULT NULL COMMENT 'Si tiene stock de este producto que no lo controlan las basculas ',
                            `days_average_consumption` int(3) unsigned DEFAULT '30' COMMENT 'Cuantos días atrás tiene en cuenta en el log de pesos para obtener el consumo medio',
                            `days_serve_order` int(3) unsigned DEFAULT '0' COMMENT 'Cuantos días tarda un pedido en llegar desde que se realiza. Este campo lo iremos actualizando de forma dinámica',
                            `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                            `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                            `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                            `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Productos';

-- Table structure for table `scales`
DROP TABLE IF EXISTS `scales`;
CREATE TABLE `scales` (
                          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                          `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                          `serial_number` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'El número de serie del chip',
                          `voltage_min` decimal(5,3) unsigned NOT NULL COMMENT 'Voltage mínimo que hará dispararse nuestra alarma interna',
                          `last_send` datetime DEFAULT NULL COMMENT 'Fecha y hora del último envío de la bascula que llegó al api',
                          `battery_die` datetime DEFAULT NULL COMMENT 'Fecha / hora estimada de cuando se va a agotar la batería',
                          `product_id` int(11) unsigned DEFAULT NULL,
                          `posX` tinyint(2) unsigned DEFAULT NULL COMMENT 'Posición x en la cuadricula del hueco',
                          `width` tinyint(2) unsigned DEFAULT NULL COMMENT 'Ancho en celdas en la cuadricula del hueco',
                          `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                          `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                          `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                          `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                          PRIMARY KEY (`id`),
                          UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                          KEY `fk_scales_products_idx` (`product_id`),
                          CONSTRAINT `fk_scales_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Balanzas';

-- Table structure for table `storage_racks`
DROP TABLE IF EXISTS `storage_racks`;
CREATE TABLE `storage_racks` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                 `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                 `name` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'Nombre de la estantería',
                                 `warehouse_id` int(11) unsigned NOT NULL,
                                 `posX` tinyint(2) unsigned NOT NULL COMMENT 'Posición x en la cuadricula del almacén para la visualización del almacén',
                                 `posY` tinyint(2) unsigned NOT NULL COMMENT 'Posición y en la cuadricula del almacén para la visualización del almacén',
                                 `width` tinyint(2) unsigned NOT NULL COMMENT 'Ancho en celdas en la cuadricula del almacén para visualización almacén',
                                 `orientation` enum('horizontal','vertical') COLLATE utf8_bin NOT NULL COMMENT 'La orientación. Si es vertical, width indica el alto',
                                 `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                                 `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                 `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                 `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                                 UNIQUE KEY `name_UNIQUE` (`name`,`warehouse_id`),
                                 KEY `fk_storageracks_warehouses_idx` (`warehouse_id`),
                                 CONSTRAINT `fk_storageracks_warehouses` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Estanterias';

-- Table structure for table `alarms`
DROP TABLE IF EXISTS `alarms`;
CREATE TABLE `alarms` (
                          `type_alarm_id` int(11) unsigned NOT NULL,
                          `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                          `repetition` enum('immediate','daily','weekly','monthly') COLLATE utf8_bin NOT NULL,
                          `week_day` tinyint(1) unsigned DEFAULT NULL COMMENT 'Si la periodicidad es semanal, que dia de la semana se comprueba la alarma, 1-lunes 7-domingo',
                          `month_day` tinyint(2) unsigned DEFAULT NULL COMMENT 'Si la periodicidad es mensual, el día del mes que se comprueba la alarma',
                          `check_hour` varchar(5) COLLATE utf8_bin DEFAULT NULL COMMENT 'Hora para chequear la alarma. Inmediatly no la tiene en cuenta. 09:00',
                          `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                          `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                          `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                          `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                          PRIMARY KEY (`type_alarm_id`),
                          UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                          CONSTRAINT `fk_alarms_alarmstypes` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Alarmas';

-- Table structure for table `alarms_notifications`
DROP TABLE IF EXISTS `alarms_notifications`;
CREATE TABLE `alarms_notifications` (
                                        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                        `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                        `type_alarm_id` int(11) unsigned NOT NULL,
                                        `recipient_id` int(11) unsigned DEFAULT NULL,
                                        PRIMARY KEY (`id`),
                                        UNIQUE KEY `recipientid_typealarmid_UNIQUE` (`recipient_id`,`type_alarm_id`),
                                        KEY `fk_alarmsnotifications_alarms_idx` (`type_alarm_id`),
                                        KEY `fk_alarmsnotifications_alarmsrecipients_idx` (`recipient_id`),
                                        CONSTRAINT `fk_alarmsnotifications_alarms` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms` (`type_alarm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                        CONSTRAINT `fk_alarmsnotifications_alarmsrecipients` FOREIGN KEY (`recipient_id`) REFERENCES `alarms_recipients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Destinatarios de las notificaciones de las alarmas';

-- Table structure for table `products_warehouse`
DROP TABLE IF EXISTS `products_warehouse`;
CREATE TABLE `products_warehouse` (
                                      `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                      `product_id` int(11) unsigned NOT NULL,
                                      `warehouse_id` int(11) unsigned NOT NULL,
                                      `out_system_stock` tinyint(1) DEFAULT '0' COMMENT 'Si tiene stock de este producto que no lo controlan las basculas ',
                                      `days_average_consumption` int(3) unsigned DEFAULT '30' COMMENT 'Cuantos días atrás tiene en cuenta en el log de pesos para obtener el consumo medio',
                                      `days_serve_order` int(3) unsigned DEFAULT '0' COMMENT 'Cuantos días tarda un pedido en llegar desde que se realiza. Este campo lo iremos actualizando de forma dinámica',
                                      `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario que la creo',
                                      `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                      `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                      `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `productid_warehouseid_UNIQUE` (`product_id`,`warehouse_id`),
                                      KEY `fk_productswarehouses_warehouses_idx` (`warehouse_id`),
                                      KEY `fk_productswarehouses_products_idx` (`product_id`),
                                      CONSTRAINT `fk_productswarehouses_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                      CONSTRAINT `fk_productswarehouses_warehouses` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Configuración de los productos por cada almacén';

-- Table structure for table `alarms_log`
DROP TABLE IF EXISTS `alarms_log`;
CREATE TABLE `alarms_log` (
                              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                              `type_alarm_id` int(11) unsigned NOT NULL,
                              `product_id` int(11) unsigned DEFAULT NULL COMMENT 'Id del producto',
                              `scale_id` int(11) unsigned DEFAULT NULL COMMENT 'id de la balanza (cuando la alarma es de voltage)',
                              `date` datetime NOT NULL COMMENT 'Fecha / hora de la comprobación',
                              `weight` decimal(5,2) DEFAULT NULL COMMENT 'Peso en kilos registrados. Es la suma de kilos de todas las balanzas del producto',
                              `charge_percentage` decimal(5,2) DEFAULT NULL COMMENT 'Porcentaje de carga de la batería que dispara la alarma xxx.xx',
                              `voltage` decimal(5,2) DEFAULT NULL COMMENT 'Voltage de la batería que dispara la alarma xxx.xx',
                              `triggered` tinyint(1) NOT NULL COMMENT 'Si se disparó o no',
                              `notification_send_id` int(11) unsigned DEFAULT NULL COMMENT 'El id del envío de la notificación si se produjo',
                              PRIMARY KEY (`id`),
                              KEY `fk_alarmslog_alarms_idx` (`type_alarm_id`),
                              KEY `fk_alarmslog_alarmsnotificationssend_idx` (`notification_send_id`),
                              KEY `fk_alarmslog_products_idx` (`product_id`),
                              KEY `fk_alarmslog_scales_idx` (`scale_id`),
                              CONSTRAINT `fk_alarmslog_alarms` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms` (`type_alarm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                              CONSTRAINT `fk_alarmslog_alarmsnotificationssend` FOREIGN KEY (`notification_send_id`) REFERENCES `alarms_notifications_send` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
                              CONSTRAINT `fk_alarmslog_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                              CONSTRAINT `fk_alarmslog_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='Log de las comprobaciones realizadas';

-- Table structure for table `alarms_notifications_send`
DROP TABLE IF EXISTS `alarms_notifications_send`;
CREATE TABLE `alarms_notifications_send` (
                                             `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                             `type_alarm_id` int(11) unsigned DEFAULT NULL COMMENT 'id de la alarma (cuando la alarma es de usuario)',
                                             `product_id` int(11) unsigned DEFAULT NULL COMMENT 'Id del producto (cuando la alarma es de pesos)',
                                             `scale_id` int(11) unsigned DEFAULT NULL COMMENT 'id de la balanza (cuando la alarma es de voltage)',
                                             `key` varchar(100) COLLATE utf8_bin NOT NULL COMMENT 'Si es de usuario la concatenación de ids (tipo de alarma, producto y balanza). Si es de sistema la concatenación de una clave interna nuestra',
                                             `date` datetime NOT NULL COMMENT 'Fecha / hora de la comprobación',
                                             `recipients` varchar(2000) COLLATE utf8_bin NOT NULL COMMENT 'Los destinatarios (teléfonos, mails) a los que se envió el aviso',
                                             PRIMARY KEY (`id`),
                                             KEY `fk_alarmsnotificationssend_alarms_idx` (`type_alarm_id`),
                                             KEY `fk_alarmsnotificationssend_products_idx` (`product_id`),
                                             KEY `fk_alarmsnotificationssend_scales_idx` (`scale_id`),
                                             CONSTRAINT `fk_alarmsnotificationssend_alarms` FOREIGN KEY (`type_alarm_id`) REFERENCES `alarms` (`type_alarm_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                             CONSTRAINT `fk_alarmsnotificationssend_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                             CONSTRAINT `fk_alarmsnotificationssend_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Log de los envíos de notificaciones y a que destinatarios';

-- Table structure for table `weights_log`
DROP TABLE IF EXISTS `weights_log`;
CREATE TABLE `weights_log` (
                               `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id única para el registro',
                               `scale_id` int(11) unsigned NOT NULL,
                               `product_id` int(11) unsigned DEFAULT NULL,
                               `date` datetime NOT NULL COMMENT 'Fecha y hora de la pesada',
                               `real_weight` decimal(8,5) NOT NULL COMMENT 'Peso en kilos que devolvio la balda',
                               `adjust_weight` decimal(8,5) NOT NULL COMMENT 'Peso en kilos ajustado por el api',
                               `charge_percentage` decimal(5,2) NOT NULL COMMENT 'Porcentaje de carga de la batería xxx.xx',
                               `voltage` decimal(5,3) NOT NULL COMMENT 'Voltage de la batería xxx.xx',
                               PRIMARY KEY (`id`),
                               KEY `fk_weightslog_scales_idx` (`scale_id`),
                               KEY `fk_weightslog_products_idx` (`product_id`),
                               CONSTRAINT `fk_weightslog_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                               CONSTRAINT `fk_weightslog_scales` FOREIGN KEY (`scale_id`) REFERENCES `scales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Log de las pesadas';

-- Restore previous settings
SET TIME_ZONE = IF(@OLD_TIME_ZONE IS NULL, @@session.time_zone, @OLD_TIME_ZONE);
SET SQL_MODE = @OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS = @OLD_UNIQUE_CHECKS;
SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT;
SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS;
SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION;
SET SQL_NOTES = @OLD_SQL_NOTES;