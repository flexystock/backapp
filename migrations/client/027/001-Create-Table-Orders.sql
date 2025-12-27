CREATE TABLE IF NOT EXISTS `orders` (
  -- Primary Key
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  
  -- Identificación
  `order_number` VARCHAR(50) UNIQUE NOT NULL,
  
  -- Foreign Key
  `client_supplier_id` INT NOT NULL,
  
  -- Estado
  `status` ENUM('draft','pending','sent','confirmed','received','cancelled') DEFAULT 'draft',
  
  -- Montos
  `total_amount` DECIMAL(10,2) DEFAULT 0.00,
  `currency` VARCHAR(3) DEFAULT 'EUR',
  
  -- Fechas
  `delivery_date` DATE,
  `sent_at` TIMESTAMP NULL,
  `confirmed_at` TIMESTAMP NULL,
  `received_at` TIMESTAMP NULL,
  `cancelled_at` TIMESTAMP NULL,
  
  -- Comunicación
  `email_sent_to` VARCHAR(255),
  `pdf_path` VARCHAR(500),
  
  -- Notas
  `notes` TEXT,
  `cancellation_reason` TEXT,
  
  -- Auditoría
  `created_by_user_id` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Foreign Key
  FOREIGN KEY (`client_supplier_id`) REFERENCES `client_suppliers`(`id`),
  
  -- Índices
  INDEX `idx_status` (`status`),
  INDEX `idx_delivery_date` (`delivery_date`),
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_supplier_status` (`client_supplier_id`, `status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
