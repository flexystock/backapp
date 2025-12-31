-- Agregar columnas de auto-pedido a la tabla products
ALTER TABLE `products` 
ADD COLUMN `auto_order_enabled` BOOLEAN DEFAULT TRUE,
ADD COLUMN `auto_order_threshold` ENUM('critical', 'high', 'medium') DEFAULT 'critical',
ADD COLUMN `auto_order_quantity_days` INT DEFAULT 14;

-- Índice
CREATE INDEX `idx_auto_order_enabled` ON `products`(`auto_order_enabled`);
