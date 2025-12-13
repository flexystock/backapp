CREATE TABLE IF NOT EXISTS `product_suppliers` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Foreign Keys
  `product_id` INT NOT NULL,
  `client_supplier_id` INT NOT NULL,
  
  -- Configuración
  `is_preferred` BOOLEAN DEFAULT FALSE,
  `product_code` VARCHAR(100),
  `unit_price` DECIMAL(10,2),
  `min_order_quantity` DECIMAL(10,2),
  `delivery_days` INT,
  `notes` TEXT,
  
  -- Timestamps
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign Keys
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_supplier_id`) REFERENCES `client_suppliers`(`id`) ON DELETE CASCADE,
  
  -- Índices
  UNIQUE KEY `unique_product_supplier` (`product_id`, `client_supplier_id`),
  INDEX `idx_is_preferred` (`is_preferred`),
  INDEX `idx_product_id` (`product_id`),
  INDEX `idx_client_supplier_id` (`client_supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
