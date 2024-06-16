DROP TABLE IF EXISTS `business_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `business_group` (
                         `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'Nombre completo',
                         `datehourCreation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
                         `datehourModification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación'

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Grupo Empresarial';
/*!40101 SET character_set_client = @saved_cs_client */;