ALTER TABLE products
ADD COLUMN stock DECIMAL(5,2) NOT NULL DEFAULT '0' COMMENT 'Stock de un producto' AFTER ean;