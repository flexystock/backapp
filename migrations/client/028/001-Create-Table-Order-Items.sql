CREATE TABLE IF NOT EXISTS `order_items` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Foreign Keys
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  
  -- Cantidades
  `quantity` DECIMAL(10,2) NOT NULL,
  `unit` VARCHAR(20) DEFAULT 'kg',
  
  -- Precios
  `unit_price` DECIMAL(10,2),
  `subtotal` DECIMAL(10,2),
  
  -- Info adicional
  `notes` TEXT,
  `prediction_data` JSON,
  
  -- Timestamp
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Foreign Keys
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
  
  -- Índices
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
