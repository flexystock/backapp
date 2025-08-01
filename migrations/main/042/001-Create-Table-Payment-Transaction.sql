CREATE TABLE `payment_transaction` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `subscription_uuid` CHAR(36) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `currency` VARCHAR(10) NOT NULL,
    `status` VARCHAR(20) NOT NULL,
    `gateway` VARCHAR(50) NOT NULL,            -- ej. Stripe, PayPal…
    `transaction_reference` VARCHAR(100) NULL, -- ID devuelto por la pasarela
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payment_subscription`
        FOREIGN KEY (`subscription_uuid`) REFERENCES `subscription`(`uuid_subscription`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;