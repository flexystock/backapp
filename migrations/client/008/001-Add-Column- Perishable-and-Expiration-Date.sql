ALTER TABLE products
ADD COLUMN expiration_date DATETIME DEFAULT NULL COMMENT 'Fecha de caducidad de un producto' AFTER ean;

ALTER TABLE products
ADD COLUMN perishable BOOLEAN NOT NULL DEFAULT false COMMENT 'Producto perecedero' AFTER expiration_date;