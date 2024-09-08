ALTER TABLE `client`
    MODIFY COLUMN `business_group_id` INT(10) UNSIGNED DEFAULT NULL COMMENT 'ID del grupo empresarial al que pertenece el cliente'
        AFTER `uuid`;
ALTER TABLE `users`
    MODIFY COLUMN `client_id` INT(11) NOT NULL COMMENT 'ID del cliente al que pertenece el usuario'
        AFTER `uuid`;
