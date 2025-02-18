CREATE TABLE `alarm_types` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `type_name` VARCHAR(50) COLLATE utf8_bin NOT NULL, -- Ej: 'stock', 'horario', etc.
    `description` TEXT COLLATE utf8_bin DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

INSERT INTO `alarm_types` (`type_name`, `description`) VALUES
('stock', 'Alarma que se dispara cuando el stock está por debajo del umbral definido.'),
('horario', 'Alarma que se activa cuando la acción ocurre fuera del horario permitido.');
