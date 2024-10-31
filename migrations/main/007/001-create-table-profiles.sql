CREATE TABLE `profiles` (
                            `id` INT(11) NOT NULL AUTO_INCREMENT,
                            `name` VARCHAR(100) NOT NULL COMMENT 'Nombre del perfil',
                            `description` TEXT DEFAULT NULL COMMENT 'Descripción del perfil',
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla de perfiles';