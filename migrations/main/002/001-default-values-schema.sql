LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
INSERT INTO `clients` VALUES ('086c6014-f915-3114-abe1-83158a3a1795','Test1','server1',NULL,NULL,NULL,'dinaspain_v2_test1',1,0);
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `countries` WRITE;
/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` VALUES ('AD','AND','Andorra','376',6),('AE','ARE','Emiratos Árebes Unidos','971',234),('AF','AFG','Afganistán','93',1),('AG','ATG','Antigua y Barbuda','1+268',10),('AI','AIA','Anguilla','1+264',8),('AL','ALB','Albania','355',3),('AM','ARM','Armenia','374',12),('AO','AGO','Angola','244',7),('AQ','ATA','Antártida','672',9),('AR','ARG','Argentina','54',11),('AS','ASM','Samoa Americana','1+684',5),('AT','AUT','Austria','43',15),('AU','AUS','Australia','61',14),('AW','ABW','Aruba','297',13),('AX','ALA','Islas Áland','358',2),('AZ','AZE','Azerbaiyán','994',16),('BA','BIH','Bosnia-Herzegovina','387',29),('BB','BRB','Barbados','1+246',20),('BD','BGD','Bangladesh','880',19),('BE','BEL','Bélgica','32',22),('BF','BFA','Burkina Faso','226',36),('BG','BGR','Bulgaria','359',35),('BH','BHR','Bahrein','973',18),('BI','BDI','Burundi','257',37),('BJ','BEN','Benín','229',24),('BL','BLM','San Bartolomé','590',184),('BM','BMU','Bermudas','1+441',25),('BN','BRN','Brunei Darussalam','673',34),('BO','BOL','Bolivia','591',27),('BQ','BES','Caribe Neerlandés','599',28),('BR','BRA','Brasil','55',32),('BS','BHS','Bahamas','1+242',17),('BT','BTN','Bután','975',26),('BV','BVT','Isla Bouvet','NONE',31),('BW','BWA','Botswana','267',30),('BY','BLR','Bielorrusia','375',21),('BZ','BLZ','Belice','501',23),('CA','CAN','Canadá','1',40),('CC','CCK','Islas Cocos','61',48),('CD','COD','República Democrática del Congo','243',60),('CF','CAF','República Centroafricana','236',43),('CG','COG','República del Congo','242',51),('CH','CHE','Suiza','41',216),('CI','CIV','Costa de Marfil','225',54),('CK','COK','Islas Cook','682',52),('CL','CHL','Chile','56',45),('CM','CMR','Camerún','237',39),('CN','CHN','China','86',46),('CO','COL','Colombia','57',49),('CR','CRI','Costa Rica','506',53),('CU','CUB','Cuba','53',56),('CV','CPV','Cabo Verde','238',41),('CW','CUW','Curazao','599',57),('CX','CXR','Isla de Navidad, Isla Christmas','61',47),('CY','CYP','Chipre','357',58),('CZ','CZE','República Checa','420',59),('DE','DEU','Alemania','49',83),('DJ','DJI','Djibouti','253',62),('DK','DNK','Dinamarca','45',61),('DM','DMA','Dominica','1+767',63),('DO','DOM','República Dominicana','1+809, 8',64),('DZ','DZA','Argelia','213',4),('EC','ECU','Ecuador','593',65),('EE','EST','Estonia','372',70),('EG','EGY','Egiipto','20',66),('EH','ESH','Sáhara Occidental','212',247),('ER','ERI','Eritrea','291',69),('ES','ESP','España','34',209),('ET','ETH','Etiopía','251',71),('FI','FIN','Finlandia','358',75),('FJ','FJI','Fiyi','679',74),('FK','FLK','Islas Malvinas','500',72),('FM','FSM','Micronesia','691',143),('FO','FRO','Islas Feroe','298',73),('FR','FRA','Francia','33',76),('GA','GAB','Gabón','241',80),('GB','GBR','Reino Unido','44',235),('GD','GRD','Granada','1+473',88),('GE','GEO','Georgia','995',82),('GF','GUF','Guayana Francesa','594',77),('GG','GGY','Guernsey','44',92),('GH','GHA','Ghana','233',84),('GI','GIB','Gibraltar','350',85),('GL','GRL','Groenlandia','299',87),('GM','GMB','Gambia','220',81),('GN','GIN','República Guinea','224',93),('GP','GLP','Guadalupe','590',89),('GQ','GNQ','Guinea Ecuatorial','240',68),('GR','GRC','Grecia','30',86),('GS','SGS','Sudo Georgia y los Islas Sandwich del Sur','500',206),('GT','GTM','Guatemala','502',91),('GU','GUM','Guam','1+671',90),('GW','GNB','Guinea Bissau','245',94),('GY','GUY','Guyana','592',95),('HK','HKG','Hong Kong','852',99),('HM','HMD','Islas de Heard y McDonald','NONE',97),('HN','HND','Honduras','504',98),('HR','HRV','Croacia','385',55),('HT','HTI','Haiti','509',96),('HU','HUN','Hungría','36',100),('ID','IDN','Indonesia','62',103),('IE','IRL','Irlanda','353',106),('IL','ISR','Israel','972',108),('IM','IMN','Isla Man','44',107),('IN','IND','India','91',102),('IO','IOT','Territorio Británico del Océano Indico','246',33),('IQ','IRQ','Iraq','964',105),('IR','IRN','Irán','98',104),('IS','ISL','Islandia','354',101),('IT','ITA','Italia','39',109),('JE','JEY','Jersey','44',112),('JM','JAM','Jamaica','1+876',110),('JO','JOR','Jordania','962',113),('JP','JPN','Japón','81',111),('KE','KEN','Kenia','254',115),('KG','KGZ','Kirguistán','996',119),('KH','KHM','Camboya','855',38),('KI','KIR','Kiribati','686',116),('KM','COM','Comores','269',50),('KN','KNA','San Cristobal y Nevis','1+869',186),('KP','KOR','Corea del Norte','850',163),('KR','PRK','Corea del Sur','82',207),('KW','KWT','Kuwait','965',118),('KY','CYM','Islas Caimán','1+345',42),('KZ','KAZ','Kazajstán','7',114),('LA','LAO','Laos','856',120),('LB','LBN','Líbano','961',122),('LC','LCA','Santa Lucía','1+758',187),('LI','LIE','Liechtenstein','423',126),('LK','LKA','Sri Lanka','94',210),('LR','LBR','Liberia','231',124),('LS','LSO','Lesotho','266',123),('LT','LTU','Lituania','370',127),('LU','LUX','Luxemburgo','352',128),('LV','LVA','Letonia','371',121),('LY','LBY','Libia','218',125),('MA','MAR','Marruecos','212',149),('MC','MCO','Mónaco','377',145),('MD','MDA','Modavia','373',144),('ME','MNE','Montenegro','382',147),('MF','MAF','San Martín','590',188),('MG','MDG','Madagascar','261',131),('MH','MHL','Islas Marshall','692',137),('MK','MKD','Macedonia','389',130),('ML','MLI','Malí','223',135),('MM','MMR','Myanmar (Birmania)','95',151),('MN','MNG','Mongolia','976',146),('MO','MAC','Macao','853',129),('MP','MNP','Marianas del Norte','1+670',164),('MQ','MTQ','Martinica','596',138),('MR','MRT','Mauritania','222',139),('MS','MSR','Montserrat','1+664',148),('MT','MLT','Malta','356',136),('MU','MUS','Mauricio','230',140),('MV','MDV','Maldivas','960',134),('MW','MWI','Malawi','265',132),('MX','MEX','México','52',142),('MY','MYS','Malasia','60',133),('MZ','MOZ','Mozambique','258',150),('NA','NAM','Namibia','264',152),('NC','NCL','Nueva Caledonia','687',156),('NE','NER','Niger','227',159),('NF','NFK','Norfolk Island','672',162),('NG','NGA','Nigeria','234',160),('NI','NIC','Nicaragua','505',158),('NL','NLD','Países Bajos, Holanda','31',155),('NO','NOR','Noruega','47',165),('NP','NPL','Nepal','977',154),('NR','NRU','Nauru','674',153),('NU','NIU','Niue','683',161),('NZ','NZL','Nueva Zelanda','64',157),('OM','OMN','Omán','968',166),('PA','PAN','Panamá','507',170),('PE','PER','Perú','51',173),('PF','PYF','Polinesia Francesa','689',78),('PG','PNG','Papúa-Nueva Guinea','675',171),('PH','PHL','Filipinas','63',174),('PK','PAK','Pakistán','92',167),('PL','POL','Polonia','48',176),('PM','SPM','San Pedro y Miquelón','508',189),('PN','PCN','Isla Pitcairn','NONE',175),('PR','PRI','Puerto Rico','1+939',178),('PS','PSE','Palestina','970',169),('PT','PRT','Portugal','351',177),('PW','PLW','Palau','680',168),('PY','PRY','Paraguay','595',172),('QA','QAT','Qatar','974',179),('RE','REU','Reunión','262',180),('RO','ROU','Rumania','40',181),('RS','SRB','Serbia','381',196),('RU','RUS','Federación Rusa','7',182),('RW','RWA','Ruanda','250',183),('SA','SAU','Arabia Saudita','966',194),('SB','SLB','Islas Salomón','677',203),('SC','SYC','Seychelles','248',197),('SD','SDN','Sudán','249',211),('SE','SWE','Suecia','46',215),('SG','SGP','Singapur','65',199),('SH','SHN','Santa Elena','290',185),('SI','SVN','Eslovenia','386',202),('SJ','SJM','Isla Jan Mayen y Archipiélago de Svalbard','47',213),('SK','SVK','Eslovaquia','421',201),('SL','SLE','Sierra Leona','232',198),('SM','SMR','San Marino','378',192),('SN','SEN','Senegal','221',195),('SO','SOM','Somalia','252',204),('SR','SUR','Surinam','597',212),('SS','SSD','Sudán del Sur','211',208),('ST','STP','San Tomé y Principe','239',193),('SV','SLV','El Salvador','503',67),('SX','SXM','Sint Maarten','1+721',200),('SY','SYR','Siria','963',217),('SZ','SWZ','Swazilandia','268',214),('TC','TCA','Islas Turcas y Caicos','1+649',230),('TD','TCD','Chad','235',44),('TF','ATF','Tierras Australes Frencesas','NONE',79),('TG','TGO','Togo','228',223),('TH','THA','Tailandia','66',221),('TJ','TJK','Tadjikistan','992',219),('TK','TKL','Tokelau','690',224),('TL','TLS','Timor Oriental','670',222),('TM','TKM','Turkmenistan','993',229),('TN','TUN','Túez','216',227),('TO','TON','Tonga','676',225),('TR','TUR','Turquía','90',228),('TT','TTO','Trinidad y Tobago','1+868',226),('TV','TUV','Tuvalu','688',231),('TW','TWN','Taiwan','886',218),('TZ','TZA','Tanzania','255',220),('UA','UKR','Ucrania','380',233),('UG','UGA','Uganda','256',232),('UM','UMI','Islas ultramarinas menores de Estados Unidos','NONE',237),('US','USA','Estados Unidos','1',236),('UY','URY','Uruguay','598',238),('UZ','UZB','Uzbekistán','998',239),('VA','VAT','Ciudad del Vaticano','39',241),('VC','VCT','San Vincente y Granadinas','1+784',190),('VE','VEN','Venezuela','58',242),('VG','VGB','Islas Virgenes Británicas','1+284',244),('VI','VIR','Islas Virgenes Americanas','1+340',245),('VN','VNM','Vietnam','84',243),('VU','VUT','Vanuatu','678',240),('WF','WLF','Wallis y Futuna','681',246),('WS','WSM','Samoa','685',191),('XK','UNK','Kosovo','381',117),('YE','YEM','Yemen','967',248),('YT','MYT','Mayotte','262',141),('ZA','ZAF','Sudáfrica','27',205),('ZM','ZMB','Zambia','260',249),('ZW','ZWE','Zimbabwe','263',250);
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;
UNLOCK TABLES;

-- ---------------------------
LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES ('all','Todos',1),('allow','Permitido',2),('none','Sin acceso',3),('notAllow','No permitido',4),('onlyMine','Solo los mios',5),('read','Lectura',6),('write','Lectura y escritura',7);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES ('alarms','Alarmas','Permisos relacionados con las alarmas',5,11,12),('chartConfig','Gráficos','Permisos relacionados con la configuración de los gráficos',6,13,14),('clientScales','Balanzas del cliente','Permisos relacionados con las balanzas del cliente',4,3,4),('modules','Acceso a los módulos','Permisos para poder acceder o no a los distintos módulos',1,1,2),('users','Usuarios','Permisos del módulo de usuarios',7,5,6),('warehouses','Almacenes','Permisos relacionados con los almacenes',2,7,8);
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `features` WRITE;
/*!40000 ALTER TABLE `features` DISABLE KEYS */;
INSERT INTO `features` VALUES ('alarms_create','Crear alarmas',NULL,0,'notAllow','alarms',2,1,NULL),('alarms_delete','Borrar alarmas',NULL,0,'notAllow','alarms',4,2,NULL),('alarms_modify','Modificar alarmas',NULL,0,'notAllow','alarms',3,3,NULL),('clientConfig_modify','Modificar la configuración general',NULL,0,'notAllow','users',4,21,NULL),('configScales','Configuración de balanzas y productos',NULL,0,'notAllow','clientScales',1,25,NULL),('del_in_other_clients','Si puede borrar o desactivar el usuario estando en otros clientes ',NULL,1,'notAllow','users',2,4,NULL),('entity_users','Acceso a los usuarios',NULL,1,'none','users',1,5,NULL),('module_alarms','Módulo de alarmas',NULL,0,'notAllow','modules',3,17,NULL),('module_clientConfig','Módulo de configuración',NULL,0,'notAllow','modules',7,20,NULL),('module_myAccount','Módulo de mi cuenta',NULL,0,'allow','modules',4,6,NULL),('module_products','Módulo de productos',NULL,0,'notAllow','modules',2,24,NULL),('module_profiles','Módulo de perfiles',NULL,1,'notAllow','modules',5,7,NULL),('module_systemScales','Módulo de balanzas del sistema',NULL,1,'notAllow','modules',8,23,NULL),('module_users','Módulo de usuarios',NULL,1,'notAllow','modules',6,8,NULL),('module_warehouses','Módulo de almacenes',NULL,0,'allow','modules',1,9,NULL),('session_not_expired','Si puede definir que la sesion del usuario no expire',NULL,1,'notAllow','users',2,10,NULL),('warehouses_create','Crear almacenes',NULL,0,'notAllow','warehouses',1,11,NULL),('warehouses_delete','Borrar almacenes',NULL,0,'notAllow','warehouses',3,12,NULL),('warehouses_modify','Modificar almacenes',NULL,0,'notAllow','warehouses',2,13,NULL);
/*!40000 ALTER TABLE `features` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('205b2031-c4e8-3e42-8d57-7eccf724ca86','Ghost','086c6014-f915-3114-abe1-83158a3a1795','086c6014-f915-3114-abe1-83158a3a1795@dinaspain.com','7727f253ca675f103954434895443653224bd00f7727f253ca',0,1,NULL,1,0,NULL,NULL,'b22214ef-5d77-3d65-91df-9780528d65d2','2022-12-09 04:30:19',NULL,NULL),('b22214ef-5d77-3d65-91df-9780528d65d2','Alberto','Blanco','alberto.blanco@dinaspain.com','da9ea52de24db9d6fd02cb1bff027208',1,0,NULL,1,0,NULL,'2023-10-04 05:59:18','b22214ef-5d77-3d65-91df-9780528d65d2','2020-01-01 09:00:00','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-14 04:07:59'),('c5513c14-b32f-35cc-83cf-85dcf499443f','Soporte','Dinaspain','soporte@dinaspain.com','da9ea52de24db9d6fd02cb1bff027208',0,0,30,1,0,NULL,'2022-11-14 14:49:05','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-05 03:12:07','c5513c14-b32f-35cc-83cf-85dcf499443f','2022-11-14 04:29:54');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `login` WRITE;
/*!40000 ALTER TABLE `login` DISABLE KEYS */;
INSERT INTO `login` VALUES (64,'b22214ef-5d77-3d65-91df-9780528d65d2','88f77de298af573e53b905e5d5cc07e7','2032-11-11 04:57:26','127.0.0.1','PostmanRuntime/7.29.2'),(71,'b22214ef-5d77-3d65-91df-9780528d65d2','923c197795518989a9f89b4463aaa0b2','2032-12-03 04:46:35','127.0.0.1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Mobile Safari/537.36'),(72,'b22214ef-5d77-3d65-91df-9780528d65d2','2c3cf7991f62f487e69a0cea8d252fa2','2033-01-07 09:23:13','127.0.0.1','PostmanRuntime/7.30.0'),(73,'b22214ef-5d77-3d65-91df-9780528d65d2','fd582b37e0b51d0fd7ceb06d2c25f5df','2033-04-12 06:23:54','127.0.0.1','PostmanRuntime/7.31.3'),(75,'b22214ef-5d77-3d65-91df-9780528d65d2','bc23e01e45b31e622c11e67634267acc','2033-09-19 03:56:58','127.0.0.1','PostmanRuntime/7.33.0'),(82,'b22214ef-5d77-3d65-91df-9780528d65d2','178efc1f696bd52536d82947e5146744','2033-09-23 05:33:13','127.0.0.1','Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Mobile Safari/537.36'),(86,'b22214ef-5d77-3d65-91df-9780528d65d2','c1b2598bebde30b419eabd6243cfa0bd','2033-10-02 05:32:20','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36'),(87,'b22214ef-5d77-3d65-91df-9780528d65d2','7e4510f12ff7bbf6afad235b8cb0e036','2033-10-04 05:59:18','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36');
/*!40000 ALTER TABLE `login` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `profile_user_client` WRITE;
/*!40000 ALTER TABLE `profile_user_client` DISABLE KEYS */;
INSERT INTO `profile_user_client` VALUES (1,'205b2031-c4e8-3e42-8d57-7eccf724ca86','086c6014-f915-3114-abe1-83158a3a1795'),(2,'c5513c14-b32f-35cc-83cf-85dcf499443f','086c6014-f915-3114-abe1-83158a3a1795');
/*!40000 ALTER TABLE `profile_user_client` ENABLE KEYS */;
UNLOCK TABLES;

-- ----------------------------
LOCK TABLES `profiles` WRITE;
/*!40000 ALTER TABLE `profiles` DISABLE KEYS */;
INSERT INTO `profiles` VALUES (1,'Root',1,1,1,NULL,'b22214ef-5d77-3d65-91df-9780528d65d2','2020-03-01 10:00:00',NULL,NULL,1),(2,'Administrador',1,0,1,NULL,'b22214ef-5d77-3d65-91df-9780528d65d2','2020-03-01 10:00:00',NULL,NULL,2),(3,'Usuario',1,0,1,NULL,'b22214ef-5d77-3d65-91df-9780528d65d2','2020-03-01 10:00:00',NULL,NULL,3),(4,'Solo consultas',1,0,1,NULL,'b22214ef-5d77-3d65-91df-9780528d65d2','2020-03-01 10:00:00',NULL,NULL,4),(5,'Administrador panel de control',1,0,1,NULL,'b22214ef-5d77-3d65-91df-9780528d65d2','2020-03-01 10:00:00',NULL,NULL,5);
/*!40000 ALTER TABLE `profiles` ENABLE KEYS */;
UNLOCK TABLES;

-- -----------------------------
LOCK TABLES `system_scales` WRITE;
/*!40000 ALTER TABLE `system_scales` DISABLE KEYS */;
INSERT INTO `system_scales` VALUES (1,'2fc92ab5-3cfe-4f1d-ad03-3ba684c94aaa','01-A',3.400,NULL,NULL,60,'2022-12-10 10:13:06',1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:11','b22214ef-5d77-3d65-91df-9780528d65d2','2022-12-09 04:00:51'),(2,'26d566e5-69eb-3a00-9a0a-7b716291bf94','01-B',3.400,NULL,NULL,60,'2022-12-09 05:36:33',1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:16',NULL,NULL),(3,'fa5d982e-90d1-3822-8443-16bb17038afa','01-C',3.400,NULL,NULL,60,'2022-11-24 08:34:42',1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:21',NULL,NULL),(4,'98ce0ac8-1a26-3522-80ba-3cce839fd90c','01-D',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:38',NULL,NULL),(5,'d1389e05-d2e2-311b-9879-d410e9156428','01-E',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:43',NULL,NULL),(6,'2c305aee-ce8a-3205-a8d7-e276702b90f3','01-F',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:49',NULL,NULL),(7,'1861c80c-8d16-4452-89b3-2ee572d6ac4e','01-G',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-11 03:31:49',NULL,NULL),(8,'1bfac3bf-1a69-305c-aa85-2eba53a9572e','01-H',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:01',NULL,NULL),(9,'1b4f77d0-2c96-39c8-b6a1-538deb53fe53','01-I',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:06',NULL,NULL),(10,'23db9c73-1553-3898-a288-98894cc55f46','01-J',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:12',NULL,NULL),(11,'08b96aa4-52e6-3f9d-b747-643366866baa','01-K',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:17',NULL,NULL),(12,'d93f9e6b-0729-3cc2-a847-00f514136db4','01-L',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:21',NULL,NULL),(13,'942f93c9-39f0-3eb5-a851-342db72e8b9b','01-M',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:27',NULL,NULL),(14,'d13e7709-7680-3f5e-931e-7f512c1a2834','01-N',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:31',NULL,NULL),(15,'2dc22fad-bd75-3caf-8ffa-01d2e7c9c818','01-O',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:36',NULL,NULL),(16,'d61a4f71-4d3c-3b5f-8e2c-9bbe3cf4c979','01-P',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:40',NULL,NULL),(17,'1d775816-9ed2-3365-b38f-ad8161ca0d60','01-Q',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:45',NULL,NULL),(18,'ae6171ac-c288-3a3d-bee8-beb12f99497e','01-R',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:49',NULL,NULL),(19,'a76220cb-c161-3eae-8945-8591ee961b9b','01-S',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:54',NULL,NULL),(20,'f114fa15-a974-3608-b060-612c7bbb2066','01-T',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:48:59',NULL,NULL),(21,'3925353b-179e-393d-b79c-23cd74ded946','01-U',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:49:14',NULL,NULL),(22,'3724dd2e-4778-3241-a7ae-1a9fd508e115','01-V',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:49:18',NULL,NULL),(23,'c155e637-1eea-33a8-90c0-67a9064c8439','01-W',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:49:22',NULL,NULL),(24,'a1b46e50-87ca-34d9-bc8e-124519bb22fe','01-X',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:49:27',NULL,NULL),(25,'2d1af50c-32ba-37c2-ac1e-a9727dc3a941','01-Y',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:49:32',NULL,NULL),(26,'609ed10c-ee36-3596-b8ca-80884488dc20','01-Z',3.400,NULL,NULL,60,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-17 03:49:37',NULL,NULL),(29,'ddeb6051-c8ec-3947-b545-7ca4aa59afd3','02-A',2.100,NULL,NULL,60,'2022-11-22 15:34:13',1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-21 07:29:56','b22214ef-5d77-3d65-91df-9780528d65d2','2022-11-21 07:41:44'),(30,'ad90e115-673d-3ee1-af63-75f49d0db427','002-A',2.100,'b22214ef-5d77-3d65-91df-9780528d65d2','2023-09-19 04:40:58',25,NULL,1,'086c6014-f915-3114-abe1-83158a3a1795','b22214ef-5d77-3d65-91df-9780528d65d2','2022-12-10 09:01:17','b22214ef-5d77-3d65-91df-9780528d65d2','2022-12-10 09:01:52');
/*!40000 ALTER TABLE `system_scales` ENABLE KEYS */;
UNLOCK TABLES;

-- -----------------------------
LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
INSERT INTO `translations` VALUES (1,'countries','EN','Afghanistan'),(1,'features','EN','Create alarms'),(1,'permissions','EN','Everyone'),(1,'profiles','EN','Root'),(1,'sections','EN','Access to modules'),(1,'weight_units','EN','Kilos'),(2,'countries','EN','Aland Islands'),(2,'features','EN','Delete alarms'),(2,'permissions','EN','Permitted'),(2,'profiles','EN','Administrator'),(2,'sections','EN','Permissions to be able to access or not the different modules'),(2,'weight_units','EN','Pounds'),(3,'countries','EN','Albania'),(3,'features','EN','Modify alarms'),(3,'permissions','EN','No access'),(3,'profiles','EN','User'),(3,'sections','EN','Client scales'),(3,'weight_units','EN','Ounces'),(4,'countries','EN','Algeria'),(4,'features','EN','If you can delete or deactivate the user while in other clients'),(4,'permissions','EN','Not allowed'),(4,'profiles','EN','Inquiries only'),(4,'sections','EN','Client scales related permissions'),(5,'countries','EN','American Samoa'),(5,'features','EN','Access to users'),(5,'permissions','EN','Only mine'),(5,'profiles','EN','Admin dashboard'),(5,'sections','EN','Users'),(6,'countries','EN','Andorra'),(6,'features','EN','My account module'),(6,'permissions','EN','Reading'),(6,'sections','EN','Users module permissions'),(7,'countries','EN','Angola'),(7,'features','EN','Profile module'),(7,'permissions','EN','Reading and writing'),(7,'sections','EN','Warehouses'),(8,'countries','EN','Anguilla'),(8,'features','EN','User module'),(8,'sections','EN','Warehouse related permits'),(9,'countries','EN','Antarctica'),(9,'features','EN','Warehouse module'),(10,'countries','EN','Antigua and Barbuda'),(10,'features','EN','If you can define that the user session does not expire'),(11,'countries','EN','Argentina'),(11,'features','EN','Create warehouses'),(11,'sections','EN','Alarms'),(12,'countries','EN','Armenia'),(12,'features','EN','Delete warehouses'),(12,'sections','EN','Alarms related permissions '),(13,'countries','EN','Aruba'),(13,'features','EN','Modify warehouses'),(13,'sections','EN','Graphics'),(14,'countries','EN','Australia'),(14,'sections','EN','Graphics settings related permissions '),(15,'countries','EN','Austria'),(16,'countries','EN','Azerbaijan'),(17,'countries','EN','Bahamas'),(17,'features','EN','Alarms module'),(18,'countries','EN','Bahrain'),(19,'countries','EN','Bangladesh'),(20,'countries','EN','Barbados'),(20,'features','EN','Config module'),(21,'countries','EN','Belarus'),(21,'features','EN','Modify general settings'),(22,'countries','EN','Belgium'),(23,'countries','EN','Belize'),(23,'features','EN','System scales module'),(24,'countries','EN','Benin'),(24,'features','EN','Products module'),(25,'countries','EN','Bermuda'),(25,'features','EN','Configuration of scales and products'),(26,'countries','EN','Bhutan'),(27,'countries','EN','Bolivia'),(28,'countries','EN','Bonaire, Sint Eustatius and Saba'),(29,'countries','EN','Bosnia and Herzegovina'),(30,'countries','EN','Botswana'),(31,'countries','EN','Bouvet Island'),(32,'countries','EN','Brazil'),(33,'countries','EN','British Indian Ocean Territory'),(34,'countries','EN','Brunei'),(35,'countries','EN','Bulgaria'),(36,'countries','EN','Burkina Faso'),(37,'countries','EN','Burundi'),(38,'countries','EN','Cambodia'),(39,'countries','EN','Cameroon'),(40,'countries','EN','Canada'),(41,'countries','EN','Cape Verde'),(42,'countries','EN','Cayman Islands'),(43,'countries','EN','Central African Republic'),(44,'countries','EN','Chad'),(45,'countries','EN','Chile'),(46,'countries','EN','China'),(47,'countries','EN','Christmas Island'),(48,'countries','EN','Cocos (Keeling) Islands'),(49,'countries','EN','Colombia'),(50,'countries','EN','Comoros'),(51,'countries','EN','Congo'),(52,'countries','EN','Cook Islands'),(53,'countries','EN','Costa Rica'),(54,'countries','EN','Cote d\'ivoire (Ivory Coast)'),(55,'countries','EN','Croatia'),(56,'countries','EN','Cuba'),(57,'countries','EN','Curacao'),(58,'countries','EN','Cyprus'),(59,'countries','EN','Czech Republic'),(60,'countries','EN','Democratic Republic of the Congo'),(61,'countries','EN','Denmark'),(62,'countries','EN','Djibouti'),(63,'countries','EN','Dominica'),(64,'countries','EN','Dominican Republic'),(65,'countries','EN','Ecuador'),(66,'countries','EN','Egypt'),(67,'countries','EN','El Salvador'),(68,'countries','EN','Equatorial Guinea'),(69,'countries','EN','Eritrea'),(70,'countries','EN','Estonia'),(71,'countries','EN','Ethiopia'),(72,'countries','EN','Falkland Islands (Malvinas)'),(73,'countries','EN','Faroe Islands'),(74,'countries','EN','Fiji'),(75,'countries','EN','Finland'),(76,'countries','EN','France'),(77,'countries','EN','French Guiana'),(78,'countries','EN','French Polynesia'),(79,'countries','EN','French Southern Territories'),(80,'countries','EN','Gabon'),(81,'countries','EN','Gambia'),(82,'countries','EN','Georgia'),(83,'countries','EN','Germany'),(84,'countries','EN','Ghana'),(85,'countries','EN','Gibraltar'),(86,'countries','EN','Greece'),(87,'countries','EN','Greenland'),(88,'countries','EN','Grenada'),(89,'countries','EN','Guadaloupe'),(90,'countries','EN','Guam'),(91,'countries','EN','Guatemala'),(92,'countries','EN','Guernsey'),(93,'countries','EN','Guinea'),(94,'countries','EN','Guinea-Bissau'),(95,'countries','EN','Guyana'),(96,'countries','EN','Haiti'),(97,'countries','EN','Heard Island and McDonald Islands'),(98,'countries','EN','Honduras'),(99,'countries','EN','Hong Kong'),(100,'countries','EN','Hungary'),(101,'countries','EN','Iceland'),(102,'countries','EN','India'),(103,'countries','EN','Indonesia'),(104,'countries','EN','Iran'),(105,'countries','EN','Iraq'),(106,'countries','EN','Ireland'),(107,'countries','EN','Isle of Man'),(108,'countries','EN','Israel'),(109,'countries','EN','Italy'),(110,'countries','EN','Jamaica'),(111,'countries','EN','Japan'),(112,'countries','EN','Jersey'),(113,'countries','EN','Jordan'),(114,'countries','EN','Kazakhstan'),(115,'countries','EN','Kenya'),(116,'countries','EN','Kiribati'),(117,'countries','EN','Kosovo'),(118,'countries','EN','Kuwait'),(119,'countries','EN','Kyrgyzstan'),(120,'countries','EN','Laos'),(121,'countries','EN','Latvia'),(122,'countries','EN','Lebanon'),(123,'countries','EN','Lesotho'),(124,'countries','EN','Liberia'),(125,'countries','EN','Libya'),(126,'countries','EN','Liechtenstein'),(127,'countries','EN','Lithuania'),(128,'countries','EN','Luxembourg'),(129,'countries','EN','Macao'),(130,'countries','EN','Macedonia'),(131,'countries','EN','Madagascar'),(132,'countries','EN','Malawi'),(133,'countries','EN','Malaysia'),(134,'countries','EN','Maldives'),(135,'countries','EN','Mali'),(136,'countries','EN','Malta'),(137,'countries','EN','Marshall Islands'),(138,'countries','EN','Martinique'),(139,'countries','EN','Mauritania'),(140,'countries','EN','Mauritius'),(141,'countries','EN','Mayotte'),(142,'countries','EN','Mexico'),(143,'countries','EN','Micronesia'),(144,'countries','EN','Moldava'),(145,'countries','EN','Monaco'),(146,'countries','EN','Mongolia'),(147,'countries','EN','Montenegro'),(148,'countries','EN','Montserrat'),(149,'countries','EN','Morocco'),(150,'countries','EN','Mozambique'),(151,'countries','EN','Myanmar (Burma)'),(152,'countries','EN','Namibia'),(153,'countries','EN','Nauru'),(154,'countries','EN','Nepal'),(155,'countries','EN','Netherlands'),(156,'countries','EN','New Caledonia'),(157,'countries','EN','New Zealand'),(158,'countries','EN','Nicaragua'),(159,'countries','EN','Niger'),(160,'countries','EN','Nigeria'),(161,'countries','EN','Niue'),(162,'countries','EN','Norfolk Island'),(163,'countries','EN','North Korea'),(164,'countries','EN','Northern Mariana Islands'),(165,'countries','EN','Norway'),(166,'countries','EN','Oman'),(167,'countries','EN','Pakistan'),(168,'countries','EN','Palau'),(169,'countries','EN','Palestine'),(170,'countries','EN','Panama'),(171,'countries','EN','Papua New Guinea'),(172,'countries','EN','Paraguay'),(173,'countries','EN','Peru'),(174,'countries','EN','Phillipines'),(175,'countries','EN','Pitcairn'),(176,'countries','EN','Poland'),(177,'countries','EN','Portugal'),(178,'countries','EN','Puerto Rico'),(179,'countries','EN','Qatar'),(180,'countries','EN','Reunion'),(181,'countries','EN','Romania'),(182,'countries','EN','Russia'),(183,'countries','EN','Rwanda'),(184,'countries','EN','Saint Barthelemy'),(185,'countries','EN','Saint Helena'),(186,'countries','EN','Saint Kitts and Nevis'),(187,'countries','EN','Saint Lucia'),(188,'countries','EN','Saint Martin'),(189,'countries','EN','Saint Pierre and Miquelon'),(190,'countries','EN','Saint Vincent and the Grenadines'),(191,'countries','EN','Samoa'),(192,'countries','EN','San Marino'),(193,'countries','EN','Sao Tome and Principe'),(194,'countries','EN','Saudi Arabia'),(195,'countries','EN','Senegal'),(196,'countries','EN','Serbia'),(197,'countries','EN','Seychelles'),(198,'countries','EN','Sierra Leone'),(199,'countries','EN','Singapore'),(200,'countries','EN','Sint Maarten'),(201,'countries','EN','Slovakia'),(202,'countries','EN','Slovenia'),(203,'countries','EN','Solomon Islands'),(204,'countries','EN','Somalia'),(205,'countries','EN','South Africa'),(206,'countries','EN','South Georgia and the South Sandwich Islands'),(207,'countries','EN','South Korea'),(208,'countries','EN','South Sudan'),(209,'countries','EN','Spain'),(210,'countries','EN','Sri Lanka'),(211,'countries','EN','Sudan'),(212,'countries','EN','Suriname'),(213,'countries','EN','Svalbard and Jan Mayen'),(214,'countries','EN','Swaziland'),(215,'countries','EN','Sweden'),(216,'countries','EN','Switzerland'),(217,'countries','EN','Syria'),(218,'countries','EN','Taiwan'),(219,'countries','EN','Tajikistan'),(220,'countries','EN','Tanzania'),(221,'countries','EN','Thailand'),(222,'countries','EN','Timor-Leste (East Timor)'),(223,'countries','EN','Togo'),(224,'countries','EN','Tokelau'),(225,'countries','EN','Tonga'),(226,'countries','EN','Trinidad and Tobago'),(227,'countries','EN','Tunisia'),(228,'countries','EN','Turkey'),(229,'countries','EN','Turkmenistan'),(230,'countries','EN','Turks and Caicos Islands'),(231,'countries','EN','Tuvalu'),(232,'countries','EN','Uganda'),(233,'countries','EN','Ukraine'),(234,'countries','EN','United Arab Emirates'),(235,'countries','EN','United Kingdom'),(236,'countries','EN','United States'),(237,'countries','EN','United States Minor Outlying Islands'),(238,'countries','EN','Uruguay'),(239,'countries','EN','Uzbekistan'),(240,'countries','EN','Vanuatu'),(241,'countries','EN','Vatican City'),(242,'countries','EN','Venezuela'),(243,'countries','EN','Vietnam'),(244,'countries','EN','Virgin Islands, British'),(245,'countries','EN','Virgin Islands, US'),(246,'countries','EN','Wallis and Futuna'),(247,'countries','EN','Western Sahara'),(248,'countries','EN','Yemen'),(249,'countries','EN','Zambia'),(250,'countries','EN','Zimbabwe');
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;

-- -----------------------------
LOCK TABLES `users_change_mail` WRITE;
/*!40000 ALTER TABLE `users_change_mail` DISABLE KEYS */;
INSERT INTO `users_change_mail` VALUES (8,'c5513c14-b32f-35cc-83cf-85dcf499443f','b17646e6a793e9ce1bcafdf0688bc18a','2022-11-14 04:42:46','no-reply@dinaspain.com');
/*!40000 ALTER TABLE `users_change_mail` ENABLE KEYS */;
UNLOCK TABLES;

-- ------------------------------
LOCK TABLES `users_change_pass` WRITE;
/*!40000 ALTER TABLE `users_change_pass` DISABLE KEYS */;
INSERT INTO `users_change_pass` VALUES (13,'b22214ef-5d77-3d65-91df-9780528d65d2','928451e149682b1da8a0351e8799d297','2022-11-14 04:33:13'),(14,'b22214ef-5d77-3d65-91df-9780528d65d2','f0fad81832ae35d6f9508790291fed84','2022-11-14 04:36:31'),(15,'b22214ef-5d77-3d65-91df-9780528d65d2','c50e47f17f2e0abd90f7b92770f3346f','2022-11-14 04:38:14'),(16,'b22214ef-5d77-3d65-91df-9780528d65d2','bc9ea75002536c7adddfbdae9c76df91','2022-11-14 04:38:49'),(17,'b22214ef-5d77-3d65-91df-9780528d65d2','d58bfe4c6d57a87c49205cb47437d51c','2022-11-14 04:41:24'),(18,'b22214ef-5d77-3d65-91df-9780528d65d2','dd4a42bc45bc80a46243010fbfcdc6ef','2022-11-14 04:41:56');
/*!40000 ALTER TABLE `users_change_pass` ENABLE KEYS */;
UNLOCK TABLES;

-- ------------------------------
LOCK TABLES `permissions_by_feature` WRITE;
/*!40000 ALTER TABLE `permissions_by_feature` DISABLE KEYS */;
INSERT INTO `permissions_by_feature` VALUES ('alarms_create','allow'),('alarms_delete','allow'),('alarms_modify','allow'),('clientConfig_modify','allow'),('configScales','allow'),('del_in_other_clients','allow'),('module_alarms','allow'),('module_clientConfig','allow'),('module_myAccount','allow'),('module_products','allow'),('module_profiles','allow'),('module_systemScales','allow'),('module_users','allow'),('module_warehouses','allow'),('session_not_expired','allow'),('warehouses_create','allow'),('warehouses_delete','allow'),('warehouses_modify','allow'),('entity_users','none'),('alarms_create','notAllow'),('alarms_delete','notAllow'),('alarms_modify','notAllow'),('clientConfig_modify','notAllow'),('configScales','notAllow'),('del_in_other_clients','notAllow'),('module_alarms','notAllow'),('module_clientConfig','notAllow'),('module_myAccount','notAllow'),('module_products','notAllow'),('module_profiles','notAllow'),('module_systemScales','notAllow'),('module_users','notAllow'),('module_warehouses','notAllow'),('session_not_expired','notAllow'),('warehouses_create','notAllow'),('warehouses_delete','notAllow'),('warehouses_modify','notAllow'),('entity_users','read'),('entity_users','write');
/*!40000 ALTER TABLE `permissions_by_feature` ENABLE KEYS */;
UNLOCK TABLES;

-- -------------------------------
LOCK TABLES `permissions_by_profile` WRITE;
/*!40000 ALTER TABLE `permissions_by_profile` DISABLE KEYS */;
INSERT INTO `permissions_by_profile` VALUES (1,'alarms_create','allow'),(2,'alarms_create','allow'),(3,'alarms_create','allow'),(4,'alarms_create','notAllow'),(5,'alarms_create','notAllow'),(1,'alarms_delete','allow'),(2,'alarms_delete','allow'),(3,'alarms_delete','notAllow'),(4,'alarms_delete','notAllow'),(5,'alarms_delete','notAllow'),(1,'alarms_modify','allow'),(2,'alarms_modify','allow'),(3,'alarms_modify','notAllow'),(4,'alarms_modify','notAllow'),(5,'alarms_modify','notAllow'),(1,'clientConfig_modify','allow'),(2,'clientConfig_modify','allow'),(3,'clientConfig_modify','notAllow'),(4,'clientConfig_modify','notAllow'),(5,'clientConfig_modify','notAllow'),(1,'configScales','allow'),(2,'configScales','allow'),(3,'configScales','notAllow'),(4,'configScales','notAllow'),(5,'configScales','notAllow'),(1,'del_in_other_clients','allow'),(2,'del_in_other_clients','notAllow'),(3,'del_in_other_clients','notAllow'),(4,'del_in_other_clients','notAllow'),(5,'del_in_other_clients','notAllow'),(3,'entity_users','none'),(4,'entity_users','none'),(1,'entity_users','write'),(2,'entity_users','write'),(5,'entity_users','write'),(1,'module_alarms','allow'),(2,'module_alarms','allow'),(3,'module_alarms','notAllow'),(4,'module_alarms','notAllow'),(5,'module_alarms','notAllow'),(1,'module_clientConfig','allow'),(2,'module_clientConfig','allow'),(3,'module_clientConfig','notAllow'),(4,'module_clientConfig','notAllow'),(5,'module_clientConfig','notAllow'),(1,'module_myAccount','allow'),(2,'module_myAccount','allow'),(3,'module_myAccount','allow'),(4,'module_myAccount','allow'),(5,'module_myAccount','allow'),(1,'module_products','allow'),(2,'module_products','allow'),(3,'module_products','allow'),(4,'module_products','allow'),(5,'module_products','notAllow'),(1,'module_profiles','allow'),(2,'module_profiles','allow'),(3,'module_profiles','notAllow'),(4,'module_profiles','notAllow'),(5,'module_profiles','notAllow'),(1,'module_systemScales','allow'),(2,'module_systemScales','notAllow'),(3,'module_systemScales','notAllow'),(4,'module_systemScales','notAllow'),(5,'module_systemScales','notAllow'),(1,'module_users','allow'),(2,'module_users','allow'),(5,'module_users','allow'),(3,'module_users','notAllow'),(4,'module_users','notAllow'),(1,'module_warehouses','allow'),(2,'module_warehouses','allow'),(3,'module_warehouses','allow'),(4,'module_warehouses','allow'),(5,'module_warehouses','notAllow'),(1,'session_not_expired','allow'),(2,'session_not_expired','notAllow'),(3,'session_not_expired','notAllow'),(4,'session_not_expired','notAllow'),(5,'session_not_expired','notAllow'),(1,'warehouses_create','allow'),(2,'warehouses_create','allow'),(3,'warehouses_create','notAllow'),(4,'warehouses_create','notAllow'),(5,'warehouses_create','notAllow'),(1,'warehouses_delete','allow'),(2,'warehouses_delete','allow'),(3,'warehouses_delete','notAllow'),(4,'warehouses_delete','notAllow'),(5,'warehouses_delete','notAllow'),(1,'warehouses_modify','allow'),(2,'warehouses_modify','allow'),(3,'warehouses_modify','notAllow'),(4,'warehouses_modify','notAllow'),(5,'warehouses_modify','notAllow');
/*!40000 ALTER TABLE `permissions_by_profile` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------
LOCK TABLES `weight_units` WRITE;
/*!40000 ALTER TABLE `weight_units` DISABLE KEYS */;
INSERT INTO `weight_units` VALUES ('kg','Kilos',1,1),('lb','Libras',0.4536,2),('oz','Onzas',0.02835,3);
/*!40000 ALTER TABLE `weight_units` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;