-- Table structure for table `login`
--

DROP TABLE IF EXISTS `login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login` (
                         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                         `uuidUser` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'uuid del usuario',
                         PRIMARY KEY (`id`),
                         KEY `fk_login_users_idx` (`uuidUser`),
                         CONSTRAINT `fk_login_users` FOREIGN KEY (`uuidUser`) REFERENCES `users` (`uuid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COMMENT='Control de logins';
/*!40101 SET character_set_client = @saved_cs_client */;
