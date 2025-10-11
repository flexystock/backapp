CREATE TABLE `log_mail` (
  `id` INT AUTO_INCREMENT PRIMARY KEY COMMENT 'Identificador único del registro',
  `recipient` VARCHAR(255) NOT NULL COMMENT 'Correo electrónico del destinatario',
  `subject` VARCHAR(255) NOT NULL COMMENT 'Asunto del correo',
  `body` TEXT DEFAULT NULL COMMENT 'Cuerpo del correo (opcional)',
  `status` VARCHAR(50) NOT NULL COMMENT 'Estado del envío (success,failure)',
  `error_message` TEXT DEFAULT NULL COMMENT 'Mensaje de error en caso de fallo (opcional)',
  `sent_at` DATETIME NOT NULL COMMENT 'Fecha y hora del envío',
  `additional_data` JSON DEFAULT NULL COMMENT 'Datos adicionales en formato JSON (opcional)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;