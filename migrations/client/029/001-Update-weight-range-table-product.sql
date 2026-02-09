
CREATE TABLE products_backup AS SELECT * FROM products;

UPDATE products
SET weight_range = weight_range * 1000
WHERE weight_range IS NOT NULL;

ALTER TABLE products
MODIFY COLUMN weight_range DECIMAL(8,5) UNSIGNED DEFAULT NULL
COMMENT 'Umbral en GRAMOS para detectar cambio de peso';