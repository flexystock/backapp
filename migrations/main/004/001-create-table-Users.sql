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