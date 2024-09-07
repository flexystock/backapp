-- Table structure for table `business_group`
--

DROP TABLE IF EXISTS `business_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_group` (
                                  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                                  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'Grupo Empresarial',
                                  PRIMARY KEY (`id`)
);
/*!40101 SET character_set_client = @saved_cs_client */;