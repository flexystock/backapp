-- Añadir columna business_group_id a la tabla client
ALTER TABLE `client`
    ADD COLUMN `business_group_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'ID del grupo empresarial al que pertenece el cliente',
    ADD CONSTRAINT `fk_business_group` FOREIGN KEY (`business_group_id`) REFERENCES `business_group`(`id`);

-- Añadir columna client_id a la tabla users
ALTER TABLE `users`
    ADD COLUMN `client_id` INT(11) NOT NULL DEFAULT 1 COMMENT 'ID del cliente al que pertenece el usuario';

-- (Opcional) Si necesitas inicializar valores, puedes hacer esto:
-- UPDATE `users` SET `client_id` = [id_cliente_default] WHERE `client_id` IS NULL;

-- Añadir la clave foránea para client_id en la tabla users
ALTER TABLE `users`
    ADD CONSTRAINT `fk_client` FOREIGN KEY (`client_id`) REFERENCES `client`(`id`);


