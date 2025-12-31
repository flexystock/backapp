CREATE TABLE IF NOT EXISTS `order_history` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Foreign Key
  `order_id` INT NOT NULL,
  
  -- Estados
  `status_from` ENUM('draft','pending','sent','confirmed','received','cancelled'),
  `status_to` ENUM('draft','pending','sent','confirmed','received','cancelled') NOT NULL,
  
  -- Auditoría
  `changed_by_user_id` INT,
  `notes` TEXT,
  
  -- Timestamp
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  -- Foreign Key
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  
  -- Índices
  INDEX `idx_order_id` (`order_id`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
