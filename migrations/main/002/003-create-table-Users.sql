CREATE TABLE `users` (
  `uuid_user` CHAR(36) NOT NULL,
  `name` VARCHAR(50) NOT NULL COMMENT 'Nombre usuario',
  `surnames` VARCHAR(50) DEFAULT NULL COMMENT 'Apellidos usuario',
  `phone` int NOT NULL COMMENT 'telefono',
  `email` VARCHAR(255) NOT NULL COMMENT 'email',
  `password` VARCHAR(200) NOT NULL COMMENT 'Contraseña encriptada con md5',
  `is_root` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el usuario es root o no',
  `is_ghost` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si es un usuario fantasma. Cada cliente tiene uno, que genera automaticamente el propio back, para hacer peticiones sin que tenga nadie que logarse. Son peticiones internas del api',
  `minutes_session_expiration` int DEFAULT '60' COMMENT 'Minutos de expiración del login. Si null, no expira',
  `active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si el usuario esta o no activo',
  `failed_attempts` smallint NOT NULL DEFAULT '0' COMMENT 'Número de intentos fallidos de login',
  `locked_until` datetime DEFAULT NULL COMMENT 'Si se supera el número de intentos de login, se bloquea la cuenta hasta una fecha / hora',
  `last_access` datetime DEFAULT NULL COMMENT 'Fecha y hora del último intento de login',
  `uuid_user_creation` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT 'uuid del usuairo que la creo',
  `date_hour_creation` datetime NOT NULL COMMENT 'Fecha y hora de creación',
  `uuid_user_modification` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `date_hour_modification` datetime DEFAULT NULL COMMENT 'fecha y hora de la ultima modificación',
  `document_type` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `document_number` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `timezone` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `language` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `preferred_contact_method` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT '0',
  `security_question` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `security_answer` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `verification_token` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `verification_token_expires_at` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`uuid_user`),
  UNIQUE KEY `unique_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;