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

-- ---------------------------------------------------
--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clients` (
                           `uuid` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                           `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nombre del cliente',
                           `server` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'El nombre del servidor donde esta alojada la bbdd, referenciado en servers.ini\nSi es exclusivo los datos de conexion en esta misma tabla en los campos exclusiveXXXX',
                           `exclusiveHost` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'Direción del host donde esta ubicada la base de datos cuando es única para el cliente',
                           `exclusiveUser` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'Usuario de acceso a la base de datos cuando es única para el cliente',
                           `exclusivePassword` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'Password de la base de datos cuando es única para el cliente',
                           `scheme` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'nombre del esquema de bbdd',
                           `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Si esta activo o no',
                           `ttnSecureByIp` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el registro de pesos a través de TTN tiene que comprobar ips seguras',
                           PRIMARY KEY (`uuid`),
                           UNIQUE KEY `name_UNIQUE` (`name`),
                           UNIQUE KEY `bbdd_UNIQUE` (`scheme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Relación de clientes	';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `countries` (
                             `iso2` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Código ISO2',
                             `iso3` varchar(5) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Código ISO3',
                             `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nombre',
                             `callingCode` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Prefijo de llamada internacional',
                             `translation_name_id` int(10) unsigned DEFAULT NULL,
                             PRIMARY KEY (`iso2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Paises';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `countries`
--

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
                               `id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                               `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                               `translation_name_id` int(10) unsigned DEFAULT NULL,
                               PRIMARY KEY (`id`),
                               UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lista de permisos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
                            `id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                            `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nombre',
                            `comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'Comentario aclaratorio',
                            `order` smallint(6) NOT NULL COMMENT 'Orden para mostrarla en el front',
                            `translation_name_id` int(10) unsigned DEFAULT NULL,
                            `translation_comment_id` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_spanish_ci COMMENT='Relación de las secciones para agrupar los permisos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

--
-- Table structure for table `features`
--

DROP TABLE IF EXISTS `features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `features` (
                            `id` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                            `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                            `comment` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
                            `onlyRoot` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si la caracteristica solo puede cambiarla en los perfiles un usuario root.',
                            `defaultValueId` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Valor por defecto',
                            `sectionId` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Seccion a la que pertenece el permiso',
                            `order` smallint(6) NOT NULL COMMENT 'Orden para mostrarla en el front',
                            `translation_name_id` int(10) unsigned DEFAULT NULL,
                            `translation_comment_id` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name_UNIQUE` (`name`),
                            KEY `fk_features_sections_idx` (`sectionId`),
                            KEY `fk_features_permissions_idx` (`defaultValueId`),
                            CONSTRAINT `fk_features_permissions` FOREIGN KEY (`defaultValueId`) REFERENCES `permissions` (`id`) ON UPDATE CASCADE,
                            CONSTRAINT `fk_features_sections` FOREIGN KEY (`sectionId`) REFERENCES `sections` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permisos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `features`
--

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
                         `uuid` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                         `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nombre completo',
                         `surnames` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                         `mail` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'email',
                         `pass` varchar(200) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Contraseña encriptada con md5',
                         `isRoot` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el usuario es root o no',
                         `isGhost` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si es un usuario fantasma. Cada cliente tiene uno, que genera automaticamente el propio back, para hacer peticiones sin que tenga nadie que logarse. Son peticiones internas del api',
                         `minutesSessionExpiration` int(11) DEFAULT '60' COMMENT 'Minutos de expiración del login. Si null, no expira',
                         `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Si el usuario esta o no activo',
                         `failedAttempts` smallint(6) NOT NULL DEFAULT '0' COMMENT 'Número de intentos fallidos de login',
                         `lockedUntil` datetime DEFAULT NULL COMMENT 'Si se supera el número de intentos de login, se bloquea la cuenta hasta una fecha / hora',
                         `lastAccess` datetime DEFAULT NULL COMMENT 'Fecha y hora del último intento de login',
                         `uuidUserCreation` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuairo que la creo',
                         `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                         `uuidUserModification` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
                         `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                         PRIMARY KEY (`uuid`),
                         UNIQUE KEY `mail_UNIQUE` (`mail`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Usuarios';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
                         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                         `uuidUser` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario',
                         `hash` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'hash de control',
                         `expiration` datetime NOT NULL COMMENT 'fecha y hora de expiración',
                         `remote_addr` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'ip desde la que se logo',
                         `http_user_agent` varchar(400) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'navegador que hizo la petición',
                         PRIMARY KEY (`id`),
                         UNIQUE KEY `hash_UNIQUE` (`hash`),
                         KEY `fk_login_users_idx` (`uuidUser`),
                         CONSTRAINT `fk_login_users` FOREIGN KEY (`uuidUser`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COMMENT='Control de logins';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login`
--

--
-- Table structure for table `client_secure_ips`
--

DROP TABLE IF EXISTS `client_secure_ips`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `client_secure_ips` (
                                     `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Id autoincremental',
                                     `uuid_client` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del cliente',
                                     `ip` varchar(15) COLLATE utf8_bin NOT NULL COMMENT 'ip válida',
                                     PRIMARY KEY (`id`),
                                     KEY `fk_clientsecureips_clients` (`uuid_client`),
                                     CONSTRAINT `fk_clientsecureips_clients` FOREIGN KEY (`uuid_client`) REFERENCES `clients` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Ips válidas para el registro de pesos si el cliente tiene activada la opción de seguridad por ip';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `client_secure_ips`
--

--
-- Table structure for table `profile_user_client`
--

DROP TABLE IF EXISTS `profile_user_client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile_user_client` (
                                       `profileId` int(10) unsigned NOT NULL,
                                       `uuidUser` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                       `uuidClient` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                       PRIMARY KEY (`profileId`,`uuidUser`,`uuidClient`),
                                       KEY `fk_profileuserclient_client_idx` (`uuidClient`),
                                       KEY `fk_profileuserclient_users_idx` (`uuidUser`),
                                       CONSTRAINT `fk_profileuserclient_client` FOREIGN KEY (`uuidClient`) REFERENCES `clients` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE,
                                       CONSTRAINT `fk_profileuserclient_profiles` FOREIGN KEY (`profileId`) REFERENCES `profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                       CONSTRAINT `fk_profileuserclient_users` FOREIGN KEY (`uuidUser`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla que relaciona que perfil tiene un usuario en un cliente';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile_user_client`
--

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profiles` (
                            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                            `system` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el perfil es de sistema. Sólo un root puede modificarlos',
                            `onlyRoot` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el perfil solo lo puede asignar a un usuario un root.',
                            `visible` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Si el perfil se devuelve en el profiles get',
                            `uuidClient` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT 'uuid del cliente al que pertenece el perfil. Si es un perfil de sistema, null',
                            `uuidUserCreation` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuairo que la creo',
                            `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                            `uuidUserModification` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
                            `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                            `translation_name_id` int(10) unsigned DEFAULT NULL,
                            PRIMARY KEY (`id`),
                            UNIQUE KEY `name_UNIQUE` (`name`,`uuidClient`),
                            KEY `fk_profiles_clients_idx` (`uuidClient`),
                            CONSTRAINT `fk_profiles_clients` FOREIGN KEY (`uuidClient`) REFERENCES `clients` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Perfiles';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profiles`
--

--
-- Table structure for table `system_scales`
--

DROP TABLE IF EXISTS `system_scales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_scales` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                 `uuid` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid',
                                 `serial_number` varchar(50) COLLATE utf8_bin NOT NULL COMMENT 'El número de serie del chip',
                                 `voltage_min` decimal(5,3) unsigned NOT NULL COMMENT 'Voltage mínimo que hará dispararse nuestra alarma interna',
                                 `uuidUserSketch` varchar(36) COLLATE utf8_bin DEFAULT NULL COMMENT 'uuid del último usuairo que descargo el sketch',
                                 `datehourSketch` datetime DEFAULT NULL COMMENT 'Fecha y hora de la última descarga del sketch',
                                 `send_frequency` int(11) unsigned NOT NULL DEFAULT '60' COMMENT 'Cada cuantos segundos envía datos la balanza',
                                 `last_send` datetime DEFAULT NULL COMMENT 'Fecha y hora del último envío de la bascula que llegó al api',
                                 `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Si esta activo o no',
                                 `uuid_client` varchar(36) COLLATE utf8_bin DEFAULT NULL COMMENT 'uuid',
                                 `uuidUserCreation` varchar(36) COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuairo que la creo',
                                 `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                                 `uuidUserModification` varchar(36) COLLATE utf8_bin DEFAULT NULL,
                                 `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `uuid_UNIQUE` (`uuid`),
                                 UNIQUE KEY `serial_number_UNIQUE` (`serial_number`),
                                 KEY `fk_systemscales_clients` (`uuid_client`),
                                 CONSTRAINT `fk_systemscales_clients` FOREIGN KEY (`uuid_client`) REFERENCES `clients` (`uuid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Balanzas del sistema';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_scales`
--

--
-- Table structure for table `translations`
--

DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `translations` (
                                `text_id` int(10) unsigned NOT NULL COMMENT 'Id del texto',
                                `table` varchar(50) COLLATE utf8_bin NOT NULL,
                                `language_id` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT 'EN' COMMENT 'Identificador ISO del idioma',
                                `text` varchar(1000) COLLATE utf8_bin NOT NULL COMMENT 'El texto traducido',
                                PRIMARY KEY (`text_id`,`table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Traducciones';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `translations`
--

--
-- Table structure for table `users_change_mail`
--

DROP TABLE IF EXISTS `users_change_mail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_change_mail` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id autoincremental',
                                     `uuidUser` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario',
                                     `hash` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'hash de control',
                                     `expiration` datetime NOT NULL COMMENT 'Fecha / hora de expiracion del enlace',
                                     `newMail` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                     PRIMARY KEY (`id`),
                                     KEY `fk_userschangemail_users_idx` (`uuidUser`),
                                     CONSTRAINT `fk_userschangemail_users` FOREIGN KEY (`uuidUser`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_change_mail`
--

--
-- Table structure for table `users_change_pass`
--

DROP TABLE IF EXISTS `users_change_pass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_change_pass` (
                                     `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id autoincremental',
                                     `uuidUser` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario',
                                     `hash` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'hash de control',
                                     `expiration` datetime NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `hash_UNIQUE` (`hash`),
                                     KEY `fk_userchangepass_users_idx` (`uuidUser`),
                                     CONSTRAINT `fk_userchangepass_users` FOREIGN KEY (`uuidUser`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='Tabla para controlar los cambios de password';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_change_pass`
--

--
-- Table structure for table `permissions_by_feature`
--

DROP TABLE IF EXISTS `permissions_by_feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions_by_feature` (
                                          `featureId` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                          `permissionId` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                          PRIMARY KEY (`featureId`,`permissionId`),
                                          KEY `fk_permissionsbyfeature_permissions_idx` (`permissionId`),
                                          CONSTRAINT `fk_permissionsbyfeature_features` FOREIGN KEY (`featureId`) REFERENCES `features` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                                          CONSTRAINT `fk_permissionsbyfeature_permissions` FOREIGN KEY (`permissionId`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permisos que puede tener una característica';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions_by_feature`
--

--
-- Table structure for table `permissions_by_profile`
--

DROP TABLE IF EXISTS `permissions_by_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions_by_profile` (
                                          `profileId` int(10) unsigned NOT NULL,
                                          `featureId` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                          `permissionId` varchar(20) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                          PRIMARY KEY (`profileId`,`featureId`,`permissionId`),
                                          KEY `fk_permissionsbyprofile_permissionsbyfeature_idx` (`featureId`,`permissionId`),
                                          CONSTRAINT `fk_permissionsbyprofile_permissionsbyfeature` FOREIGN KEY (`featureId`, `permissionId`) REFERENCES `permissions_by_feature` (`featureId`, `permissionId`) ON DELETE CASCADE ON UPDATE CASCADE,
                                          CONSTRAINT `fk_permissionsbyprofile_profiles` FOREIGN KEY (`profileId`) REFERENCES `profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Permisos por perfil';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions_by_profile`
--

--
-- Table structure for table `weight_units`
--

DROP TABLE IF EXISTS `weight_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `weight_units` (
                                `key` varchar(2) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
                                `relation_kg` float NOT NULL DEFAULT '1' COMMENT 'Relación de una unidad con un kilo',
                                `translation_name_id` int(10) unsigned NOT NULL COMMENT 'Unidades de peso relacionadas con client_config, key: weigthUnit',
                                PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Unidades de peso';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weight_units`
